// quiz_results.js - Version modifiée pour 10 points par question

// Fonction principale pour calculer et afficher tous les résultats
function calculateAndDisplayAllResults() {
    console.log('Début du calcul des résultats (10 points par question)...');
    console.log('Données reçues:', window.quizData);

    // Vérification plus robuste des données
    if (!window.quizData || !window.quizData.questions || !Array.isArray(window.quizData.questions)) {
        console.error('Données du quiz non trouvées ou invalides');
        showError('Données du quiz manquantes ou invalides');
        return;
    }

    const questions = window.quizData.questions;
    const totalQuestions = questions.length;
    
    if (totalQuestions === 0) {
        console.error('Aucune question trouvée');
        showError('Aucune question trouvée dans le quiz');
        return;
    }

    // Configuration des points
    const POINTS_PER_QUESTION = 10; // CHANGÉ: 10 points par question
    let totalScore = 0;
    let maxScore = totalQuestions * POINTS_PER_QUESTION;
    let correctAnswers = 0;
    let incorrectAnswers = 0;
    let unansweredAnswers = 0;
    let resultsHTML = '';

    console.log('Nombre de questions:', totalQuestions);
    console.log('Score maximum possible:', maxScore, 'points');

    // Calculer le score et générer le HTML des résultats
    questions.forEach(function(question, index) {
        console.log('Question', index + 1, ':', question);
        
        // Vérification des données de la question
        if (!question || typeof question !== 'object') {
            console.warn('Question invalide à l\'index', index);
            return;
        }
        
        const userAnswer = parseInt(question.user_answer);
        const correctAnswer = parseInt(question.correct_answer);
        const isUnanswered = question.is_unanswered || question.user_answer === null || question.user_answer === undefined;
        
        let isCorrect = false;
        let questionStatus = 'incorrect';
        let questionScore = 0;
        
        if (isUnanswered) {
            // Question non répondue - 0 point
            questionStatus = 'unanswered';
            questionScore = 0;
            unansweredAnswers++;
        } else if (!isNaN(userAnswer) && !isNaN(correctAnswer)) {
            isCorrect = userAnswer === correctAnswer;
            questionStatus = isCorrect ? 'correct' : 'incorrect';
            
            if (isCorrect) {
                // Bonne réponse - 10 points
                questionScore = POINTS_PER_QUESTION;
                correctAnswers++;
                totalScore += POINTS_PER_QUESTION;
            } else {
                // Mauvaise réponse - 0 point
                questionScore = 0;
                incorrectAnswers++;
            }
        } else {
            // Réponse invalide
            questionStatus = 'unanswered';
            questionScore = 0;
            unansweredAnswers++;
        }

        console.log(`Question ${index + 1}: ${questionStatus} - Score: ${questionScore}/${POINTS_PER_QUESTION}`);

        // Générer le HTML pour cette question
        resultsHTML += 
            '<div class="question-result ' + questionStatus + '">' +
                '<div class="result-question-header">' +
                    '<div class="result-question">' + 
                        (index + 1) + '. ' + escapeHtml(question.question || 'Question non disponible') +
                    '</div>' +
                    '<div class="question-score">' + questionScore + '/' + POINTS_PER_QUESTION + ' pts</div>' +
                '</div>' +
                '<div class="result-answers">';
        
        // Afficher la réponse de l'utilisateur
        if (isUnanswered) {
            resultsHTML += 
                '<div class="user-answer unanswered">' +
                    '<strong>Votre réponse:</strong> <span class="unanswered-badge">Non répondu (0 point)</span>' +
                '</div>';
        } else {
            resultsHTML += 
                '<div class="user-answer ' + (isCorrect ? 'correct' : 'incorrect') + '">' +
                    '<strong>Votre réponse:</strong> ' + escapeHtml(getAnswerText(question, question.user_answer)) +
                    ' <span class="answer-status">' + (isCorrect ? '(10 points)' : '(0 point)') + '</span>' +
                '</div>';
        }
        
        // Afficher la bonne réponse si ce n'est pas correct ou si c'est non répondu
        if (!isCorrect || isUnanswered) {
            resultsHTML += 
                '<div class="correct-answer">' +
                    '<strong>Bonne réponse:</strong> ' + escapeHtml(getAnswerText(question, question.correct_answer)) +
                '</div>';
        }
        
        resultsHTML += 
                '</div>' +
            '</div>';
    });

    console.log('Résumé final:');
    console.log('- Score total:', totalScore + '/' + maxScore + ' points');
    console.log('- Bonnes réponses:', correctAnswers + '/' + totalQuestions);
    console.log('- Mauvaises réponses:', incorrectAnswers + '/' + totalQuestions);
    console.log('- Non répondues:', unansweredAnswers + '/' + totalQuestions);

    // Mettre à jour l'interface
    updateScoreDisplay(totalScore, maxScore);
    updateResultsMessage(totalScore, maxScore, correctAnswers, totalQuestions, unansweredAnswers);
    updateResultsDetails(resultsHTML);
    createScoreSummary(totalScore, maxScore, correctAnswers, incorrectAnswers, unansweredAnswers, totalQuestions);
    addActionButtons();

    // Animer le score
    animateScore(totalScore, maxScore);
}

