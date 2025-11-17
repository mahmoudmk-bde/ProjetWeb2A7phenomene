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
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteUtilisateur($id_util) {
        $sql = "DELETE FROM utilisateur WHERE id_util = :id_util";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id_util', $id_util);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function addUtilisateur(Utilisateur $utilisateur) {
        $sql = "INSERT INTO utilisateur VALUES (NULL, :prenom, :nom, :dt_naiss, :mail, :num, :mdp, :typee)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'prenom' => $utilisateur->getPrenom(),
                'nom' => $utilisateur->getNom(),
                'dt_naiss' => $utilisateur->getDtNaiss() ? $utilisateur->getDtNaiss()->format('Y-m-d') : null,
                'mail' => $utilisateur->getMail(),
                'num' => $utilisateur->getNum(),
                'mdp' => $utilisateur->getMdp(),
                'typee' => $utilisateur->gettype()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
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
                    typee= :typee
                WHERE id_util = :id_util'
            );
            $query->execute([
                'id_util' => $id_util,
                'prenom' => $utilisateur->getPrenom(),
                'nom' => $utilisateur->getNom(),
                'dt_naiss' => $utilisateur->getDtNaiss() ? $utilisateur->getDtNaiss()->format('Y-m-d') : null,
                'mail' => $utilisateur->getMail(),
                'num' => $utilisateur->getNum(),
                'mdp' => $utilisateur->getMdp(),
                'typee' => $utilisateur->gettype()
            ]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
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
            die('Error: ' . $e->getMessage());
        }
    }

    // AJOUT: Méthode pour vérifier si l'email existe
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
            die('Error:' . $e->getMessage());
        }
    }

    // AJOUT: Méthode pour vérifier si le numéro existe
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
            die('Error:' . $e->getMessage());
        }
    }
    // Dans la classe UtilisateurController, ajoutez cette méthode :
    public function getUtilisateursCount() {
        $sql = "SELECT COUNT(*) as count FROM utilisateur";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $result = $query->fetch();
            return $result['count'];
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }
}
?>