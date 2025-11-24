<?php
$mission_id = $_GET['mission_id'] ?? null;
if (!$mission_id) {
    header('Location: missionlist.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Postuler – ENGAGE</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/all.css">
<link rel="stylesheet" href="css/mission.css"> 

</head>

<body>
<div id="chatbot-button">💬</div>
<div id="chatbot-box">
    <div id="chatbot-header">ENGAGE Bot</div>
    <div id="chatbot-messages"></div>

    <input type="text" id="chatbot-input" placeholder="Écris ton message...">
</div>

<script src="../js/chatbot.js"></script>

<div class="body_bg">

<header class="main_menu single_page_menu">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="missionlist.php">
                <img src="img/logo.png" alt="logo">
            </a>
        </nav>
    </div>
</header>

<section class="container mt-5 mb-5 col-lg-6">

    <div class="apply-card">

        <h3 class="apply-title">🎮 Postuler à cette mission</h3>

        <form method="POST" action="../../controller/condidaturecontroller.php">

            <input type="hidden" name="mission_id" value="<?= htmlspecialchars($mission_id) ?>">

            <div class="form-modern">
                <input type="text" name="pseudo_gaming" required placeholder=" ">
                <label>Pseudo Gaming</label>
            </div>

            <div class="form-modern">
                <select name="niveau_experience" required>
                    <option value="" disabled selected hidden></option>
                    <option value="débutant">Débutant</option>
                    <option value="intermédiaire">Intermédiaire</option>
                    <option value="avancé">Avancé</option>
                    <option value="expert">Expert</option>
                </select>
                <label>Niveau d'expérience</label>
            </div>

            <div class="form-modern">
                <input type="text" name="disponibilites" required placeholder=" ">
                <label>Disponibilités (ex: 3 soirs / semaine)</label>
            </div>

            <div class="form-modern">
                <input type="email" name="email" required placeholder=" ">
                <label>Email</label>
            </div>

            <button type="submit" class="btn-apply">Envoyer ma candidature</button>

        </form>

    </div>

</section>


</div>

<script src="js/jquery-1.12.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