// Fonction pour échapper les caractères HTML
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Fonction pour obtenir le texte d'une réponse
function getAnswerText(question, answerIndex) {
    if (answerIndex === null || answerIndex === undefined || isNaN(parseInt(answerIndex))) {
        return 'Non répondu';
    }
    
    const answerNum = parseInt(answerIndex);
    const answers = {
        1: question.reponse1,
        2: question.reponse2, 
        3: question.reponse3
    };
    
    return answers[answerNum] || 'Réponse invalide';
}

// Fonction pour mettre à jour l'affichage du score
function updateScoreDisplay(score, maxScore) {
    const scoreCircle = document.querySelector('.score-circle');
    const scorePercent = document.querySelector('.score-percent');
    
    if (scoreCircle && scorePercent) {
        // Calculer le pourcentage pour l'animation du cercle
        const percentage = maxScore > 0 ? (score / maxScore) * 100 : 0;
        scoreCircle.style.background = `conic-gradient(var(--primary-color) ${percentage}%, var(--border-color) ${percentage}%)`;
        // Afficher le score sous forme X/Y avec "points"
        scorePercent.textContent = score + '/' + maxScore + ' pts';
    }
}

// Fonction pour mettre à jour le message des résultats
function updateResultsMessage(score, maxScore, correctAnswers, totalQuestions, unansweredCount) {
    const scorePercentage = maxScore > 0 ? Math.round((score / maxScore) * 100) : 0;
    
    let title = '';
    let message = '';
    
    if (scorePercentage >= 90) {
        title = 'Félicitations ! 🎉';
        message = 'Excellent ! Vous maîtrisez parfaitement le sujet.';
    } else if (scorePercentage >= 70) {
        title = 'Bravo ! 👏';
        message = 'Très bon travail ! Vous avez de solides connaissances.';
    } else if (scorePercentage >= 50) {
        title = 'Bon travail ! 💪';
        message = 'Pas mal ! Vous avez des bases correctes.';
    } else if (scorePercentage >= 30) {
        title = 'À travailler 📚';
        message = 'Continuez à vous exercer pour améliorer vos connaissances.';
    } else {
        title = 'À revoir 🔄';
        message = 'Ne vous découragez pas ! Relisez l\'article et réessayez.';
    }
    
    // Construire le message détaillé
    let detailedMessage = score + '/' + maxScore + ' points (' + correctAnswers + '/' + totalQuestions + ' bonnes réponses)';
    
    // Ajouter des informations sur les questions non répondues
    if (unansweredCount > 0) {
        detailedMessage += ' - ' + unansweredCount + ' question(s) non répondue(s)';
    }
    
    // Mettre à jour le titre et le message
    const resultsTitle = document.getElementById('resultsTitle');
    const resultsMessage = document.getElementById('resultsMessage');
    
    if (resultsTitle) {
        resultsTitle.textContent = title;
    }
    
    if (resultsMessage) {
        resultsMessage.textContent = detailedMessage + '. ' + message;
    }
}

