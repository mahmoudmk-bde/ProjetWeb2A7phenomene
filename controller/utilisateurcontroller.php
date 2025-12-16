<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../Model/Utilisateur.php');

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

    public function addUtilisateur(Utilisateur $utilisateur) {
        $sql = "INSERT INTO utilisateur 
                (prenom, nom, dt_naiss, mail, num, mdp, typee, q1, rp1, q2, rp2, auth, img, face) 
                VALUES 
                (:prenom, :nom, :dt_naiss, :mail, :num, :mdp, :typee, :q1, :rp1, :q2, :rp2, :auth, :img, :face)";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            
            // Gestion de la date de naissance
            $dt_naiss = $utilisateur->getDtNaiss();
            if ($dt_naiss instanceof DateTime) {
                $dt_naiss = $dt_naiss->format('Y-m-d');
            } else {
                $dt_naiss = null;
            }
            
            $query->execute([
                'prenom' => $utilisateur->getPrenom(),
                'nom' => $utilisateur->getNom(),
                'dt_naiss' => $dt_naiss,
                'mail' => $utilisateur->getMail(),
                'num' => $utilisateur->getNum(),
                'mdp' => $utilisateur->getMdp(),
                'typee' => $utilisateur->getType(),
                'q1' => $utilisateur->getQ1(),
                'rp1' => $utilisateur->getRp1(),
                'q2' => $utilisateur->getQ2(),
                'rp2' => $utilisateur->getRp2(),
                'auth' => $utilisateur->getAuth(),
                'img' => $utilisateur->getImg(),
                'face' => $utilisateur->getFace()
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log('Error adding user: ' . $e->getMessage());
            return false;
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
                    q1 = :q1,
                    rp1 = :rp1,
                    q2 = :q2,
                    rp2 = :rp2,
                    auth = :auth,
                    img = :img,
                    face = :face
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
                'typee' => $utilisateur->getType(),
                'q1' => $utilisateur->getQ1(),
                'rp1' => $utilisateur->getRp1(),
                'q2' => $utilisateur->getQ2(),
                'rp2' => $utilisateur->getRp2(),
                'auth' => $utilisateur->getAuth(),
                'img' => $utilisateur->getImg(),
                'face' => $utilisateur->getFace()
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

    // Méthode pour mettre à jour uniquement le champ auth
    public function updateAuth($user_id, $auth_value) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE utilisateur SET auth = :auth WHERE id_util = :id_util'
            );
            
            $query->execute([
                'id_util' => $user_id,
                'auth' => $auth_value
            ]);
            
            return true;
            
        } catch (PDOException $e) {
            error_log('Error updating auth: ' . $e->getMessage());
            return false;
        }
    }

    // Méthode pour mettre à jour uniquement l'image
    public function updateImage($user_id, $image_path) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE utilisateur SET img = :img WHERE id_util = :id_util'
            );
            
            $query->execute([
                'id_util' => $user_id,
                'img' => $image_path
            ]);
            
            return true;
            
        } catch (PDOException $e) {
            error_log('Error updating image: ' . $e->getMessage());
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

    // Dans utilisateurcontroller.php, ajoutez cette méthode si elle n'existe pas
public function updateProfilePicture($id_util, $image_name) {
    try {
        $sql = "UPDATE utilisateur SET img = :img WHERE id_util = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':img', $image_name);
        $req->bindValue(':id', $id_util);
        return $req->execute();
    } catch (Exception $e) {
        die('Erreur: ' . $e->getMessage());
    }
}
    // Méthode pour Login/Register via Social Media
    public function loginOrRegisterSocial($provider, $uid, $email, $prenom, $nom, $picture_url = null) {
        $db = config::getConnexion();
        
        try {
            // 1. Chercher si l'utilisateur existe déjà avec ce provider/uid
            $sql = "SELECT * FROM utilisateur WHERE oauth_provider = :provider AND oauth_uid = :uid";
            $stmt = $db->prepare($sql);
            $stmt->execute([':provider' => $provider, ':uid' => $uid]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                return $user; // Utilisateur trouvé !
            }
            
            // 2. Si non trouvé, chercher par email (pour lier le compte)
            if (!empty($email)) {
                $sql = "SELECT * FROM utilisateur WHERE mail = :email";
                $stmt = $db->prepare($sql);
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    // Lier le compte existant au compte social
                    $sql = "UPDATE utilisateur SET oauth_provider = :provider, oauth_uid = :uid, img = COALESCE(NULLIF(img, 'default_avatar.jpg'), :img) WHERE id_util = :id";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([
                        ':provider' => $provider, 
                        ':uid' => $uid, 
                        ':img' => $picture_url, // Met à jour l'image si c'était celle par défaut
                        ':id' => $user['id_util']
                    ]);
                    
                    // Re-fetch utilisateur mis à jour
                    $sql = "SELECT * FROM utilisateur WHERE id_util = :id";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([':id' => $user['id_util']]);
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
            
            // 3. Si aucun utilisateur trouvé, créer un nouveau compte
            // Générer un mot de passe aléatoire sécurisé
            $random_password = bin2hex(random_bytes(8)); 
            
            // S'assurer que les champs requis sont remplis
            if (empty($prenom)) $prenom = 'User';
            if (empty($nom)) $nom = 'Social';
            
            // Télécharger l'image si fournie
            $img_filename = 'default_avatar.jpg';
            if ($picture_url) {
                // Ici on pourrait télécharger l'image, mais pour l'instant on garde l'URL ou on met défaut
                // Pour faire simple, on va essayer de lier l'URL si possible ou juste garder default
                // TODO: Implémenter le téléchargement d'image distante si nécessaire
            }

            $sql = "INSERT INTO utilisateur (prenom, nom, mail, mdp, mdp_plain, typee, img, oauth_provider, oauth_uid, auth) 
                    VALUES (:prenom, :nom, :mail, :mdp, :mdp_plain, 'membre', :img, :provider, :uid, 'inactive')";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':prenom' => $prenom,
                ':nom' => $nom,
                ':mail' => $email,
                ':mdp' => password_hash($random_password, PASSWORD_DEFAULT),
                ':mdp_plain' => null, // Pas de plain text
                ':img' => $img_filename,
                ':provider' => $provider,
                ':uid' => $uid
            ]);
            
            $new_id = $db->lastInsertId();
            
            // Retourner le nouvel utilisateur
            $sql = "SELECT * FROM utilisateur WHERE id_util = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $new_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            die('Erreur Social Login: ' . $e->getMessage());
        }
    }

    public function updateFace($id_util, $face) {
        $sql = "UPDATE utilisateur SET face = :face WHERE id_util = :id";
        $db = config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->bindValue(':face', $face);
            $req->bindValue(':id', $id_util);
            return $req->execute();
        } catch (Exception $e) {
            error_log('Error updating face descriptor: ' . $e->getMessage());
            return false;
        }
    }

    public function getAllFaces() {
        $sql = "SELECT id_util, face FROM utilisateur WHERE face IS NOT NULL AND face != ''";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error fetching face descriptors: ' . $e->getMessage());
            return [];
        }
    }
}
?>