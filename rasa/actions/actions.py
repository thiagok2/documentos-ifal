from typing import Any, Text, Dict, List
from rasa_sdk import Action, Tracker
from rasa_sdk.executor import CollectingDispatcher
import json
import urllib.request
import urllib.error
import os
import threading
import random

unload_timer = None

def unload_model(model_id, api_key, base_url):
    try:
        # A API nativa do LM Studio usa /api/v1/models/unload
        unload_url = base_url.replace("/v1/chat/completions", "/api/v1/models/unload")
        print(f"[Timer] Disparando unload em: {unload_url} para o modelo {model_id}")
        req = urllib.request.Request(
            unload_url,
            data=json.dumps({"instance_id": model_id}).encode('utf-8'),
            headers={'Authorization': f'Bearer {api_key}', 'Content-Type': 'application/json'},
            method='POST'
        )
        response = urllib.request.urlopen(req, timeout=10)
        print(f"[Timer] Resposta do LM Studio: {response.status}")
        print(f"[Timer] Modelo {model_id} descarregado com sucesso por inatividade.")
    except Exception as e:
        print(f"[Timer] Falha ao descarregar modelo {model_id}: {e}")

def schedule_unload(model_id, api_key, base_url):
    global unload_timer
    if unload_timer is not None:
        print("Cancelando timer anterior...")
        unload_timer.cancel()
    
    # 5 minutos para inatividade
    delay = 300.0
    print(f"Agendando desligamento do modelo {model_id} em {delay} segundos (5 minutos)...")
    unload_timer = threading.Timer(delay, unload_model, args=[model_id, api_key, base_url])
    unload_timer.daemon = True 
    unload_timer.start()

class ActionAskLLM(Action):

    def name(self) -> Text:
        return "action_ask_llm"

    def run(self, dispatcher: CollectingDispatcher,
            tracker: Tracker,
            domain: Dict[Text, Any]) -> List[Dict[Text, Any]]:

        # Pegamos a última mensagem enviada pelo usuário
        user_message = tracker.latest_message.get('text')
        
        # Pegando as intenções para debugar caso precise
        intent = tracker.latest_message.get('intent', {}).get('name')

        # Extraindo metadados enviados pelo Laravel
        metadata = tracker.latest_message.get('metadata', {})
        document_context = metadata.get('document_context', 'Nenhum contexto adicional fornecido.')

        lm_studio_url = "http://host.docker.internal:1234/v1/chat/completions"

        prompt_system = (
            f"Contexto do documento atual:\n{document_context}\n\n"
            "Você é Iúna, uma assistente de IA prestativa e analítica. Você deve priorizar responder perguntas com base nas informações do documento acima.\n"
            "Sempre responda em Português do Brasil (PT-BR).\n\n"
            "REGRAS DE MATEMÁTICA E COMPARAÇÃO:\n"
            "- Preços no Brasil usam ponto para milhares e vírgula para centavos.\n"
            "- Exemplo obrigatório: '3.055,15' significa TRÊS MIL e cinquenta e cinco reais e quinze centavos. '3.856' significa TRÊS MIL oitocentos e cinquenta e seis reais. Portanto, 3.055,15 é MENOR que 3.856.\n"
            "- Ao buscar o 'mais caro' ou 'mais barato', analise toda a lista do documento. Se houver itens empatados no valor máximo ou mínimo, liste TODOS os que empataram."
        )

        messages = [
            {
                "role": "system",
                "content": prompt_system
            }
        ]

        # Pega os últimos 12 eventos de fala (6 interações de ida e volta)
        recent_events = [e for e in tracker.events if e.get('event') in ['user', 'bot']][-12:]
        
        for event in recent_events:
            text = event.get('text')
            if not text:
                continue
                
            if event.get('event') == 'user':
                messages.append({"role": "user", "content": text})
            elif event.get('event') == 'bot':
                messages.append({"role": "assistant", "content": text})

        payload = {
            "model": "local-model",
            "messages": messages,
            "temperature": 0.1,
            "max_tokens": 4096,
            "stream": False
        }

        api_key = os.environ.get('LM_STUDIO_API_KEY', 'CHAVE_FALTANDO')
        
        def make_request(model_name):
            payload["model"] = model_name
            req = urllib.request.Request(
                lm_studio_url, 
                data=json.dumps(payload).encode('utf-8'),
                headers={
                    'Content-Type': 'application/json',
                    'Authorization': f'Bearer {api_key}'
                },
                method='POST'
            )
            # Aumentamos o timeout no caso de JIT load, pois carregar o modelo pode demorar
            with urllib.request.urlopen(req, timeout=120) as response:
                result = json.loads(response.read().decode('utf-8'))
                
                # Reseta o timer de inatividade sempre que houver sucesso
                actual_model_id = result.get('model', model_name)
                schedule_unload(actual_model_id, api_key, lm_studio_url)
                
                return result['choices'][0]['message']['content']

        try:
            llm_reply = make_request("local-model")
            dispatcher.utter_message(text=llm_reply)
            
        except urllib.error.HTTPError as e:
            error_body = e.read().decode('utf-8')
            if "No models loaded" in error_body or "invalid_request_error" in error_body:
                try:
                    # Busca modelos disponíveis na API do LM Studio
                    models_url = lm_studio_url.replace("/chat/completions", "/models")
                    req_models = urllib.request.Request(
                        models_url,
                        headers={'Authorization': f'Bearer {api_key}'}
                    )
                    with urllib.request.urlopen(req_models, timeout=10) as response:
                        models_data = json.loads(response.read().decode('utf-8'))
                        # Filtra embeddings
                        available_models = [m['id'] for m in models_data.get('data', []) if 'embed' not in m['id'].lower()]
                        
                        if available_models:
                            first_model = available_models[0]
                            # Não enviamos a mensagem como balão permanente para não poluir o histórico,
                            # pois o frontend vai exibir a mensagem no próprio indicador de 'typing' se demorar.
                            # Mas continuamos mandando o log.
                            print(f"Iniciando JIT load do modelo {first_model}...")
                            
                            llm_reply = make_request(first_model)
                            dispatcher.utter_message(text=llm_reply)
                        else:
                            dispatcher.utter_message(text=f"Erro: Nenhum modelo de texto encontrado no LM Studio. Detalhes: {error_body}")
                except urllib.error.HTTPError as e_retry:
                    error_retry_body = e_retry.read().decode('utf-8')
                    dispatcher.utter_message(text=f"Erro ao tentar carregar o modelo. Detalhes: {error_retry_body}")
                except Exception as e_retry:
                    dispatcher.utter_message(text=f"Falha ao auto-carregar modelo: {e_retry}")
            else:
                dispatcher.utter_message(text=f"Erro 400 da IA local. Detalhes: {error_body}")
        except Exception as e:
            dispatcher.utter_message(text=f"Desculpe, não consegui conectar à IA local. Erro: {e}")

        return []
