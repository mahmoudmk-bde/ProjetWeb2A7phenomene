<?php
include '../../controller/utilisateurcontroller.php';
require_once __DIR__ . '/../../Model/utilisateur.php';

$message = "";
$utilisateurc = new utilisateurcontroller();

if (isset($_POST['register_face']) && isset($_POST['user_id']) && isset($_POST['descriptor'])) {
    $userId = $_POST['user_id'];
    $descriptor = $_POST['descriptor']; 
    
    // Validate User ID exists
    $user = $utilisateurc->showUtilisateur($userId);
    if ($user) {
        $result = $utilisateurc->updateFace($userId, $descriptor); // Uses the method we added
        if ($result) {
            $message = "Visage enregistré avec succès pour l'ID $userId !";
        } else {
            $message = "Erreur lors de l'enregistrement dans la base de données.";
        }
    } else {
        $message = "Utilisateur avec ID $userId non trouvé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrer un visage (Test)</title>
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; padding: 20px; background: #f0f2f5; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        video { border-radius: 8px; background: #000; }
        .btn { padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; font-size: 16px; }
        .btn:disabled { background: #ccc; }
        .message { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Enregistrement Facial (Outil de Test)</h2>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 20px;">
            <label for="user_id_input">ID Utilisateur :</label>
            <input type="number" id="user_id_input" placeholder="Ex: 1" style="padding: 8px; width: 100px;">
        </div>

        <div style="position: relative; width: 480px; height: 360px; margin: 0 auto; background: black;">
            <video id="video" width="480" height="360" autoplay muted style="position: absolute; top:0; left:0;"></video>
            <canvas id="overlay" style="position: absolute; top:0; left:0;"></canvas>
        </div>

        <p id="status">Chargement des modèles...</p>
        
        <button id="capture-btn" class="btn" disabled>Capturer et Enregistrer</button>
        <br><br>
        <a href="connexion.php">Retour à la connexion</a>
    </div>

    <script>
        const video = document.getElementById('video');
        const captureBtn = document.getElementById('capture-btn');
        const status = document.getElementById('status');
        const userIdInput = document.getElementById('user_id_input');
        
        // Use CDN models or local fallback
        const MODEL_URL = 'https://justadudewhohacks.github.io/face-api.js/models';

        Promise.all([
            faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
        ]).then(startVideo).catch(err => {
            console.error(err);
            status.innerText = "Erreur chargement modèles API.";
        });

        function startVideo() {
            status.innerText = "Modèles chargés. Accès caméra...";
            navigator.mediaDevices.getUserMedia({ video: {} })
                .then(stream => {
                    video.srcObject = stream;
                    status.innerText = "Prêt. Entrez un ID utilisateur et capturez.";
                })
                .catch(err => status.innerText = "Erreur caméra: " + err);
        }

        video.addEventListener('play', () => {
            const canvas = document.getElementById('overlay');
            const displaySize = { width: video.width, height: video.height };
            faceapi.matchDimensions(canvas, displaySize);

            setInterval(async () => {
                const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                faceapi.draw.drawDetections(canvas, resizedDetections);
                
                if (detections.length > 0) {
                    captureBtn.disabled = userIdInput.value === "";
                    status.innerText = "Visage détecté !";
                } else {
                    captureBtn.disabled = true;
                    status.innerText = "En attente de visage...";
                }
            }, 100);
        });

        captureBtn.addEventListener('click', async () => {
            const userId = userIdInput.value;
            if (!userId) {
                alert("Veuillez entrer un ID utilisateur.");
                return;
            }

            status.innerText = "Capture en cours...";
            try {
                const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
                if (detections.length > 0) {
                    const descriptor = detections[0].descriptor;
                    const descriptorArray = Array.from(descriptor); // Convert to regular array

                    // Send to server
                    const formData = new FormData();
                    formData.append('register_face', '1');
                    formData.append('user_id', userId);
                    formData.append('descriptor', JSON.stringify(descriptorArray));

                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text()) // Get text first to debug if needed
                    .then(html => {
                        // Ideally we'd parse JSON but this is a simple PHP view
                        // Reload page to show PHP message
                        document.open();
                        document.write(html);
                        document.close();
                    });

                } else {
                    alert("Aucun visage détecté lors de la capture.");
                }
            } catch (err) {
                console.error(err);
                alert("Erreur lors de la capture.");
            }
        });
        
        userIdInput.addEventListener('input', () => {
             // Re-evaluate button state
        });
    </script>
</body>
</html>
