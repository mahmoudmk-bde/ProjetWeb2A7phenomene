<?php
require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../model/utilisateur.php';

class UtilisateurController {

    public function listUtilisateurs() {
        $sql = "SELECT * FROM utilisateur";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            error_log('Error listing users: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteUtilisateur($id_util) {
        $sql = "DELETE FROM utilisateur WHERE id_util = :id_util";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id_util', $id_util);
        try {
            return $req->execute();
        } catch (Exception $e) {
            error_log('Error deleting user: ' . $e->getMessage());
            return false;
        }
    }

   public function addUtilisateur(Utilisateur $u)
{
    $sql = "INSERT INTO utilisateur 
        (prenom, nom, dt_naiss, mail, num, mdp, typee, q1, rp1, q2, rp2)
        VALUES 
        (:prenom, :nom, :dt_naiss, :mail, :num, :mdp, :typee, :q1, :rp1, :q2, :rp2)";

    $db = config::getConnexion();

    try {
        $query = $db->prepare($sql);
        $query->execute([
            ':prenom' => $u->getPrenom(),
            ':nom' => $u->getNom(),
            ':dt_naiss' => $u->getDtNaiss(),
            ':mail' => $u->getMail(),
            ':num' => $u->getNum(),
            ':mdp' => $u->getMdp(),
            ':typee' => $u->getTypee(),
            ':q1' => $u->getQ1(),
            ':rp1' => $u->getRp1(),
            ':q2' => $u->getQ2(),
            ':rp2' => $u->getRp2()
        ]);
    } catch (Exception $e) {
        die("Erreur lors de l'ajout : " . $e->getMessage());
    }
}


    public function updateUtilisateur(Utilisateur $utilisateur, $id_util) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE utilisateur SET 
                    prenom = :prenom,
                    nom = :nom,
                    dt_naiss = :dt_naiss,
                    mail = :mail,
                    num = :num,
                    mdp = :mdp,
                    typee = :typee,
                WHERE id_util = :id_util'
            );
            
            // Gestion de la date de naissance
            $dt_naiss = $utilisateur->getDtNaiss();
            if ($dt_naiss instanceof DateTime) {
                $dt_naiss = $dt_naiss->format('Y-m-d');
            } else {
                $dt_naiss = null;
            }
            
            $query->execute([
                'id_util' => $id_util,
                'prenom' => $utilisateur->getPrenom(),
                'nom' => $utilisateur->getNom(),
                'dt_naiss' => $dt_naiss,
                'mail' => $utilisateur->getMail(),
                'num' => $utilisateur->getNum(),
                'mdp' => $utilisateur->getMdp(),
                'typee' => $utilisateur->getType(), // CORRIGÉ: getType() au lieu de getTypee()
                
            ]);
            
            return true;
            
        } catch (PDOException $e) {
            error_log('Error updating user: ' . $e->getMessage());
            return false;
        }
    }

    public function showUtilisateur($id_util) {
        $sql = "SELECT * FROM utilisateur WHERE id_util = :id_util";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id_util', $id_util);

        try {
            $query->execute();
            $utilisateur = $query->fetch();
            return $utilisateur;
        } catch (Exception $e) {
            error_log('Error showing user: ' . $e->getMessage());
            return false;
        }
    }

    public function emailExists($email) {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE mail = :email";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':email', $email);
        
        try {
            $query->execute();
            $count = $query->fetchColumn();
            return $count > 0;
        } catch (Exception $e) {
            error_log('Error checking email: ' . $e->getMessage());
            return false;
        }
    }
    public function login($mail, $mdp) {
    try {
        $sql = "SELECT * FROM utilisateur WHERE mail = :mail AND mdp = :mdp";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute([
            'mail' => $mail,
            'mdp' => $mdp
        ]);
        
        $user = $query->fetch(PDO::FETCH_ASSOC);
        return $user ? $user : false;
        
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}

    public function numExists($num) {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE num = :num";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':num', $num);
        
        try {
            $query->execute();
            $count = $query->fetchColumn();
            return $count > 0;
        } catch (Exception $e) {
            error_log('Error checking number: ' . $e->getMessage());
            return false;
        }
    }

    public function getUtilisateursCount() {
        $sql = "SELECT COUNT(*) as count FROM utilisateur";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $result = $query->fetch();
            return $result['count'];
        } catch (Exception $e) {
            error_log('Error counting users: ' . $e->getMessage());
            return 0;
        }
    }

    // Méthode pour vérifier les réponses aux questions de sécurité
    public function verifySecurityQuestions($user_id, $answer1, $answer2) {
        $sql = "SELECT * FROM utilisateur WHERE id_util = :id_util AND rp1 = :rp1 AND rp2 = :rp2";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute([
            'id_util' => $user_id,
            'rp1' => $answer1,
            'rp2' => $answer2
        ]);
        
        try {
            $user = $query->fetch();
            return $user !== false;
        } catch (Exception $e) {
            error_log('Error verifying security questions: ' . $e->getMessage());
            return false;
        }
    }

    // Méthode pour récupérer un utilisateur par email
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM utilisateur WHERE mail = :email";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':email', $email);
        
        try {
            $query->execute();
            $user = $query->fetch();
            return $user;
        } catch (Exception $e) {
            error_log('Error getting user by email: ' . $e->getMessage());
            return false;
        }
    }

    // Méthode pour mettre à jour uniquement le mot de passe
    public function updatePassword($user_id, $new_password) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE utilisateur SET mdp = :mdp WHERE id_util = :id_util'
            );
            
            $query->execute([
                'id_util' => $user_id,
                'mdp' => $new_password
            ]);
            
            return true;
            
        } catch (PDOException $e) {
            error_log('Error updating password: ' . $e->getMessage());
            return false;
        }
    }

    // Méthode pour récupérer les questions de sécurité d'un utilisateur
    public function getSecurityQuestions($user_id) {
        $sql = "SELECT q1, q2 FROM utilisateur WHERE id_util = :id_util";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id_util', $user_id);
        
        try {
            $query->execute();
            $result = $query->fetch();
            return [
                'q1' => $result['q1'] ?? null,
                'q2' => $result['q2'] ?? null
            ];
        } catch (Exception $e) {
            error_log('Error getting security questions: ' . $e->getMessage());
            return false;
        }
    }
}
?>