document.addEventListener('DOMContentLoaded', () => {
    // Configuration
    const CONFIG = {
        webhookUrl: 'http://192.168.1.149:5678/webhook-test/12bc059b-b353-410d-ba41-51e42cbc462b',
        botName: 'Asistente Virtual',
        welcomeMessage: '¡Hola! ¿En qué puedo ayudarte hoy?',
        errorMessage: 'Lo siento, hubo un problema al conectar con el servidor.'
    };

    // Inject HTML Structure
    const widgetHTML = `
        <button class="chat-widget-toggle" aria-label="Abrir chat">
            <svg class="icon-chat" viewBox="0 0 24 24">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
            </svg>
            <svg class="icon-close" viewBox="0 0 24 24">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
        </button>

        <div class="chat-widget-container">
            <div class="chat-header">
                <div class="chat-header-avatar">
                    <svg viewBox="0 0 24 24" fill="white" width="20" height="20">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    </svg>
                </div>
                <div class="chat-header-info">
                    <h3>${CONFIG.botName}</h3>
                    <span>En línea</span>
                </div>
                <button class="chat-header-close" aria-label="Cerrar chat">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <div class="message bot">
                    ${CONFIG.welcomeMessage}
                </div>
            </div>

            <div class="chat-input-area">
                <input type="text" class="chat-input" id="chatInput" placeholder="Escribe tu mensaje..." autocomplete="off">
                <button class="chat-send-btn" id="chatSendBtn" aria-label="Enviar mensaje">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', widgetHTML);

    // Elements
    const toggleBtn = document.querySelector('.chat-widget-toggle');
    const container = document.querySelector('.chat-widget-container');
    const messagesContainer = document.getElementById('chatMessages');
    const input = document.getElementById('chatInput');
    const sendBtn = document.getElementById('chatSendBtn');
    const closeBtn = document.querySelector('.chat-header-close');

    // Toggle Chat
    toggleBtn.addEventListener('click', () => {
        const isOpen = container.classList.contains('open');
        if (isOpen) {
            closeChat();
        } else {
            openChat();
        }
    });

    closeBtn.addEventListener('click', closeChat);

    function openChat() {
        container.classList.add('open');
        toggleBtn.classList.add('open');
        input.focus();
    }

    function closeChat() {
        container.classList.remove('open');
        toggleBtn.classList.remove('open');
    }

    // Send Message
    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        // Add User Message
        addMessage(text, 'user');
        input.value = '';
        input.disabled = true;
        sendBtn.disabled = true;

        // Add Loading Indicator
        const loadingId = addLoadingIndicator();

        try {
            const response = await fetch(CONFIG.webhookUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: text }) // Format requested by user
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const data = await response.json();

            // Remove loading and add bot response
            removeMessage(loadingId);

            // Handle expected response format { "response": "..." }
            const botResponse = data.response || data.output || data.text || JSON.stringify(data);
            addMessage(botResponse, 'bot');

        } catch (error) {
            console.error('Error:', error);
            removeMessage(loadingId);
            addMessage(CONFIG.errorMessage, 'bot');
        } finally {
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
        }
    }

    // Event Listeners
    sendBtn.addEventListener('click', sendMessage);

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Helpers
    function addMessage(text, sender) {
        const div = document.createElement('div');
        div.className = `message ${sender}`;
        div.textContent = text;
        messagesContainer.appendChild(div);
        scrollToBottom();
        return div;
    }

    function addLoadingIndicator() {
        const id = 'loading-' + Date.now();
        const div = document.createElement('div');
        div.className = 'typing-indicator';
        div.id = id;
        div.innerHTML = `
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        `;
        messagesContainer.appendChild(div);
        scrollToBottom();
        return id;
    }

    function removeMessage(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
