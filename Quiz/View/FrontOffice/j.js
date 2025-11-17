// js11.js - Version avec validation complète des quiz obligatoires

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== GESTION DES MODALS =====
    
    // Éléments des cartes
    const santeCard = document.getElementById('sante-card');
    const environnementCard = document.getElementById('environnement-card');
    const educationCard = document.getElementById('education-card');
    
    // Éléments des modals
    const modalSante = document.getElementById('quizModalSante');
    const modalEnvironnement = document.getElementById('quizModalEnvironnement');
    const modalEducation = document.getElementById('quizModalEducation');
    
    // Boutons de fermeture
    const closeButtons = document.querySelectorAll('.close-btn');
    
    // ===== OUVERTURE DES MODALS =====
    
    santeCard.addEventListener('click', function() {
        modalSante.style.display = 'block';
        document.body.style.overflow = 'hidden';
    });
    
    environnementCard.addEventListener('click', function() {
        modalEnvironnement.style.display = 'block';
        document.body.style.overflow = 'hidden';
    });
    
    educationCard.addEventListener('click', function() {
        modalEducation.style.display = 'block';
        document.body.style.overflow = 'hidden';
    });
    
    // ===== FERMETURE DES MODALS =====
    
    // Fermer avec le bouton X
    closeButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    });
    
    // Fermer en cliquant en dehors du modal
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
    
    // Fermer avec la touche Échap
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                if (modal.style.display === 'block') {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        }
    });
    
    // ===== GESTION DES QUIZ OBLIGATOIRES =====
    
    const submitButtons = document.querySelectorAll('.submit-btn');
    
    submitButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const quizType = this.getAttribute('data-quiz');
            submitQuiz(quizType);
        });
    });
    
    function submitQuiz(quizType) {
        const formId = 'quizForm' + capitalizeFirstLetter(quizType);
        const resultId = 'quizResult' + capitalizeFirstLetter(quizType);
        
        const form = document.getElementById(formId);
        const resultDiv = document.getElementById(resultId);
        const submitBtn = form.querySelector('.submit-btn');
        
        // VÉRIFICATION OBLIGATOIRE - Toutes les questions doivent être répondues
        const validationResult = validateAllQuestions(form);
        
        if (!validationResult.isValid) {
            showValidationError(form, validationResult.unansweredQuestions);
            showTemporaryMessage(resultDiv, `❌ Veuillez répondre à toutes les questions (${validationResult.unansweredQuestions.length} question(s) manquante(s))`, 'error');
            return;
        }
        
        // Si toutes les questions sont répondues, procéder au calcul
        submitBtn.disabled = true;
        submitBtn.textContent = 'Calcul du score...';
        
        // Simulation du traitement
        setTimeout(function() {
            const score = calculateScore(form);
            showResults(resultDiv, score);
            
            submitBtn.disabled = false;
            submitBtn.textContent = 'Soumettre le quiz';
            
            resultDiv.scrollIntoView({ behavior: 'smooth' });
            
        }, 1500);
    }
    
    // FONCTION DE VALIDATION COMPLÈTE - TOUTES LES QUESTIONS OBLIGATOIRES
    function validateAllQuestions(form) {
        const questions = form.querySelectorAll('.quiz-question');
        const unansweredQuestions = [];
        
        questions.forEach(function(question, index) {
            const radioButtons = question.querySelectorAll('input[type="radio"]');
            let questionAnswered = false;
            
            radioButtons.forEach(function(radio) {
                if (radio.checked) {
                    questionAnswered = true;
                }
            });
            
            if (!questionAnswered) {
                unansweredQuestions.push({
                    element: question,
                    questionNumber: index + 1
                });
            }
        });
        
        return {
            isValid: unansweredQuestions.length === 0,
            unansweredQuestions: unansweredQuestions,
            totalQuestions: questions.length,
            answeredQuestions: questions.length - unansweredQuestions.length
        };
    }
    
    // AFFICHAGE DES ERREURS DE VALIDATION
    function showValidationError(form, unansweredQuestions) {
        // Réinitialiser tous les styles d'erreur d'abord
        const allQuestions = form.querySelectorAll('.quiz-question');
        allQuestions.forEach(function(question) {
            question.style.borderLeftColor = '#e74c3c';
            question.style.background = 'linear-gradient(145deg, #ffffff, #f8f9fa)';
            question.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.08)';
        });
        
        // Appliquer le style d'erreur aux questions non répondues
        unansweredQuestions.forEach(function(unanswered) {
            unanswered.element.style.borderLeftColor = '#dc3545';
            unanswered.element.style.background = 'linear-gradient(145deg, #fff5f5, #ffeaea)';
            unanswered.element.style.boxShadow = '0 8px 25px rgba(220, 53, 69, 0.2)';
            unanswered.element.style.animation = 'shake 0.5s ease-in-out';
            
            // Ajouter un indicateur visuel
            const questionText = unanswered.element.querySelector('.question-text');
            if (questionText && !questionText.querySelector('.required-indicator')) {
                const indicator = document.createElement('span');
                indicator.className = 'required-indicator';
                indicator.textContent = ' *';
                indicator.style.color = '#dc3545';
                indicator.style.fontWeight = 'bold';
                questionText.appendChild(indicator);
            }
            
            // Retirer l'animation après qu'elle soit terminée
            setTimeout(function() {
                unanswered.element.style.animation = '';
            }, 500);
        });
        
        // Faire défiler vers la première question non répondue
        if (unansweredQuestions.length > 0) {
            unansweredQuestions[0].element.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }
    }
    
    // CALCUL DU SCORE
    function calculateScore(form) {
        const questions = form.querySelectorAll('.quiz-question');
        let correctAnswers = 0;
        let totalQuestions = questions.length;
        
        questions.forEach(function(question) {
            const selectedOption = question.querySelector('input[type="radio"]:checked');
            
            // Marquer visuellement les réponses
            if (selectedOption) {
                if (selectedOption.value === 'correct') {
                    correctAnswers++;
                    // Bonne réponse
                    selectedOption.parentElement.style.background = 'linear-gradient(145deg, #d4edda, #c3e6cb)';
                    selectedOption.parentElement.style.borderColor = '#28a745';
                    selectedOption.parentElement.style.color = '#155724';
                } else {
                    // Mauvaise réponse
                    selectedOption.parentElement.style.background = 'linear-gradient(145deg, #f8d7da, #f5c6cb)';
                    selectedOption.parentElement.style.borderColor = '#dc3545';
                    selectedOption.parentElement.style.color = '#721c24';
                    
                    // Afficher la bonne réponse
                    const correctOption = question.querySelector('input[value="correct"]');
                    if (correctOption) {
                        correctOption.parentElement.style.background = 'linear-gradient(145deg, #d4edda, #c3e6cb)';
                        correctOption.parentElement.style.borderColor = '#28a745';
                        correctOption.parentElement.style.color = '#155724';
                    }
                }
            }
        });
        
        return {
            correct: correctAnswers,
            total: totalQuestions,
            percentage: Math.round((correctAnswers / totalQuestions) * 100)
        };
    }
    
    // AFFICHAGE DES RÉSULTATS
    function showResults(resultDiv, score) {
        let message = '';
        let resultClass = '';
        let emoji = '';
        
        if (score.percentage === 100) {
            message = '🎉 Parfait ! Score maximum ! Vous maîtrisez parfaitement ce sujet.';
            resultClass = 'success';
            emoji = '🏆';
        } else if (score.percentage >= 80) {
            message = '👍 Excellent travail ! Vos connaissances sont solides.';
            resultClass = 'success';
            emoji = '⭐';
        } else if (score.percentage >= 60) {
            message = '💪 Bon score ! Continuez à progresser.';
            resultClass = 'success';
            emoji = '✅';
        } else if (score.percentage >= 40) {
            message = '📚 Pas mal ! Revevez les points à améliorer.';
            resultClass = 'success';
            emoji = '📖';
        } else {
            message = '🔄 Ne vous découragez pas ! Étudiez à nouveau et réessayez.';
            resultClass = 'error';
            emoji = '💪';
        }
        
        resultDiv.innerHTML = `
            <div class="score-display">${score.correct}/${score.total}</div>
            <div style="font-size: 1.5rem; margin: 10px 0;">${emoji} Score: ${score.percentage}% ${emoji}</div>
            <p style="margin-top: 20px; font-size: 1.1rem;">${message}</p>
        `;
        
        resultDiv.className = 'result ' + resultClass;
        resultDiv.style.display = 'block';
    }
    
    // MESSAGE TEMPORAIRE
    function showTemporaryMessage(element, message, type) {
        const originalContent = element.innerHTML;
        const originalClass = element.className;
        const originalDisplay = element.style.display;
        
        element.innerHTML = `<p style="margin: 0; font-size: 1.1rem;">${message}</p>`;
        element.className = 'result ' + type;
        element.style.display = 'block';
        
        setTimeout(function() {
            element.innerHTML = originalContent;
            element.className = originalClass;
            element.style.display = originalDisplay;
        }, 4000);
    }
    
    // ===== AMÉLIORATIONS DE L'EXPÉRIENCE UTILISATEUR =====
    
    // Animation des cartes
    const cards = document.querySelectorAll('.card');
    cards.forEach(function(card, index) {
        setTimeout(function() {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 200 * index);
    });
    
    // Empêcher la fermeture accidentelle
    document.querySelectorAll('.modal-content').forEach(function(modalContent) {
        modalContent.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });
    
    // Ajout du CSS pour l'animation de secousse
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }
        
        .required-indicator {
            color: #dc3545 !important;
            font-weight: bold;
            font-size: 1.2em;
        }
    `;
    document.head.appendChild(style);
    
    // Indicateur de progression en temps réel
    document.querySelectorAll('.quiz-question').forEach(function(question) {
        const radioButtons = question.querySelectorAll('input[type="radio"]');
        
        radioButtons.forEach(function(radio) {
            radio.addEventListener('change', function() {
                // Mettre à jour le style quand une réponse est sélectionnée
                const questionElement = this.closest('.quiz-question');
                questionElement.style.borderLeftColor = '#e74c3c';
                questionElement.style.background = 'linear-gradient(145deg, #ffffff, #f8f9fa)';
                questionElement.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.08)';
                
                // Retirer l'indicateur d'erreur si présent
                const indicator = questionElement.querySelector('.required-indicator');
                if (indicator) {
                    indicator.remove();
                }
            });
        });
    });
    
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    
    console.log('Quiz JavaScript avec validation obligatoire chargé !');
});