// quiz_page.js - Version avec toutes les questions obligatoires et temps expiré = non répondu
// ==================== AJOUTER CES FONCTIONS GLOBALES ====================
// Exposer les fonctions nécessaires pour les aides
window.quizManager = {
    currentQuestion: 1,
    questionTimers: {},
    updateTimerDisplay: function(questionNumber) {
        const timer = this.questionTimers[questionNumber];
        if (!timer || !timer.display) return;
        
        const minutes = Math.floor(timer.timeLeft / 60);
        const seconds = timer.timeLeft % 60;
        timer.display.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Mettre à jour les classes CSS
        const timerContainer = timer.display.parentElement;
        timerContainer.classList.remove('warning', 'danger');
        
        if (timer.timeLeft <= timer.dangerThreshold) {
            timerContainer.classList.add('danger');
            timerContainer.style.boxShadow = '0 0 15px rgba(220, 53, 69, 0.2)';
        } else if (timer.timeLeft <= timer.warningThreshold) {
            timerContainer.classList.add('warning');
        }
    },
    addTime: function(questionNumber, secondsToAdd) {
        if (this.questionTimers[questionNumber]) {
            this.questionTimers[questionNumber].timeLeft += secondsToAdd;
            this.updateTimerDisplay(questionNumber);
            
            // Animation visuelle
            const timerDisplay = document.querySelector('.timer-display');
            if (timerDisplay) {
                timerDisplay.style.animation = 'none';
                setTimeout(() => {
                    timerDisplay.style.animation = 'timeAdded 1s ease';
                    timerDisplay.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
                    timerDisplay.style.borderColor = 'rgba(40, 167, 69, 0.3)';
                    
                    setTimeout(() => {
                        timerDisplay.style.animation = '';
                        timerDisplay.style.backgroundColor = '';
                        timerDisplay.style.borderColor = '';
                    }, 1000);
                }, 10);
            }
            return true;
        }
        return false;
    },
    revealWrongAnswer: function(questionNumber) {
        const currentCard = document.getElementById(`question-${questionNumber}`);
        if (!currentCard) return false;
        
        // Trouver toutes les options incorrectes
        const incorrectOptions = currentCard.querySelectorAll('.option-label[data-correct="false"]:not(.selected)');
        
        if (incorrectOptions.length > 0) {
            // Sélectionner une mauvaise réponse au hasard
            const randomIndex = Math.floor(Math.random() * incorrectOptions.length);
            const optionToReveal = incorrectOptions[randomIndex];
            
            // Ajouter une animation et révéler
            optionToReveal.style.animation = 'none';
            setTimeout(() => {
                optionToReveal.style.animation = 'shake 0.5s ease, fadeOut 2s ease';
                
                // Ajouter un effet de révélation
                const overlay = document.createElement('div');
                overlay.style.cssText = `
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(220, 53, 69, 0.2);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #dc3545;
                    font-weight: bold;
                    border-radius: var(--border-radius);
                    z-index: 100;
                `;
                overlay.innerHTML = '<span>✗ Mauvaise réponse</span>';
                optionToReveal.appendChild(overlay);
                
                // Retirer après 3 secondes
                setTimeout(() => {
                    if (overlay.parentNode) {
                        overlay.remove();
                    }
                    optionToReveal.style.animation = '';
                }, 3000);
            }, 10);
            return true;
        }
        return false;
    }
};
// ==================== FIN DES AJOUTS ====================

