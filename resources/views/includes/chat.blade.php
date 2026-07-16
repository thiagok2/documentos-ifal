<!-- Importando biblioteca Marked.js para interpretar o Markdown do LLM -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<style>
    /* Estilos do Chat Flutuante */
    .chat-widget-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #19882c;
        color: white;
        text-align: center;
        line-height: 60px;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        z-index: 9999;
        transition: transform 0.3s;
    }
    .chat-widget-btn:hover {
        transform: scale(1.1);
    }
    .chat-window {
        position: fixed;
        bottom: 0;
        right: 30px;
        width: 450px;
        height: 75vh;
        max-height: 800px;
        background-color: white;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 0 20px rgba(0,0,0,0.2);
        display: none;
        flex-direction: column;
        z-index: 9999;
        overflow: hidden;
    }
    @media (max-width: 768px) {
        .chat-window {
            width: 100%;
            height: 100vh;
            right: 0;
            border-radius: 0;
            max-height: 100%;
        }
    }
    .chat-header {
        background-color: #19882c;
        color: white;
        padding: 15px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-header .close-btn {
        cursor: pointer;
        font-size: 18px;
    }
    .chat-body {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background-color: #f9f9f9;
        display: flex;
        flex-direction: column;
    }
    .chat-message {
        margin-bottom: 15px;
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 15px;
        font-size: 14px;
        line-height: 1.4;
        word-wrap: break-word;
    }
    .chat-message strong, .chat-message b {
        font-weight: bold !important;
    }
    .chat-message ul {
        padding-left: 20px !important;
        list-style-type: disc !important;
        margin-top: 5px;
        margin-bottom: 5px;
    }
    .chat-message ol {
        padding-left: 20px !important;
        list-style-type: decimal !important;
        margin-top: 5px;
        margin-bottom: 5px;
    }
    .chat-message p {
        margin-bottom: 8px;
    }
    .chat-message p:last-child {
        margin-bottom: 0;
    }
    .chat-message.bot {
        background-color: #e9ecef;
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 0;
    }
    .chat-message.user {
        background-color: #19882c;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 0;
    }
    .chat-footer {
        padding: 10px;
        border-top: 1px solid #ddd;
        background: white;
        display: flex;
    }
    .chat-footer input {
        flex: 1;
        border: 1px solid #ccc;
        border-radius: 20px;
        padding: 8px 15px;
        outline: none;
    }
    .chat-footer button {
        background-color: #19882c;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        margin-left: 10px;
        cursor: pointer;
    }
    
    /* Move o VLibras mais para cima para não sobrepor o botão do Chat */
    .vw-plugin-wrapper, [vw] {
        bottom: 110px !important;
    }

    /* Animação de Loading (3 pontinhos) aguardando resposta */
    .typing-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px;
    }
    .typing-indicator span {
        width: 8px;
        height: 8px;
        background-color: #888;
        border-radius: 50%;
        animation: typing 1.4s infinite ease-in-out both;
    }
    .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
    .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
    @keyframes typing {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
</style>

<!-- Botão Flutuante -->
<div class="chat-widget-btn" id="chatWidgetBtn" title="Fale com a Iúna sobre este documento">
    <i class="fa fa-comments"></i>
</div>

<!-- Janela do Chat -->
<div class="chat-window" id="chatWindow">
    <div class="chat-header">
        <span><i class="fa fa-robot"></i> Iúna</span>
        <div>
            <span class="action-btn" id="chatClearBtn" title="Limpar Conversa" style="margin-right: 15px; cursor: pointer; font-size: 16px;"><i class="fa fa-trash"></i></span>
            <span class="action-btn" id="chatCloseBtn" title="Fechar Chat" style="cursor: pointer; font-size: 18px;"><i class="fa fa-times"></i></span>
        </div>
    </div>
    <div class="chat-body" id="chatBody">
        <div class="chat-message bot">
            Olá! Eu sou a Iúna. Você tem alguma dúvida sobre este documento?
        </div>
    </div>
    <div class="chat-footer">
        <input type="text" id="chatInput" placeholder="Digite sua dúvida..." autocomplete="off" />
        <button id="chatSendBtn"><i class="fa fa-paper-plane"></i></button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatBtn = document.getElementById('chatWidgetBtn');
    const chatWindow = document.getElementById('chatWindow');
    const chatCloseBtn = document.getElementById('chatCloseBtn');
    const chatClearBtn = document.getElementById('chatClearBtn');
    const chatSendBtn = document.getElementById('chatSendBtn');
    const chatInput = document.getElementById('chatInput');
    const chatBody = document.getElementById('chatBody');

    const documentId = "{{ $document_id ?? '' }}";
    const isNormativa = {{ isset($is_normativa) && $is_normativa ? 'true' : 'false' }};

    function saveChatHistory() {
        localStorage.setItem('chat_history_' + documentId, chatBody.innerHTML);
    }

    function loadChatHistory() {
        const saved = localStorage.getItem('chat_history_' + documentId);
        if (saved) {
            chatBody.innerHTML = saved;
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    }

    // Carrega o histórico ao abrir a página
    loadChatHistory();

    chatClearBtn.addEventListener('click', () => {
        if(confirm("Tem certeza que deseja limpar o histórico desta conversa?")) {
            localStorage.removeItem('chat_history_' + documentId);
            chatBody.innerHTML = '<div class="chat-message bot">Olá! Eu sou a Iúna. Você tem alguma dúvida sobre este documento? (Histórico limpo)</div>';
            
            // Envia o comando /restart oculto para limpar a memória do Rasa
            fetch('{{ route("chat-send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: '/restart',
                    document_id: documentId,
                    is_normativa: isNormativa
                })
            });
        }
    });

    chatBtn.addEventListener('click', () => {
        chatWindow.style.display = 'flex';
        chatBtn.style.display = 'none';
        chatBody.scrollTop = chatBody.scrollHeight; // Garante rolagem no fim ao abrir
    });

    chatCloseBtn.addEventListener('click', () => {
        chatWindow.style.display = 'none';
        chatBtn.style.display = 'block';
    });

    chatInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    chatSendBtn.addEventListener('click', sendMessage);

    function appendMessage(text, sender) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'chat-message ' + sender;
        
        if (sender === 'bot') {
            chatBody.appendChild(msgDiv);
            
            let i = 0;
            let currentText = "";
            
            // Efeito de digitação (simulated streaming)
            let interval = setInterval(() => {
                currentText += text.charAt(i);
                
                if (typeof marked !== 'undefined') {
                    msgDiv.innerHTML = marked.parse(currentText);
                } else {
                    let formattedText = currentText.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                    msgDiv.innerHTML = formattedText.replace(/\n/g, '<br>');
                }
                
                // Remove margin-bottom do último paragrafo gerado pelo marked para não quebrar o balão
                const lastP = msgDiv.querySelector('p:last-child');
                if(lastP) lastP.style.marginBottom = '0';
                
                chatBody.scrollTop = chatBody.scrollHeight;
                
                i++;
                if (i >= text.length) {
                    clearInterval(interval);
                    // Salva o histórico apenas quando a digitação terminar
                    saveChatHistory();
                }
            }, 10); // Velocidade: 10ms por caractere
        } else {
            msgDiv.textContent = text;
            chatBody.appendChild(msgDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
            saveChatHistory();
        }
    }

    function sendMessage() {
        const text = chatInput.value.trim();
        if (!text) return;

        appendMessage(text, 'user');
        chatInput.value = '';

        // Mostra indicador animado de loading (3 pontinhos) enquanto aguarda o servidor
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message bot typing';
        typingDiv.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
        chatBody.appendChild(typingDiv);
        chatBody.scrollTop = chatBody.scrollHeight;

        // Se demorar mais de 5 segundos, avisa ao usuário que a IA está analisando (mascarando o JIT load)
        const loadingTimeout = setTimeout(() => {
            const mensagens = [
                "Estou analisando os documentos a fundo, isso pode levar alguns instantes... <div class='typing-indicator' style='margin-top: 8px;'><span></span><span></span><span></span></div>",
                "Consultando o acervo para encontrar a resposta mais precisa. Aguarde um momento... <div class='typing-indicator' style='margin-top: 8px;'><span></span><span></span><span></span></div>",
                "Processando as informações detalhadas do documento... <div class='typing-indicator' style='margin-top: 8px;'><span></span><span></span><span></span></div>"
            ];
            typingDiv.innerHTML = mensagens[Math.floor(Math.random() * mensagens.length)];
            chatBody.scrollTop = chatBody.scrollHeight;
        }, 5000);

        fetch('{{ route("chat-send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: text,
                document_id: documentId,
                is_normativa: isNormativa
            })
        })
        .then(response => response.json())
        .then(data => {
            clearTimeout(loadingTimeout);
            chatBody.removeChild(typingDiv);
            if (data.reply) {
                appendMessage(data.reply, 'bot');
            }
        })
        .catch(error => {
            clearTimeout(loadingTimeout);
            chatBody.removeChild(typingDiv);
            appendMessage('Desculpe, ocorreu um erro de conexão com o servidor.', 'bot');
            console.error('Error:', error);
        });
    }
});
</script>