// Fonction pour mettre à jour les détails des résultats
function updateResultsDetails(html) {
    const resultsDetails = document.getElementById('resultsDetails');
    if (resultsDetails) {
        // Supprimer l'indicateur de chargement
        const loading = resultsDetails.querySelector('.results-loading');
        if (loading) {
            loading.remove();
        }
        
        resultsDetails.innerHTML = html || '<p>Aucun détail disponible.</p>';
        
        // Ajouter une animation d'entrée
        const questionResults = resultsDetails.querySelectorAll('.question-result');
        questionResults.forEach((result, index) => {
            result.style.opacity = '0';
            result.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                result.style.transition = 'all 0.5s ease';
                result.style.opacity = '1';
                result.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }
}

// Fonction pour créer le résumé du score
function createScoreSummary(totalScore, maxScore, correctAnswers, incorrectAnswers, unansweredCount, totalQuestions) {
    const resultsActions = document.getElementById('resultsActions');
    
    if (!resultsActions) return;
    
    const scorePercentage = maxScore > 0 ? Math.round((totalScore / maxScore) * 100) : 0;
    
    const scoreSummaryHTML = 
        '<div class="score-summary">' +
            '<h3>Résumé du Score</h3>' +
            '<div class="score-breakdown">' +
                '<div class="score-item">' +
                    '<span class="score-label">Questions totales:</span>' +
                    '<span class="score-value">' + totalQuestions + '</span>' +
                '</div>' +
                '<div class="score-item">' +
                    '<span class="score-label">Points par question:</span>' +
                    '<span class="score-value">10 points</span>' +
                '</div>' +
                '<div class="score-item">' +
                    '<span class="score-label">Score maximum:</span>' +
                    '<span class="score-value">' + maxScore + ' points</span>' +
                '</div>' +
                '<div class="score-item">' +
                    '<span class="score-label">Bonnes réponses:</span>' +
                    '<span class="score-value correct">' + correctAnswers + ' (' + (correctAnswers * 10) + ' points)</span>' +
                '</div>' +
                '<div class="score-item">' +
                    '<span class="score-label">Mauvaises réponses:</span>' +
                    '<span class="score-value incorrect">' + incorrectAnswers + ' (0 point)</span>' +
                '</div>' +
                '<div class="score-item">' +
                    '<span class="score-label">Non répondues:</span>' +
                    '<span class="score-value">' + unansweredCount + ' (0 point)</span>' +
                '</div>' +
                '<div class="score-item total">' +
                    '<span class="score-label">Score final:</span>' +
                    '<span class="score-value total">' + totalScore + '/' + maxScore + ' points</span>' +
                '</div>' +
                '<div class="score-item">' +
                    '<span class="score-label">Pourcentage:</span>' +
                    '<span class="score-value">' + scorePercentage + '%</span>' +
                '</div>' +
            '</div>' +
        '</div>';
    
    resultsActions.innerHTML = scoreSummaryHTML;
}

// Fonction pour ajouter les boutons d'action
function addActionButtons() {
    const resultsActions = document.getElementById('resultsActions');
    
    if (!resultsActions) return;
    
    const buttonsHTML = 
        '<div class="results-buttons">' +
            '<a href="quiz_page.php?article_id=' + (window.quizData.article_id || '') + '" class="btn-retry">' +
                '🔄 Réessayer le quiz' +
            '</a>' +
            '<a href="index1.php" class="btn-back">' +
                '📚 Retour à l\'accueil' +
            '</a>' +
        '</div>';
    
    resultsActions.innerHTML += buttonsHTML;
}

// Fonction pour animer le score
function animateScore(score, maxScore) {
    const scoreCircle = document.querySelector('.score-circle');
    const scorePercent = document.querySelector('.score-percent');
    
    if (!scoreCircle || !scorePercent) return;
    
    let animatedScore = 0;
    const duration = 2000;
    const steps = 80;
    const increment = score / steps;
    const stepTime = duration / steps;
    
    const timer = setInterval(function() {
        animatedScore += increment;
        if (animatedScore >= score) {
            animatedScore = score;
            clearInterval(timer);
        }
        
        const currentScore = Math.round(animatedScore);
        // Calculer le pourcentage pour l'animation du cercle
        const percentage = maxScore > 0 ? (currentScore / maxScore) * 100 : 0;
        scoreCircle.style.background = `conic-gradient(var(--primary-color) ${percentage}%, var(--border-color) ${percentage}%)`;
        scorePercent.textContent = currentScore + '/' + maxScore + ' pts';
    }, stepTime);
}

// Fonction en cas d'erreur
function showError(message) {
    const resultsTitle = document.getElementById('resultsTitle');
    const resultsMessage = document.getElementById('resultsMessage');
    const resultsDetails = document.getElementById('resultsDetails');
    const resultsActions = document.getElementById('resultsActions');
    
    if (resultsTitle) {
        resultsTitle.textContent = 'Erreur ❌';
    }
    
    if (resultsMessage) {
        resultsMessage.textContent = message || 'Une erreur est survenue lors du calcul des résultats.';
    }
    
    if (resultsDetails) {
        resultsDetails.innerHTML = '<p class="text-center">Veuillez réessayer le quiz.</p>';
    }
    
    if (resultsActions) {
        resultsActions.innerHTML = 
            '<div class="results-buttons">' +
                '<a href="index.php" class="btn-back">Retour à l\'accueil</a>' +
            '</div>';
    }
}

// Vérification que les données sont disponibles avant initialisation
function initializeResults() {
    console.log('Initialisation des résultats...');
    
    if (typeof window.quizData !== 'undefined' && window.quizData.questions) {
        console.log('Données quizData trouvées:', window.quizData);
        // Démarrer immédiatement le calcul
        calculateAndDisplayAllResults();
    } else {
        console.error('quizData non défini ou données manquantes');
        showError('Les données du quiz ne sont pas disponibles.');
    }
}

// Initialisation quand la page est chargée
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page chargée, début de l\'initialisation...');
    initializeResults();
});