document.addEventListener('DOMContentLoaded', function() {
    // Configuration
    const TOTAL_TIME_PER_QUESTION = 30; // 30 secondes par question
    let currentQuestion = 1;
    let totalQuestions = document.querySelectorAll('.question-card').length;
    let userAnswers = {};
    let questionTimers = {};
    let quizSubmitted = false;
    let allQuestionsAnswered = false;
    let unansweredQuestions = new Set(); // Ensemble des questions non répondues

    // Initialisation
    initializeQuiz();

    function initializeQuiz() {
        if (totalQuestions === 0) {
            console.error('Aucune question trouvée');
            return;
        }

        console.log(`Quiz initialisé avec ${totalQuestions} questions (${TOTAL_TIME_PER_QUESTION}s par question - TOUTES OBLIGATOIRES)`);

        // Initialiser les timers pour chaque question
        initializeQuestionTimers();
        
        // Configurer les événements
        setupEventListeners();
        
        // Configurer la barre de progression
        updateProgressBar();
        
        // Configurer les boutons de navigation
        updateNavigationButtons();
        
        // Configurer l'indicateur de question
        updateQuestionIndicator();
        
        // Démarrer le timer de la première question
        startQuestionTimer(currentQuestion);
        
        // Styliser les timers
        styleTimersInline();
        
        // Initialiser l'ensemble des questions non répondues
        for (let i = 1; i <= totalQuestions; i++) {
            unansweredQuestions.add(i);
        }
        
        // LES AIDES SONT GÉRÉES DANS LE FICHIER PHP - PAS BESOIN D'INITIALISER ICI
    }

    function initializeQuestionTimers() {
        document.querySelectorAll('.question-card').forEach((card, index) => {
            const questionNumber = index + 1;
            const timerDisplay = card.querySelector('.question-timer-display');
            
            if (timerDisplay) {
                const timerObj = {
                    timeLeft: TOTAL_TIME_PER_QUESTION,
                    interval: null,
                    display: timerDisplay,
                    warningThreshold: 10,
                    dangerThreshold: 5,
                    expired: false
                };
                
                questionTimers[questionNumber] = timerObj;
                // Synchroniser avec le manager global pour les aides
                window.quizManager.questionTimers[questionNumber] = timerObj;
                
                updateTimerDisplay(questionNumber);
            }
        });
    }

    function styleTimersInline() {
        document.querySelectorAll('.question-timer').forEach(timer => {
            timer.style.cssText = `
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 15px 0;
            `;
            
            const timerDisplay = timer.querySelector('.timer-display');
            if (timerDisplay) {
                timerDisplay.style.cssText = `
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    justify-content: center;
                    background: rgba(255, 74, 87, 0.08);
                    border: 1.5px solid rgba(255, 74, 87, 0.3);
                    border-radius: 15px;
                    padding: 10px 20px;
                    min-width: 110px;
                    box-shadow: 0 3px 10px rgba(0,0,0,0.12);
                    gap: 10px;
                    transition: all 0.3s ease;
                `;
                
                const icon = timerDisplay.querySelector('.timer-icon');
                if (icon) {
                    icon.style.cssText = `
                        font-size: 1.3rem;
                        color: var(--primary-color);
                        opacity: 0.9;
                    `;
                }
                
                const timerText = timerDisplay.querySelector('.question-timer-display');
                if (timerText) {
                    timerText.style.cssText = `
                        font-size: 1.4rem;
                        font-weight: 700;
                        color: var(--text-color);
                        font-family: 'Courier New', monospace;
                        letter-spacing: 1.5px;
                        min-width: 55px;
                        text-align: center;
                    `;
                }
            }
        });
    }

    function setupEventListeners() {
        // Boutons de navigation
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', goToPreviousQuestion);
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', goToNextQuestion);
        }
        
        if (submitBtn) {
            submitBtn.addEventListener('click', handleSubmit);
        }
        
        // Sélection des options
        document.querySelectorAll('.option-input').forEach(input => {
            input.addEventListener('change', function() {
                handleOptionSelection(this);
            });
        });
        
        // Gestion des touches du clavier
        document.addEventListener('keydown', handleKeyPress);
        
        // Empêcher la fermeture accidentelle
        window.addEventListener('beforeunload', handleBeforeUnload);
        
        // Gestion de la visibilité de la page
        document.addEventListener('visibilitychange', handleVisibilityChange);
    }

    function startQuestionTimer(questionNumber) {
        if (!questionTimers[questionNumber] || questionTimers[questionNumber].interval || questionTimers[questionNumber].expired) {
            return;
        }
        
        console.log(`Démarrage du timer pour la question ${questionNumber}`);
        
        // Mettre à jour currentQuestion global
        window.quizManager.currentQuestion = questionNumber;
        
        questionTimers[questionNumber].interval = setInterval(() => {
            questionTimers[questionNumber].timeLeft--;
            
            // Synchroniser avec le manager global
            if (window.quizManager.questionTimers[questionNumber]) {
                window.quizManager.questionTimers[questionNumber].timeLeft = questionTimers[questionNumber].timeLeft;
            }
            
            updateTimerDisplay(questionNumber);
            
            if (questionTimers[questionNumber].timeLeft <= 0) {
                clearInterval(questionTimers[questionNumber].interval);
                questionTimers[questionNumber].interval = null;
                questionTimers[questionNumber].expired = true;
                
                // Synchroniser avec le manager global
                if (window.quizManager.questionTimers[questionNumber]) {
                    window.quizManager.questionTimers[questionNumber].expired = true;
                }
                
                handleTimeExpired(questionNumber);
            }
        }, 1000);
    }

    function stopQuestionTimer(questionNumber) {
        if (questionTimers[questionNumber] && questionTimers[questionNumber].interval) {
            clearInterval(questionTimers[questionNumber].interval);
            questionTimers[questionNumber].interval = null;
            console.log(`Timer arrêté pour la question ${questionNumber}`);
        }
    }

    function updateTimerDisplay(questionNumber) {
        const timer = questionTimers[questionNumber];
        if (!timer || !timer.display) return;
        
        const minutes = Math.floor(timer.timeLeft / 60);
        const seconds = timer.timeLeft % 60;
        timer.display.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Mettre à jour les classes CSS
        const timerContainer = timer.display.parentElement;
        timerContainer.classList.remove('warning', 'danger');
        
        if (timer.timeLeft <= timer.dangerThreshold) {
            timerContainer.classList.add('danger');
            timerContainer.style.boxShadow = '0 0 15px rgba(220, 53, 69, 0.2)';
        } else if (timer.timeLeft <= timer.warningThreshold) {
            timerContainer.classList.add('warning');
        }
    }

    function handleTimeExpired(questionNumber) {
        console.log(`Temps écoulé pour la question ${questionNumber}`);
        
        const currentCard = document.getElementById(`question-${questionNumber}`);
        if (currentCard) {
            const selectedOption = currentCard.querySelector('.option-input:checked');
            
            if (!selectedOption) {
                // Marquer la question comme non répondue
                markQuestionAsUnanswered(questionNumber);
                
                // Ajouter à la liste des questions non répondues
                unansweredQuestions.add(questionNumber);
                
                // Mettre à jour le statut
                updateQuestionStatus(questionNumber);
                
                // Afficher une notification
                showNotification(`Temps écoulé ! Question ${questionNumber} marquée comme non répondue.`, 'warning', 2000);
                
                // Mettre à jour la progression
                updateProgressBar();
                
                // Vérifier si toutes les questions sont répondues
                checkAllQuestionsAnswered();
            }
        }
        
        // Si ce n'est pas la dernière question, passer automatiquement à la suivante
        if (questionNumber < totalQuestions) {
            setTimeout(() => {
                // Passer à la question suivante
                if (currentQuestion === questionNumber) {
                    goToNextQuestion();
                }
            }, 1000);
        }
    }

    function markQuestionAsUnanswered(questionNumber) {
        const card = document.getElementById(`question-${questionNumber}`);
        if (!card) return;
        
        // Ajouter la classe "expired" à la carte
        card.classList.add('expired');
        card.classList.remove('completed');
        
        // Ajouter un badge "Temps écoulé"
        const questionHeader = card.querySelector('.question-header');
        if (questionHeader) {
            // Supprimer les badges existants
            const existingBadges = questionHeader.querySelectorAll('.expired-badge, .required-badge');
            existingBadges.forEach(badge => badge.remove());
            
            // Créer un nouveau badge
            const expiredBadge = document.createElement('span');
            expiredBadge.className = 'expired-badge';
            expiredBadge.textContent = 'Temps écoulé';
            expiredBadge.style.cssText = `
                background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
                color: white !important;
                padding: 3px 10px !important;
                border-radius: 12px !important;
                font-size: 0.8rem !important;
                font-weight: 600 !important;
                margin-left: 10px !important;
                animation: pulseExpired 2s infinite !important;
            `;
            
            questionHeader.appendChild(expiredBadge);
        }
        
        // Désactiver les options
        const options = card.querySelectorAll('.option-input');
        options.forEach(option => {
            option.disabled = true;
        });
        
        // Ajouter un champ caché pour indiquer que le temps a expiré
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `questions[${questionNumber-1}][time_expired]`;
        hiddenInput.value = '1';
        card.appendChild(hiddenInput);
        
        console.log(`Question ${questionNumber} marquée comme non répondue (temps écoulé)`);
    }

    function removeExpiredBadge(questionNumber) {
        const card = document.getElementById(`question-${questionNumber}`);
        if (!card) return;
        
        card.classList.remove('expired');
        
        const questionHeader = card.querySelector('.question-header');
        if (questionHeader) {
            const existingBadge = questionHeader.querySelector('.expired-badge');
            if (existingBadge) {
                existingBadge.remove();
            }
        }
        
        // Réactiver les options
        const options = card.querySelectorAll('.option-input');
        options.forEach(option => {
            option.disabled = false;
        });
    }

    function navigateToQuestion(questionNumber) {
        if (questionNumber < 1 || questionNumber > totalQuestions) {
            return;
        }
        
        console.log(`Navigation de la question ${currentQuestion} à la question ${questionNumber}`);
        
        // Sauvegarder la réponse actuelle
        saveCurrentAnswer();
        
        // Arrêter le timer de la question actuelle
        stopQuestionTimer(currentQuestion);
        
        // Masquer la question actuelle
        const currentCard = document.getElementById(`question-${currentQuestion}`);
        if (currentCard) {
            currentCard.classList.remove('active');
            currentCard.style.display = 'none';
        }
        
        // Mettre à jour la question courante
        currentQuestion = questionNumber;
        window.quizManager.currentQuestion = questionNumber; // Synchroniser global
        
        // Afficher la nouvelle question
        const newCard = document.getElementById(`question-${currentQuestion}`);
        if (newCard) {
            newCard.classList.add('active');
            newCard.style.display = 'block';
            
            // Restaurer la réponse si elle existe
            restoreAnswer(currentQuestion);
            
            // Démarrer le timer (si pas déjà expiré)
            if (!questionTimers[currentQuestion]?.expired) {
                startQuestionTimer(currentQuestion);
            }
            
            // Animation
            newCard.style.animation = 'none';
            setTimeout(() => {
                newCard.style.animation = 'fadeInUp 0.5s ease';
            }, 10);
        }
        
        // Mettre à jour l'interface
        updateProgressBar();
        updateNavigationButtons();
        updateQuestionIndicator();
        
        // Scroll
        setTimeout(() => {
            if (newCard) {
                newCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }

    function goToPreviousQuestion() {
        if (currentQuestion > 1) {
            navigateToQuestion(currentQuestion - 1);
        }
    }

    function goToNextQuestion() {
        if (validateCurrentQuestion()) {
            if (currentQuestion < totalQuestions) {
                navigateToQuestion(currentQuestion + 1);
            }
        } else {
            // Si aucune réponse n'est sélectionnée
            showValidationError();
        }
    }

    function validateCurrentQuestion() {
        const currentCard = document.getElementById(`question-${currentQuestion}`);
        if (!currentCard) return false;
        
        // Si le temps a expiré, on peut passer
        if (questionTimers[currentQuestion]?.expired) {
            return true;
        }
        
        const selectedOption = currentCard.querySelector('.option-input:checked');
        return !!selectedOption;
    }

    function showValidationError() {
        const currentCard = document.getElementById(`question-${currentQuestion}`);
        if (!currentCard) return;
        
        // Animation de secousse
        currentCard.classList.add('shake-animation');
        setTimeout(() => {
            currentCard.classList.remove('shake-animation');
        }, 500);
        
        // Ajouter le badge "Réponse requise"
        markQuestionAsRequired(currentQuestion);
        
        // Surbrillance des options
        highlightUnansweredOptions(currentCard);
        
        // Notification
        showNotification('Veuillez sélectionner une réponse avant de continuer.', 'danger', 3000);
        
        // Focus sur la première option
        const firstOption = currentCard.querySelector('.option-input');
        if (firstOption) {
            const label = firstOption.closest('.option-label');
            if (label) {
                label.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }

    function markQuestionAsRequired(questionNumber) {
        const card = document.getElementById(`question-${questionNumber}`);
        if (!card) return;
        
        const questionHeader = card.querySelector('.question-header');
        if (questionHeader) {
            // Supprimer le badge existant (sauf expired)
            const existingBadge = questionHeader.querySelector('.required-badge');
            if (existingBadge) {
                existingBadge.remove();
            }
            
            // Ne pas ajouter de badge si le temps a expiré
            if (questionTimers[questionNumber]?.expired) {
                return;
            }
            
            // Créer un nouveau badge
            const requiredBadge = document.createElement('span');
            requiredBadge.className = 'required-badge';
            requiredBadge.textContent = 'Réponse requise';
            requiredBadge.style.cssText = `
                background: linear-gradient(135deg, var(--primary-color), #ff6b7a) !important;
                color: white !important;
                padding: 3px 10px !important;
                border-radius: 12px !important;
                font-size: 0.8rem !important;
                font-weight: 600 !important;
                margin-left: 10px !important;
                animation: pulseRequired 2s infinite !important;
            `;
            
            questionHeader.appendChild(requiredBadge);
        }
    }

    function removeRequiredBadge(questionNumber) {
        const card = document.getElementById(`question-${questionNumber}`);
        if (!card) return;
        
        const questionHeader = card.querySelector('.question-header');
        if (questionHeader) {
            const existingBadge = questionHeader.querySelector('.required-badge');
            if (existingBadge) {
                existingBadge.remove();
            }
        }
    }

    function highlightUnansweredOptions(card) {
        const options = card.querySelectorAll('.option-label');
        options.forEach(label => {
            label.style.backgroundColor = 'rgba(220, 53, 69, 0.05)';
            label.style.borderColor = 'rgba(220, 53, 69, 0.3)';
            
            setTimeout(() => {
                label.style.backgroundColor = '';
                label.style.borderColor = '';
            }, 2000);
        });
    }

    function handleOptionSelection(input) {
        const questionNumber = parseInt(input.dataset.question);
        const optionValue = input.value;
        
        console.log(`Question ${questionNumber}: option ${optionValue} sélectionnée`);
        
        // Retirer la classe selected de toutes les options
        const questionCard = input.closest('.question-card');
        if (questionCard) {
            questionCard.querySelectorAll('.option-label').forEach(label => {
                label.classList.remove('selected');
            });
        }
        
        // Ajouter la classe selected
        const label = input.closest('.option-label');
        if (label) {
            label.classList.add('selected');
            
            // Animation
            label.style.animation = 'none';
            setTimeout(() => {
                label.style.animation = 'selectedPulse 0.6s ease';
            }, 10);
        }
        
        // Supprimer les badges
        removeRequiredBadge(questionNumber);
        removeExpiredBadge(questionNumber);
        
        // Si le temps avait expiré, réactiver le timer
        if (questionTimers[questionNumber]?.expired) {
            questionTimers[questionNumber].expired = false;
            if (window.quizManager.questionTimers[questionNumber]) {
                window.quizManager.questionTimers[questionNumber].expired = false;
            }
        }
        
        // Enlever de la liste des questions non répondues
        unansweredQuestions.delete(questionNumber);
        
        // Sauvegarder la réponse
        saveCurrentAnswer();
        
        // Mettre à jour le statut
        updateQuestionStatus(questionNumber);
        
        // Vérifier si toutes les questions sont répondues
        checkAllQuestionsAnswered();
    }

    function saveCurrentAnswer() {
        const currentCard = document.getElementById(`question-${currentQuestion}`);
        if (!currentCard) return;
        
        const selectedOption = currentCard.querySelector('.option-input:checked');
        if (selectedOption) {
            const questionId = selectedOption.name.match(/\[(.*?)\]/)[1];
            userAnswers[currentQuestion] = {
                questionId: questionId,
                answer: selectedOption.value,
                timestamp: new Date(),
                answered: true
            };
            
            // Marquer la question comme complétée
            currentCard.classList.add('completed');
            currentCard.classList.remove('required', 'expired');
            
            // Enlever de la liste des questions non répondues
            unansweredQuestions.delete(currentQuestion);
        }
    }

    function restoreAnswer(questionNumber) {
        const savedAnswer = userAnswers[questionNumber];
        if (!savedAnswer || !savedAnswer.answered) return;
        
        const card = document.getElementById(`question-${questionNumber}`);
        if (!card) return;
        
        const radioButton = card.querySelector(`input[name="answers[${savedAnswer.questionId}]"][value="${savedAnswer.answer}"]`);
        if (radioButton) {
            radioButton.checked = true;
            
            const label = radioButton.closest('.option-label');
            if (label) {
                label.classList.add('selected');
            }
        }
    }

    function updateProgressBar() {
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        
        if (progressFill) {
            const answeredCount = Object.keys(userAnswers).length;
            const percentage = (answeredCount / totalQuestions) * 100;
            progressFill.style.width = `${percentage}%`;
            
            // Changer la couleur si des questions ne sont pas répondues
            if (unansweredQuestions.size > 0) {
                progressFill.style.background = 'linear-gradient(90deg, var(--primary-color), #ffc107)';
            } else {
                progressFill.style.background = 'linear-gradient(90deg, var(--primary-color), #ff6b7a)';
            }
        }
        
        if (progressText) {
            const answeredCount = Object.keys(userAnswers).length;
            const unansweredCount = unansweredQuestions.size;
            
            if (unansweredCount > 0) {
                progressText.textContent = `${answeredCount}/${totalQuestions} (${unansweredCount} non répondues)`;
                progressText.style.color = '#ffc107';
            } else {
                progressText.textContent = `${answeredCount}/${totalQuestions} - Toutes répondues ✓`;
                progressText.style.color = '#28a745';
            }
        }
    }

    function updateNavigationButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        
        // Bouton Précédent
        if (prevBtn) {
            prevBtn.style.display = currentQuestion > 1 ? 'inline-flex' : 'none';
        }
        
        // Boutons Suivant/Soumettre
        if (nextBtn && submitBtn) {
            if (currentQuestion < totalQuestions) {
                nextBtn.style.display = 'inline-flex';
                submitBtn.style.display = 'none';
            } else {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'inline-flex';
                
                // Le bouton soumettre est toujours actif maintenant
                // car on accepte les questions non répondues (temps expiré)
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
                submitBtn.title = 'Soumettre le quiz';
            }
        }
    }

    function updateQuestionIndicator() {
        const indicator = document.getElementById('currentQuestion');
        if (indicator) {
            let status = '';
            if (questionTimers[currentQuestion]?.expired) {
                status = ' (Temps écoulé)';
                indicator.style.color = '#ffc107';
            } else if (userAnswers[currentQuestion]) {
                status = ' (Répondu)';
                indicator.style.color = '#28a745';
            } else {
                indicator.style.color = 'var(--text-color)';
            }
            
            indicator.textContent = `Question ${currentQuestion} sur ${totalQuestions}${status}`;
        }
    }

    function updateQuestionStatus(questionNumber) {
        const card = document.getElementById(`question-${questionNumber}`);
        if (!card) return;
        
        const isAnswered = !!card.querySelector('.option-input:checked');
        const isExpired = questionTimers[questionNumber]?.expired;
        
        if (isAnswered) {
            card.classList.add('completed');
            card.classList.remove('required', 'expired');
            removeRequiredBadge(questionNumber);
            removeExpiredBadge(questionNumber);
        } else if (isExpired) {
            card.classList.add('expired');
            card.classList.remove('completed', 'required');
            markQuestionAsUnanswered(questionNumber);
        }
    }

    function checkAllQuestionsAnswered() {
        allQuestionsAnswered = (unansweredQuestions.size === 0);
        
        // Mettre à jour le bouton soumettre (toujours actif maintenant)
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn && submitBtn.style.display !== 'none') {
            if (unansweredQuestions.size > 0) {
                submitBtn.innerHTML = 'Soumettre (<span style="color: #ffc107">' + unansweredQuestions.size + ' non répondues</span>) →';
            } else {
                submitBtn.innerHTML = 'Terminer le quiz ✓ →';
                submitBtn.style.background = 'linear-gradient(45deg, var(--success-color), #20c997)';
            }
        }
        
        return allQuestionsAnswered;
    }

    function handleSubmit(event) {
        event.preventDefault();
        
        // Avertissement si des questions ne sont pas répondues
        if (unansweredQuestions.size > 0) {
            const unansweredList = Array.from(unansweredQuestions).sort((a, b) => a - b);
            const message = `Attention ! ${unansweredQuestions.size} question(s) ne sont pas répondues (${unansweredList.join(', ')}). Ces questions recevront 0 point. Voulez-vous vraiment soumettre ?`;
            
            if (!confirm(message)) {
                return;
            }
        }
        
        // Confirmation finale
        if (!confirm('Êtes-vous sûr de vouloir soumettre vos réponses ? Vous ne pourrez plus les modifier.')) {
            return;
        }
        
        // Arrêter tous les timers
        stopAllTimers();
        
        // Marquer le quiz comme soumis
        quizSubmitted = true;
        
        // Calculer le temps total passé
        let totalTime = 0;
        for (let qNum in questionTimers) {
            totalTime += (TOTAL_TIME_PER_QUESTION - questionTimers[qNum].timeLeft);
        }
        document.getElementById('timeSpent').value = totalTime;
        
        // Ajouter un champ caché pour chaque question non répondue
        unansweredQuestions.forEach(questionNumber => {
            const card = document.getElementById(`question-${questionNumber}`);
            if (card) {
                const questionIdInput = card.querySelector('input[name^="questions"]');
                if (questionIdInput) {
                    const name = questionIdInput.name;
                    const match = name.match(/questions\[(\d+)\]\[id\]/);
                    if (match) {
                        const questionIndex = match[1];
                        
                        // Ajouter un champ caché
                        const unansweredInput = document.createElement('input');
                        unansweredInput.type = 'hidden';
                        unansweredInput.name = `questions[${questionIndex}][unanswered]`;
                        unansweredInput.value = '1';
                        document.getElementById('quizForm').appendChild(unansweredInput);
                    }
                }
            }
        });
        
        // Sauvegarder les aides utilisées dans le formulaire
        saveHelpsUsed();
        
        // Soumettre le formulaire
        document.getElementById('quizForm').submit();
    }
    
    function saveHelpsUsed() {
        const helpsUsed = [];
        const helpButtons = document.querySelectorAll('.help-button.used');
        helpButtons.forEach(button => {
            const helpId = button.id.replace('help', '');
            helpsUsed.push(helpId);
        });
        document.getElementById('helpsUsed').value = helpsUsed.join(',');
    }

    function stopAllTimers() {
        for (let questionNumber in questionTimers) {
            stopQuestionTimer(parseInt(questionNumber));
        }
    }

    function handleKeyPress(event) {
        // Navigation avec les flèches
        if (event.key === 'ArrowLeft' && currentQuestion > 1) {
            event.preventDefault();
            goToPreviousQuestion();
        } else if (event.key === 'ArrowRight' && currentQuestion <= totalQuestions) {
            event.preventDefault();
            if (validateCurrentQuestion()) {
                if (currentQuestion < totalQuestions) {
                    goToNextQuestion();
                }
            } else {
                showValidationError();
            }
        }
        
        // Touches 1, 2, 3 pour sélectionner rapidement
        if (event.key >= '1' && event.key <= '9') {
            const optionIndex = parseInt(event.key) - 1;
            const currentCard = document.getElementById(`question-${currentQuestion}`);
            if (currentCard) {
                const options = currentCard.querySelectorAll('.option-input');
                if (options[optionIndex]) {
                    options[optionIndex].checked = true;
                    const changeEvent = new Event('change');
                    options[optionIndex].dispatchEvent(changeEvent);
                    event.preventDefault();
                }
            }
        }
        
        // Touche Entrée pour soumettre
        if (event.key === 'Enter' && currentQuestion === totalQuestions) {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn && submitBtn.style.display !== 'none') {
                event.preventDefault();
                handleSubmit(event);
            }
        }
    }

    function handleBeforeUnload(event) {
        if (!quizSubmitted && (currentQuestion > 1 || Object.keys(userAnswers).length > 0)) {
            event.preventDefault();
            event.returnValue = 'Vous êtes en train de passer un quiz. Si vous quittez, vos réponses seront perdues.';
            return event.returnValue;
        }
    }

    function handleVisibilityChange() {
        if (document.hidden) {
            console.log('Quiz mis en pause');
        } else {
            console.log('Quiz repris');
        }
    }

    function showNotification(message, type = 'info', duration = 3000) {
        // Créer la notification
        const notification = document.createElement('div');
        notification.className = `quiz-notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">&times;</button>
        `;
        
        // Styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 18px;
            background: ${getNotificationColor(type)};
            color: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 280px;
            max-width: 380px;
            animation: slideInRight 0.3s ease;
            font-weight: 500;
            font-size: 0.95rem;
        `;
        
        notification.querySelector('.notification-content').style.cssText = `
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        `;
        
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.style.cssText = `
            background: none;
            border: none;
            color: white;
            font-size: 1.4rem;
            cursor: pointer;
            margin-left: 10px;
            padding: 0;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.8;
            transition: opacity 0.3s;
        `;
        
        closeBtn.addEventListener('click', function() {
            notification.style.animation = 'slideInRight 0.3s ease reverse';
            setTimeout(() => {
                notification.remove();
            }, 300);
        });
        
        document.body.appendChild(notification);
        
        // Supprimer automatiquement
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideInRight 0.3s ease reverse';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }
        }, duration);
    }

    function getNotificationIcon(type) {
        switch(type) {
            case 'success': return 'fa-check-circle';
            case 'warning': return 'fa-exclamation-triangle';
            case 'danger': return 'fa-times-circle';
            case 'info': 
            default: return 'fa-info-circle';
        }
    }

    function getNotificationColor(type) {
        switch(type) {
            case 'success': return 'linear-gradient(135deg, #28a745, #20c997)';
            case 'warning': return 'linear-gradient(135deg, #ffc107, #fd7e14)';
            case 'danger': return 'linear-gradient(135deg, #dc3545, #c82333)';
            case 'info': 
            default: return 'linear-gradient(135deg, #17a2b8, #138496)';
        }
    }

    // Ajouter les styles CSS manquants
    addMissingStyles();
    
    function addMissingStyles() {
        const style = document.createElement('style');
        style.textContent = `
            /* Animation de secousse */
            .shake-animation {
                animation: shake 0.5s ease;
            }
            
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-10px); }
                75% { transform: translateX(10px); }
            }
            
            /* Animation pulse pour le badge requis */
            @keyframes pulseRequired {
                0%, 100% { 
                    opacity: 1;
                    transform: scale(1);
                }
                50% { 
                    opacity: 0.8;
                    transform: scale(1.05);
                }
            }
            
            /* Animation pulse pour le badge expiré */
            @keyframes pulseExpired {
                0%, 100% { 
                    opacity: 1;
                    transform: scale(1);
                    background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
                }
                50% { 
                    opacity: 0.7;
                    transform: scale(1.05);
                    background: linear-gradient(135deg, #fd7e14, #ffc107) !important;
                }
            }
            
            /* Animation pour les notifications */
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            /* Style pour les timers */
            .timer-display.warning {
                border-color: rgba(255, 193, 7, 0.4);
                background: rgba(255, 193, 7, 0.08);
            }
            
            .timer-display.warning .timer-icon,
            .timer-display.warning .question-timer-display {
                color: #ffc107;
            }
            
            .timer-display.danger {
                border-color: rgba(220, 53, 69, 0.4);
                background: rgba(220, 53, 69, 0.08);
            }
            
            .timer-display.danger .timer-icon,
            .timer-display.danger .question-timer-display {
                color: #dc3545;
            }
            
            /* Style pour les questions expirées */
            .question-card.expired {
                position: relative;
                opacity: 0.9;
            }
            
            .question-card.expired::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 4px;
                height: 100%;
                background: linear-gradient(to bottom, #ffc107, #fd7e14);
                border-radius: 2px;
            }
            
            .question-card.expired .option-input:disabled + .option-text {
                opacity: 0.6;
            }
            
            .question-card.expired .option-label {
                cursor: not-allowed;
            }
            
            /* Style pour les questions complétées */
            .question-card.completed {
                position: relative;
            }
            
            .question-card.completed::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 4px;
                height: 100%;
                background: linear-gradient(to bottom, #28a745, #20c997);
                border-radius: 2px;
            }
            
            /* Animation pour l'ajout de temps */
            @keyframes timeAdded {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
            
            /* Animation pour la révélation de mauvaise réponse */
            @keyframes fadeOut {
                0% { opacity: 1; }
                100% { opacity: 0.7; }
            }
            
            /* Bouton soumettre avec warning */
            .btn-submit span {
                font-weight: bold;
            }
            
            /* Animation pour l'utilisation d'une aide */
            @keyframes helpUsed {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(0.9); }
            }
        `;
        document.head.appendChild(style);
    }

    // Initialiser le check des questions
    setTimeout(() => {
        checkAllQuestionsAnswered();
    }, 500);

    console.log('Quiz JS chargé avec succès - Toutes les questions sont obligatoires, temps expiré = non répondu');
});