// Chatbot simplifié et fonctionnel
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Chatbot initializing...');
    
    // Éléments
    const chatbotButton = document.getElementById("chatbot-button");
    const chatbotBox = document.getElementById("chatbot-box");
    const chatbotClose = document.getElementById("chatbot-close");
    const chatbotInput = document.getElementById("chatbot-input");
    const chatbotSend = document.getElementById("chatbot-send");
    const chatbotMessages = document.getElementById("chatbot-messages");

    // Vérification
    if (!chatbotButton || !chatbotBox || !chatbotClose || !chatbotInput || !chatbotSend || !chatbotMessages) {
        console.error('❌ Missing chatbot elements');
        return;
    }

    console.log('✅ All elements found');

    // === OUVERTURE/FERMETURE ===
    chatbotButton.onclick = function() {
        console.log('🎯 Chatbot button clicked');
        chatbotBox.style.display = chatbotBox.style.display === "flex" ? "none" : "flex";
        if (chatbotBox.style.display === "flex") {
            setTimeout(() => chatbotInput.focus(), 100);
        }
    };

    chatbotClose.onclick = function() {
        console.log('❌ Close button clicked');
        chatbotBox.style.display = "none";
    };

    // === RÉPONSES DU BOT ===
    function getBotReply(userMessage) {
        const message = userMessage.toLowerCase().trim();
        
        const replies = {
            'bonjour': 'Salut ! Comment puis-je t\'aider aujourd\'hui ? 😊',
            'salut': 'Salut ! Prêt pour de nouvelles missions ? 🎮',
            'hello': 'Hello ! Comment vas-tu ? 🎯',
            'coucou': 'Coucou gamer ! 👾',
            'mission': 'Pour voir les missions disponibles, navigue dans la section "Missions" !',
            'missions': 'Toutes nos missions sont listées sur cette page !',
            'postuler': 'Pour postuler : 1. Choisis une mission 🎯 2. Clique sur "Voir la Mission" 👀',
            'aide': 'Je peux t\'aider avec : missions, postulation, difficultés, récompenses.',
            'help': 'I can help with: missions, applications, difficulties, rewards.',
            'default': 'Désolé, je n\'ai pas compris. Essaie avec : "missions", "postuler", "aide" 😊'
        };

        for (const [key, value] of Object.entries(replies)) {
            if (message.includes(key) && key !== 'default') {
                return value;
            }
        }
        return replies.default;
    }

    // === AJOUTER MESSAGE ===
    function addMessage(text, isUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message ${isUser ? 'user-message' : 'bot-message'}`;
        messageDiv.textContent = text;
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // === ENVOYER MESSAGE - FONCTION PRINCIPALE ===
    function sendMessage() {
        const text = chatbotInput.value.trim();
        console.log('📤 Sending message:', text);
        
        if (!text) {
            console.log('⚠️ Empty message, ignoring');
            return;
        }

        // Message utilisateur
        addMessage(text, true);
        chatbotInput.value = '';

        // Réponse bot
        setTimeout(() => {
            const reply = getBotReply(text);
            console.log('🤖 Bot reply:', reply);
            addMessage(reply, false);
        }, 500);
    }

    // === ÉVÉNEMENTS D'ENVOI - VERSION CORRIGÉE ===
    
    // 1. Bouton d'envoi - méthode directe
    chatbotSend.onclick = function() {
        console.log('🖱️ Send BUTTON clicked');
        sendMessage();
    };

    // 2. Touche Entrée - VERSION CORRIGÉE (onkeydown au lieu de onkeypress)
    chatbotInput.onkeydown = function(e) {
        if (e.key === 'Enter') {
            console.log('⌨️ Enter key pressed');
            sendMessage();
            e.preventDefault(); // Empêche le comportement par défaut
        }
    };

    // 3. Fermeture en cliquant dehors
    document.addEventListener('click', function(e) {
        if (!chatbotBox.contains(e.target) && e.target !== chatbotButton) {
            chatbotBox.style.display = "none";
        }
    });

    console.log('🚀 Chatbot ready! Both button and enter should work');
});