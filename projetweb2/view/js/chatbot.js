// ouvrir / fermer chatbot
let button = document.getElementById("chatbot-button");
let box = document.getElementById("chatbot-box");

button.onclick = () => {
  box.style.display = box.style.display === "flex" ? "none" : "flex";
};

// générer la réponse du bot
function botReply(text) {
  const replies = {
    bonjour: "Salut ! Comment puis-je t'aider ? 😊",
    coucou: "Coucou gamer 👾",
    mission:
      "Pour voir les missions disponibles, clique sur 'Liste des missions' dans la page d’accueil.",
    "comment postuler":
      "Pour postuler, clique sur une mission puis sur 'Postuler maintenant'.",
    postuler:
      "Clique d'abord sur une mission puis sur 'Postuler maintenant'. 🎮",
    help: "Je suis là pour t'aider ! Essaie : mission, postuler, comment ça marche…",
    merci: "Avec plaisir ! ❤️",
  };

  let msg =
    replies[text.toLowerCase()] ||
    "Je comprends pas bien... essaie avec : 'mission', 'postuler', 'help' 😉";

  return msg;
}

// envoyer un message
let input = document.getElementById("chatbot-input");
let messages = document.getElementById("chatbot-messages");

input.addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    let text = input.value.trim();
    if (text === "") return;

    // ajouter message utilisateur
    messages.innerHTML += `<div class='chat-msg'><b>Toi :</b> ${text}</div>`;

    // bot répond
    let response = botReply(text);
    messages.innerHTML += `<div class='chat-msg'><b>Bot :</b> ${response}</div>`;

    input.value = "";
    messages.scrollTop = messages.scrollHeight;
  }
});
