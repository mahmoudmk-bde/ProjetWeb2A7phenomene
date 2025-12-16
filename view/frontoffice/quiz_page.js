// Variables globales
let currentQuestion = 1;
let totalQuestions = 0;
let userAnswers = {};
let quizStarted = false;
let formSubmitted = false;

// Variables pour le timer de question
let questionTimer;
let questionTimeLeft = {}; // Stocke le temps restant par question
let questionTotalTime = 30; // Temps total par question

// Fonction pour afficher une notification
function showNotification(message, type) {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        animation: slideInRight 0.3s ease;
    `;

    // Ajouter à la page
    document.body.appendChild(notification);

    // Supprimer après 5 secondes
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Fonction pour afficher une erreur
function showError(message) {
    showNotification(message, 'danger');
}

// Fonction pour configurer les écouteurs d'événements
function setupEventListeners() {
    // Écouteurs pour les boutons de navigation
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('quizForm');

    if (prevBtn) {
        prevBtn.addEventListener('click', goToPreviousQuestion);
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', goToNextQuestion);
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', function (e) {
            e.preventDefault();
            validateBeforeSubmit(e);
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            validateBeforeSubmit(e);
        });
    }

    // Écouteurs pour les options de réponse
    const optionInputs = document.querySelectorAll('.option-input');
    optionInputs.forEach(input => {
        input.addEventListener('change', function () {
            const questionId = parseInt(this.dataset.question);
            const answerValue = parseInt(this.value);

            // Enregistrer la réponse
            userAnswers[questionId] = answerValue;

            // Mettre à jour le style de la réponse sélectionnée
            updateAnswerStyles(questionId, answerValue);

            // Mettre à jour la progression et les boutons
            updateProgress();
            updateButtons();

            // Réinitialiser le timer de la question
            resetQuestionTimer();
        });
    });
}

// Fonction pour mettre à jour les styles des réponses
function updateAnswerStyles(questionId, answerValue) {
    // Trouver la carte de question
    const questionCard = document.getElementById(`question-${questionId}`);
    if (!questionCard) return;

    // Réinitialiser tous les styles
    const allOptions = questionCard.querySelectorAll('.option-label');
    allOptions.forEach(option => {
        option.classList.remove('selected');
    });

    // Mettre en surbrillance l'option sélectionnée
    const selectedOption = questionCard.querySelector(`input[value="${answerValue}"]`);
    if (selectedOption && selectedOption.parentElement) {
        selectedOption.parentElement.classList.add('selected');
    }
}

// Fonction pour afficher une question spécifique
function showQuestion(questionNumber) {
    // Arrêter le timer actuel si il existe
    if (questionTimer) {
        clearInterval(questionTimer);
    }

    // Masquer toutes les questions
    const allQuestions = document.querySelectorAll('.question-card');
    allQuestions.forEach(question => {
        question.style.display = 'none';
        question.classList.remove('active');
    });

    // Afficher la question demandée
    const questionToShow = document.getElementById(`question-${questionNumber}`);
    if (questionToShow) {
        questionToShow.style.display = 'block';
        questionToShow.classList.add('active');

        // Mettre à jour la question courante
        currentQuestion = questionNumber;

        // Initialiser le temps pour cette question si non défini
        if (questionTimeLeft[currentQuestion] === undefined) {
            questionTimeLeft[currentQuestion] = questionTotalTime;
        }

        // Mettre à jour l'indicateur
        updateQuestionIndicator();
        updateButtons();

        // Mettre à jour l'affichage du timer
        updateQuestionTimerDisplay();

        // Restaurer la réponse sélectionnée si elle existe
        if (userAnswers[questionNumber]) {
            updateAnswerStyles(questionNumber, userAnswers[questionNumber]);
        }

        // Démarrer le timer pour cette question
        startQuestionTimer();
    }
}

// Fonction pour démarrer le timer de la question
function startQuestionTimer() {
    // Mettre à jour l'affichage initial
    updateQuestionTimerDisplay();

    // Arrêter le timer précédent s'il existe
    if (questionTimer) {
        clearInterval(questionTimer);
    }

    // Démarrer le nouveau timer
    questionTimer = setInterval(() => {
        questionTimeLeft[currentQuestion]--;
        updateQuestionTimerDisplay();

        // Changer le style quand il reste peu de temps
        if (questionTimeLeft[currentQuestion] <= 10) {
            setQuestionTimerStyle('danger');
        } else if (questionTimeLeft[currentQuestion] <= 20) {
            setQuestionTimerStyle('warning');
        }

        // Si le temps de la question est écoulé
        if (questionTimeLeft[currentQuestion] <= 0) {
            clearInterval(questionTimer);
            questionTimeLeft[currentQuestion] = 0;

            // Sélectionner automatiquement une réponse aléatoire si aucune réponse n'est sélectionnée
            if (!userAnswers[currentQuestion]) {
                selectRandomAnswer();
            }

            // Passer automatiquement à la question suivante après un délai
            setTimeout(() => {
                if (currentQuestion < totalQuestions) {
                    goToNextQuestion();
                } else {
                    // Si c'est la dernière question, activer le bouton de soumission
                    const submitBtn = document.getElementById('submitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }
                }
            }, 1000);
        }
    }, 1000);
}

// Fonction pour réinitialiser le timer de la question
function resetQuestionTimer() {
    questionTimeLeft[currentQuestion] = questionTotalTime;
    updateQuestionTimerDisplay();
    setQuestionTimerStyle('normal');
}

// Fonction pour sélectionner une réponse aléatoire
function selectRandomAnswer() {
    const questionCard = document.getElementById(`question-${currentQuestion}`);
    if (!questionCard) return;

    // Récupérer toutes les options disponibles
    const options = questionCard.querySelectorAll('.option-input');
    if (options.length === 0) return;

    // Sélectionner une option aléatoire
    const randomIndex = Math.floor(Math.random() * options.length);
    const randomOption = options[randomIndex];

    // Cocher l'option
    randomOption.checked = true;

    // Déclencher l'événement change
    randomOption.dispatchEvent(new Event('change'));
}

// Fonction pour mettre à jour l'affichage du timer de la question
function updateQuestionTimerDisplay() {
    // Trouver le timer de la question active
    const activeQuestion = document.querySelector('.question-card.active');
    if (!activeQuestion) return;

    const timerElement = activeQuestion.querySelector('.question-timer-display');
    if (!timerElement) return;

    const timeLeft = questionTimeLeft[currentQuestion] || questionTotalTime;
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    const formattedSeconds = seconds.toString().padStart(2, '0');
    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${formattedSeconds}`;
}

