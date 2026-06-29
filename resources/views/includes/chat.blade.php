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
        bottom: 100px;
        right: 30px;
        width: 350px;
        height: 450px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        display: none;
        flex-direction: column;
        z-index: 9999;
        overflow: hidden;
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
</style>

<!-- Botão Flutuante -->
<div class="chat-widget-btn" id="chatWidgetBtn" title="Fale com a Iúna sobre este documento">
    <i class="fa fa-comments"></i>
</div>

<!-- Janela do Chat -->
<div class="chat-window" id="chatWindow">
    <div class="chat-header">
        <span><i class="fa fa-robot"></i> Iúna</span>
        <span class="close-btn" id="chatCloseBtn"><i class="fa fa-times"></i></span>
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
    const chatSendBtn = document.getElementById('chatSendBtn');
    const chatInput = document.getElementById('chatInput');
    const chatBody = document.getElementById('chatBody');

    const documentId = "{{ $document_id ?? '' }}";
    const isNormativa = {{ isset($is_normativa) && $is_normativa ? 'true' : 'false' }};

    chatBtn.addEventListener('click', () => {
        chatWindow.style.display = 'flex';
        chatBtn.style.display = 'none';
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
        msgDiv.textContent = text;
        chatBody.appendChild(msgDiv);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    function sendMessage() {
        const text = chatInput.value.trim();
        if (!text) return;

        appendMessage(text, 'user');
        chatInput.value = '';

        // Mostra indicador de digitando
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message bot typing';
        typingDiv.innerHTML = '<em>Digitando...</em>';
        chatBody.appendChild(typingDiv);
        chatBody.scrollTop = chatBody.scrollHeight;

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
            chatBody.removeChild(typingDiv);
            if (data.reply) {
                appendMessage(data.reply, 'bot');
            }
        })
        .catch(error => {
            chatBody.removeChild(typingDiv);
            appendMessage('Desculpe, ocorreu um erro de conexão com o servidor.', 'bot');
            console.error('Error:', error);
        });
    }
});
</script>
