from typing import Any, Text, Dict, List
from rasa_sdk import Action, Tracker
from rasa_sdk.executor import CollectingDispatcher
import json
import urllib.request
import urllib.error
import os

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
        
        try:
            req = urllib.request.Request(
                lm_studio_url, 
                data=json.dumps(payload).encode('utf-8'),
                headers={
                    'Content-Type': 'application/json',
                    'Authorization': f'Bearer {api_key}'
                },
                method='POST'
            )
            with urllib.request.urlopen(req, timeout=30) as response:
                result = json.loads(response.read().decode('utf-8'))
                llm_reply = result['choices'][0]['message']['content']
                dispatcher.utter_message(text=llm_reply)
            
        except urllib.error.HTTPError as e:
            error_body = e.read().decode('utf-8')
            dispatcher.utter_message(text=f"Erro 400 da IA local. Detalhes: {error_body}")
        except Exception as e:
            dispatcher.utter_message(text=f"Desculpe, não consegui conectar à IA local. Erro: {e}")

        return []