// Fonction pour définir le style du timer de la question
function setQuestionTimerStyle(style) {
    const activeQuestion = document.querySelector('.question-card.active');
    if (!activeQuestion) return;

    const timerDisplay = activeQuestion.querySelector('.question-timer-display');
    if (!timerDisplay) return;

    // Retirer tous les styles
    timerDisplay.classList.remove('warning', 'danger');

    // Ajouter le nouveau style
    if (style === 'warning') {
        timerDisplay.classList.add('warning');
    } else if (style === 'danger') {
        timerDisplay.classList.add('danger');
    }
}

// Fonction pour aller à la question suivante
function goToNextQuestion() {
    if (currentQuestion < totalQuestions) {
        showQuestion(currentQuestion + 1);
    }
}

// Fonction pour aller à la question précédente
function goToPreviousQuestion() {
    if (currentQuestion > 1) {
        showQuestion(currentQuestion - 1);
    }
}

// Fonction pour mettre à jour l'indicateur de question
function updateQuestionIndicator() {
    const indicator = document.getElementById('currentQuestion');
    if (indicator) {
        indicator.textContent = `Question ${currentQuestion} sur ${totalQuestions}`;
    }
}

// Fonction pour mettre à jour les boutons de navigation
function updateButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    // Bouton précédent
    if (prevBtn) {
        prevBtn.style.display = currentQuestion > 1 ? 'inline-flex' : 'none';
    }

    // Bouton suivant/soumettre
    if (nextBtn && submitBtn) {
        if (currentQuestion < totalQuestions) {
            nextBtn.style.display = 'inline-flex';
            submitBtn.style.display = 'none';

            // Activer/désactiver le bouton suivant
            nextBtn.disabled = !userAnswers[currentQuestion];
        } else {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'inline-flex';
        }
    }
}

// Fonction pour mettre à jour la barre de progression
function updateProgress() {
    // Calculer le pourcentage de progression
    const answeredQuestions = Object.keys(userAnswers).length;
    const progressPercent = (answeredQuestions / totalQuestions) * 100;

    // Mettre à jour la barre de progression
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');

    if (progressFill) {
        progressFill.style.width = `${progressPercent}%`;
    }

    if (progressText) {
        progressText.textContent = `${answeredQuestions}/${totalQuestions}`;
    }
}

