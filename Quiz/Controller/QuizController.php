<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../Model/Quiz.php');


class QuizController {

    public function listQuiz() {
        $sql = "SELECT * FROM quiz";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteQuiz($id) {
        $sql = "DELETE FROM quiz WHERE id_quiz = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function addQuiz(Quiz $quiz) {
        $sql = "INSERT INTO quiz VALUES (
                    NULL, 
                    :question, 
                    :reponse1, 
                    :reponse2, 
                    :reponse3, 
                    :bonne_reponse, 
                    :id_article 
                )";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'question'      => $quiz->getQuestion(),
                'reponse1'      => $quiz->getReponse1(),
                'reponse2'      => $quiz->getReponse2(),
                'reponse3'      => $quiz->getReponse3(),
                'bonne_reponse' => $quiz->getBonneReponse(),
                'id_article'    => $quiz->getIdArticle()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function updateQuiz(Quiz $quiz, $id_quiz) {
    try {
        $db = config::getConnexion();
        $query = $db->prepare(
            'UPDATE quiz SET 
                question = :question,
                reponse1 = :reponse1,
                reponse2 = :reponse2,
                reponse3 = :reponse3,
                bonne_reponse = :bonne_reponse,
                id_article = :id_article
            WHERE id_quiz = :id_quiz'
        );
        $query->execute([
            'id_quiz' => $id_quiz,
            'question' => $quiz->getQuestion(),
            'reponse1' => $quiz->getReponse1(),
            'reponse2' => $quiz->getReponse2(),
            'reponse3' => $quiz->getReponse3(),
            'bonne_reponse' => $quiz->getBonneReponse(),
            'id_article' => $quiz->getIdArticle()
        ]);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

    // Fonctions pour la table article
    public function listArticle() {
        $sql = "SELECT * FROM article";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function updateArticle(Article $article, $id_article) {
    try {
        $db = config::getConnexion();
        $query = $db->prepare(
            'UPDATE article SET 
                titre = :titre,
                contenu = :contenu,
                date_publication = :date_publication
            WHERE id_article = :id_article'
        );
        
        // Formater la date en string pour la base de données
        $dateFormatted = $article->getDatePublication()->format('Y-m-d');
        
        $query->execute([
            'id_article' => $id_article,
            'titre' => $article->getTitre(),
            'contenu' => $article->getContenu(),
            'date_publication' => $dateFormatted
        ]);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
    public function getArticleById($id) {
        $sql = "SELECT * FROM article WHERE id_article = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $article = $query->fetch();
            return $article;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }
    public function getQuizById($id) {
        $sql = "SELECT * FROM quiz WHERE id_quiz = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $quiz = $query->fetch();
            return $quiz;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function getArticlesCount() {
        return $this->getTableCount('article');
    }
    
    public function getQuizCount() {
        return $this->getTableCount('quiz');
    }
    
    private function getTableCount($tableName) {
        try {
            $db = config::getConnexion();
            $allowedTables = ['article', 'quiz'];
            if (!in_array($tableName, $allowedTables)) {
                throw new Exception("Table non autorisée: " . $tableName);
            }
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM " . $tableName);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Erreur QuizController - {$tableName}: " . $e->getMessage());
            return 0;
        }
    }
    
public function generateQuizQuestions($quizs, $quizId) {
    if (!empty($quizs)) {
        foreach($quizs as $index => $quiz) {
            echo '
            <div class="question" data-correct-answer="' . $quiz['bonne_reponse'] . '">
                <div class="question-text">' . ($index + 1) . '. ' . $quiz['question'] . '</div>
                <div class="options">
                    <label class="option">
                        <input type="radio" name="q' . ($index + 1) . '" value="1">
                        ' . $quiz['reponse1'] . '
                    </label>
                    <label class="option">
                        <input type="radio" name="q' . ($index + 1) . '" value="2">
                        ' . $quiz['reponse2'] . '
                    </label>
                    <label class="option">
                        <input type="radio" name="q' . ($index + 1) . '" value="3">
                        ' . $quiz['reponse3'] . '
                    </label>
                </div>
            </div>';
        }
    } else {
        echo '<div class="no-quiz">Aucun quiz disponible pour cet article.</div>';
    }
}
     public function getQuizByArticle($id_article) {
    $sql = "SELECT * FROM quiz WHERE id_article = :id_article";
    $db = config::getConnexion();
    
    try {
        $query = $db->prepare($sql);
        $query->execute(['id_article' => $id_article]);
        return $query->fetchAll();
    } catch (Exception $e) {
        die('Erreur: ' . $e->getMessage());
    }
}
}

?>