function initializeQuiz() {
    try {
        // Compter le nombre total de questions
        totalQuestions = document.querySelectorAll('.question-card').length;

        if (totalQuestions === 0) {
            console.error('Aucune question trouvée');
            showError('Aucune question disponible pour ce quiz.');
            return;
        }

        console.log('Quiz initialisé avec', totalQuestions, 'questions');

        // Initialiser les timers pour toutes les questions
        for (let i = 1; i <= totalQuestions; i++) {
            questionTimeLeft[i] = questionTotalTime;
        }

        // Initialiser les écouteurs d'événements
        setupEventListeners();

        // Afficher la première question
        showQuestion(1);

        // Mettre à jour l'interface
        updateQuestionIndicator();
        updateButtons();
        updateProgress();

        quizStarted = true;

    } catch (error) {
        console.error('Erreur lors de l\'initialisation du quiz:', error);
        showError('Erreur lors du chargement du quiz.');
    }
}

function autoCompleteUnansweredQuestions() {
    // Pour chaque question non répondue, sélectionner une réponse aléatoire
    for (let i = 1; i <= totalQuestions; i++) {
        if (!userAnswers[i]) {
            selectRandomAnswer();
        }
    }
}

function submitQuizForm() {
    if (formSubmitted) {
        return false;
    }

    // Arrêter le timer avant la soumission
    if (questionTimer) {
        clearInterval(questionTimer);
    }

    // Calculer le temps total passé sur le quiz
    let totalTimeSpent = 0;
    for (let i = 1; i <= totalQuestions; i++) {
        totalTimeSpent += (questionTotalTime - (questionTimeLeft[i] || 0));
    }

    // Mettre à jour le champ caché du temps
    const timeSpentField = document.getElementById('timeSpent');
    if (timeSpentField) {
        timeSpentField.value = totalTimeSpent;
    }

    // Afficher un indicateur de chargement
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Soumission en cours...';
    }

    // Marquer comme soumis
    formSubmitted = true;

    // Soumettre le formulaire
    const quizForm = document.getElementById('quizForm');
    if (quizForm) {
        console.log('Soumission du formulaire...');
        quizForm.submit();
    }
}

function validateBeforeSubmit(event) {
    if (event) {
        event.preventDefault();
    }

    // Arrêter le timer
    if (questionTimer) {
        clearInterval(questionTimer);
    }

    const unansweredQuestions = [];

    for (let i = 1; i <= totalQuestions; i++) {
        if (!userAnswers[i]) {
            unansweredQuestions.push(i);
        }
    }

    if (unansweredQuestions.length > 0) {
        const message = unansweredQuestions.length === 1
            ? `La question ${unansweredQuestions[0]} n'a pas de réponse.`
            : `Les questions ${unansweredQuestions.join(', ')} n'ont pas de réponse.`;

        showNotification(message + ' Veuillez répondre à toutes les questions avant de soumettre.', 'warning');

        // Aller à la première question sans réponse
        const firstUnanswered = Math.min(...unansweredQuestions);
        showQuestion(firstUnanswered);

        return false;
    }

    // Afficher un message de confirmation
    const confirmed = confirm('Êtes-vous sûr de vouloir soumettre vos réponses ? Vous ne pourrez plus les modifier.');
    if (!confirmed) {
        // Redémarrer le timer si l'utilisateur annule
        if (currentQuestion <= totalQuestions) {
            startQuestionTimer();
        }
        return false;
    }

    // Soumettre le formulaire
    submitQuizForm();
    return true;
}

// Ajouter un gestionnaire pour arrêter le timer lorsque l'utilisateur quitte la page
window.addEventListener('beforeunload', function (e) {
    if (quizStarted && !formSubmitted) {
        // Arrêter le timer
        if (questionTimer) {
            clearInterval(questionTimer);
        }

        // Message d'avertissement
        const message = 'Vous avez un quiz en cours. Voulez-vous vraiment quitter ?';
        e.returnValue = message;
        return message;
    }
});

// Nettoyer le timer lorsque la page se décharge
window.addEventListener('unload', function () {
    if (questionTimer) {
        clearInterval(questionTimer);
    }
});

// Initialiser le quiz lorsque la page est chargée
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM chargé, initialisation du quiz...');
    initializeQuiz();
});
