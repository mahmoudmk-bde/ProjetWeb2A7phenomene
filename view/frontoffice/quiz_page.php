<?php
session_start();

// Inclure les fichiers nécessaires pour récupérer les données utilisateur
if (isset($_SESSION['user_id'])) {
    include '../../controller/utilisateurcontroller.php';
    $utilisateurController = new UtilisateurController();
    $user_id = $_SESSION['user_id'];
    $current_user = $utilisateurController->showUtilisateur($user_id);
    
    // Récupérer la photo de profil depuis la base de données
    $profile_picture = $current_user['img'] ?? 'default_avatar.jpg';
    $_SESSION['profile_picture'] = $profile_picture;
}

// Inclure les fichiers pour les articles de quiz
include_once __DIR__ . '/../../Controller/QuizController.php';
$quizController = new QuizController();

// Validation sécurisée de l'ID
$article_id = filter_var($_GET['article_id'] ?? null, FILTER_VALIDATE_INT);
if (!$article_id || $article_id <= 0) {
    $_SESSION['error'] = "ID d'article invalide.";
    header('Location: index.php');
    exit;
}

$article = $quizController->getArticleById($article_id);
$questions = $quizController->getQuizByArticle($article_id);

if (!$article || empty($questions)) {
    $_SESSION['error'] = "L'article ou le quiz demandé n'existe pas.";
    header('Location: index.php');
    exit;
}

function getThemeImage($titre) {
    $titre = strtolower(trim($titre));
    
    $images = [
        "sport" => "sport.png",
        "football" => "tournoi.png",
        "fifa" => "tournoi.png",
        "éducation" => "education.png",
        "education" => "education.png",
        "esport" => "valorant.png",
        "valorant" => "valorant.png",
        "minecraft" => "minecraft.png",
        "création" => "roblox.png",
        "creation" => "roblox.png",
        "prévention" => "sante.png",
        "prevention" => "sante.png",
        "coaching" => "coaching.png",
        "compétition" => "cyber.png",
        "competition" => "cyber.png",
    ];

    foreach ($images as $theme => $image) {
        if (strpos($titre, $theme) !== false) {
            return $image;
        }
    }

    return "default.png";
}

$imageUrl = "image/" . getThemeImage($article['titre'] ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <title>Engage - Plateforme de Quiz Éducatifs</title>
    <link rel="icon" href="assets/img/favicon.png" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <!-- animate CSS -->
    <link rel="stylesheet" href="assets/css/animate.css" />
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css" />
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="assets/css/all.css" />
    <!-- flaticon CSS -->
    <link rel="stylesheet" href="assets/css/flaticon.css" />
    <link rel="stylesheet" href="assets/css/themify-icons.css" />
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="assets/css/magnific-popup.css" />
    <!-- swiper CSS -->
    <link rel="stylesheet" href="assets/css/slick.css" />
    <!-- style CSS -->
    <link rel="stylesheet" href="assets/css/style.css" />
    
    <style>
      /* Styles pour le menu utilisateur */
      .user-menu {
        position: relative;
        display: inline-block;
      }
      
      .user-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        min-width: 180px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        border-radius: 5px;
        z-index: 1000;
        margin-top: 10px;
      }
      
      .user-dropdown.show {
        display: block;
      }
      
      .user-dropdown a {
        display: block;
        padding: 12px 16px;
        text-decoration: none;
        color: #333;
        border-bottom: 1px solid #eee;
        transition: background 0.3s;
        font-size: 14px;
      }
      
      .user-dropdown a:hover {
        background: #f8f9fa;
        color: #007bff;
      }
      
      .user-dropdown a:last-child {
        border-bottom: none;
        color: #dc3545;
      }
      
      .user-dropdown a:last-child:hover {
        background: #dc3545;
        color: white;
      }
      
      .user-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        color: white;
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 25px;
        transition: all 0.3s ease;
      }
      
      .user-wrapper:hover {
        background: rgba(255,255,255,0.1);
      }
      
      .user-name {
        font-weight: bold;
        font-size: 14px;
      }
      
      .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid rgba(255,255,255,0.3);
        transition: all 0.3s ease;
        overflow: hidden;
      }
      
      .user-avatar:hover {
        border-color: rgba(255,255,255,0.6);
        transform: scale(1.05);
      }
      
      .user-avatar i {
        color: white;
        font-size: 18px;
      }
      
      .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
      }
    </style>
    <link rel="stylesheet" href="quiz_pagee.css">
    <link rel="stylesheet" href="general.css">
    
    <!-- Styles pour les aides -->
    <style>
        /* Panneau d'aides */
        .quiz-help-panel {
            position: fixed;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding: 20px;
            background: rgba(26, 27, 30, 0.95);
            border-radius: 15px;
            border: 2px solid var(--primary-color);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
        }
        
        .help-title {
            color: white;
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .help-title i {
            color: var(--primary-color);
        }
        
        .help-button {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            color: white;
        }
        
        .help-button:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        
        .help-button.used {
            opacity: 0.5;
            cursor: not-allowed;
            filter: grayscale(0.8);
            pointer-events: none;
        }
        
        .help-button.used:hover {
            transform: none;
            box-shadow: none;
        }
        
        .help-button.help-1 {
            border-color: #4dabf7;
            background: linear-gradient(135deg, #228be6, #4dabf7);
        }
        
        .help-button.help-2 {
            border-color: #ffa94d;
            background: linear-gradient(135deg, #fd7e14, #ffa94d);
        }
        
        .help-button.help-3 {
            border-color: #20c997;
            background: linear-gradient(135deg, #12b886, #20c997);
        }
        
        .help-button-tooltip {
            position: absolute;
            right: 80px;
            background: white;
            color: #333;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            z-index: 1001;
        }
        
        .help-button-tooltip::after {
            content: '';
            position: absolute;
            right: -6px;
            top: 50%;
            transform: translateY(-50%);
            border-width: 6px 0 6px 6px;
            border-style: solid;
            border-color: transparent transparent transparent white;
        }
        
        .help-button:hover .help-button-tooltip {
            opacity: 1;
            visibility: visible;
        }
        
        .help-button.used .help-button-tooltip {
            display: none;
        }
        
        .help-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            border: 2px solid rgba(26, 27, 30, 0.95);
        }

        /* Styles pour les toasts des aides */
        .help-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 300px;
            max-width: 400px;
            z-index: 10001;
            animation: slideInRight 0.3s ease;
            border-left: 4px solid;
        }

        .help-toast.success {
            border-left-color: #28a745;
        }

        .help-toast.warning {
            border-left-color: #ffc107;
        }

        .help-toast.info {
            border-left-color: #17a2b8;
        }

        .help-toast-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .help-toast.success .help-toast-icon {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .help-toast.warning .help-toast-icon {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .help-toast.info .help-toast-icon {
            background: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }

        .help-toast-message {
            flex: 1;
        }

        .help-toast-message h4 {
            margin: 0 0 5px 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .help-toast-message p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        /* Animation pour les toasts */
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
        
        /* =========================================== */
        /* STYLES AMÉLIORÉS POUR LE PUZZLE */
        /* =========================================== */

        /* Puzzle modal amélioré */
        .puzzle-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(5px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .puzzle-modal.active {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .puzzle-content {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.5s ease;
            border: 2px solid var(--primary-color);
            position: relative;
        }

        .puzzle-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), #ff6b7a, #4dabf7, #20c997);
            border-radius: 20px 20px 0 0;
        }

        /* Header du puzzle */
        .puzzle-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .puzzle-title {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
            background: linear-gradient(45deg, #ff6b7a, var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .puzzle-title i {
            color: #4dabf7;
            font-size: 2rem;
        }

        .close-puzzle {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .close-puzzle:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: rotate(90deg);
            box-shadow: 0 0 15px rgba(255, 74, 87, 0.5);
        }

        /* Corps du puzzle */
        .puzzle-body {
            margin-bottom: 30px;
        }

        .puzzle-instructions {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #4dabf7;
            backdrop-filter: blur(10px);
        }

        .puzzle-instructions h4 {
            color: #4dabf7;
            font-size: 1.3rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .puzzle-instructions p {
            color: #cbd5e0;
            line-height: 1.6;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .puzzle-instructions p:last-child {
            margin-bottom: 0;
        }

        /* Conteneur du jeu de puzzle */
        .puzzle-game-container {
            display: flex;
            flex-direction: column;
            gap: 25px;
            align-items: center;
        }

        /* Zone de réponse */
        .puzzle-answer-area {
            background: rgba(255, 255, 255, 0.03);
            padding: 25px;
            border-radius: 15px;
            border: 2px dashed rgba(255, 255, 255, 0.2);
            min-height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .puzzle-answer-area::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(77, 171, 247, 0.1), transparent);
            opacity: 0.5;
        }

        /* Cases cibles avec nouvelles couleurs */
        .puzzle-targets {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        .puzzle-target-slot {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(255, 74, 87, 0.1), rgba(255, 107, 122, 0.05));
            border: 2px solid rgba(255, 74, 87, 0.3);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 800;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .puzzle-target-slot::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.05));
            border-radius: 10px;
        }

        .puzzle-target-slot.drag-over {
            background: linear-gradient(135deg, rgba(77, 171, 247, 0.2), rgba(32, 201, 151, 0.1));
            border-color: #4dabf7;
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(77, 171, 247, 0.4);
        }

        .puzzle-target-slot.correct {
            background: linear-gradient(135deg, rgba(32, 201, 151, 0.3), rgba(32, 201, 151, 0.15));
            border-color: #20c997;
            color: #20c997;
            animation: pulseSuccess 0.6s ease;
            box-shadow: 0 5px 20px rgba(32, 201, 151, 0.3);
        }

        .puzzle-target-slot.wrong {
            background: linear-gradient(135deg, rgba(245, 101, 101, 0.3), rgba(220, 53, 69, 0.15));
            border-color: #f56565;
            animation: shakeError 0.5s ease;
            box-shadow: 0 5px 20px rgba(245, 101, 101, 0.3);
        }

        /* Conteneur des pièces */
        .puzzle-pieces-container {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            backdrop-filter: blur(10px);
        }

        .puzzle-pieces {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        /* Pièces du puzzle avec nouvelles couleurs */
        .puzzle-piece-letter {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #4dabf7, #339af0);
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            cursor: grab;
            user-select: none;
            transition: all 0.3s ease;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .puzzle-piece-letter::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.1));
            border-radius: 9px;
        }

        .puzzle-piece-letter:hover {
            transform: translateY(-5px) scale(1.05);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 25px rgba(77, 171, 247, 0.4);
        }

        .puzzle-piece-letter.dragging {
            opacity: 0.8;
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 10px 30px rgba(77, 171, 247, 0.6);
            z-index: 100;
        }

        .puzzle-piece-letter.used {
            opacity: 0.4;
            cursor: not-allowed;
            background: linear-gradient(135deg, #718096, #4a5568);
            border-color: #718096;
            transform: none;
            box-shadow: none;
        }

        /* Slot d'espace (invisible) */
        .space-slot {
            background: transparent !important;
            border: none !important;
            width: 25px !important;
            pointer-events: none;
        }

        /* Indice du puzzle */
        .puzzle-hint {
            color: #a0aec0;
            font-size: 1rem;
            text-align: center;
            margin-top: 15px;
            font-style: italic;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.05);
            padding: 12px 20px;
            border-radius: 10px;
            width: 100%;
        }

        .puzzle-hint i {
            color: #ff6b7a;
            font-size: 1.2rem;
        }

        /* Écran de puzzle complété */
        .puzzle-complete {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 20px;
            border: 2px solid rgba(32, 201, 151, 0.3);
            animation: slideUp 0.5s ease;
        }

        .puzzle-complete i {
            font-size: 5rem;
            color: #20c997;
            margin-bottom: 25px;
            animation: bounceSuccess 1s infinite;
        }

        @keyframes bounceSuccess {
            0%, 100% { 
                transform: translateY(0) scale(1); 
                text-shadow: 0 0 0 rgba(32, 201, 151, 0.5);
            }
            50% { 
                transform: translateY(-15px) scale(1.1); 
                text-shadow: 0 10px 20px rgba(32, 201, 151, 0.7);
            }
        }

        .puzzle-complete h3 {
            color: white;
            font-size: 2rem;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #20c997, #4dabf7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .puzzle-complete p {
            color: #cbd5e0;
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 1.1rem;
        }

        .puzzle-complete-message {
            background: rgba(32, 201, 151, 0.1);
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid #20c997;
            margin-top: 25px;
        }

        .puzzle-complete-message h4 {
            color: #20c997;
            font-size: 1.3rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .puzzle-complete-message p {
            color: #a0aec0;
            margin-bottom: 0;
            font-size: 1rem;
        }

        /* Pied du puzzle */
        .puzzle-footer {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid rgba(255, 255, 255, 0.1);
        }

        .btn-puzzle {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 160px;
            justify-content: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-puzzle-solve {
            background: linear-gradient(135deg, #20c997, #12b886);
            color: white;
            border: 2px solid transparent;
        }

        .btn-puzzle-solve:hover {
            background: linear-gradient(135deg, #12b886, #20c997);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(32, 201, 151, 0.4);
            border-color: #20c997;
        }

        .btn-puzzle-solve:active {
            transform: translateY(-1px);
        }

        .btn-puzzle-cancel {
            background: linear-gradient(135deg, #718096, #4a5568);
            color: white;
            border: 2px solid transparent;
        }

        .btn-puzzle-cancel:hover {
            background: linear-gradient(135deg, #4a5568, #718096);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(113, 128, 150, 0.4);
            border-color: #718096;
        }

        /* Animations */
        @keyframes pulseSuccess {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }

        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive pour le puzzle */
        @media (max-width: 768px) {
            .puzzle-content {
                padding: 25px;
                width: 95%;
                max-height: 90vh;
            }
            
            .puzzle-title {
                font-size: 1.5rem;
            }
            
            .puzzle-target-slot {
                width: 50px;
                height: 50px;
                font-size: 1.6rem;
            }
            
            .puzzle-piece-letter {
                width: 50px;
                height: 50px;
                font-size: 1.4rem;
            }
            
            .btn-puzzle {
                padding: 12px 20px;
                min-width: 140px;
                font-size: 0.9rem;
            }
            
            .puzzle-complete i {
                font-size: 4rem;
            }
            
            .puzzle-complete h3 {
                font-size: 1.6rem;
            }
        }

        @media (max-width: 480px) {
            .puzzle-content {
                padding: 20px;
            }
            
            .puzzle-title {
                font-size: 1.3rem;
                gap: 10px;
            }
            
            .puzzle-target-slot {
                width: 45px;
                height: 45px;
                font-size: 1.4rem;
            }
            
            .puzzle-piece-letter {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
            }
            
            .puzzle-footer {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn-puzzle {
                width: 100%;
                min-width: auto;
            }
        }
    </style>
  </head>

  <body>
    <div class="body_bg">
      <!--::header part start::-->
      <header class="main_menu single_page_menu">
        
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-12">
              <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="index.html">
                  <img src="img/logo.png" alt="logo" />
                </a>
                <button
                  class="navbar-toggler"
                  type="button"
                  data-toggle="collapse"
                  data-target="#navbarSupportedContent"
                  aria-controls="navbarSupportedContent"
                  aria-expanded="false"
                  aria-label="Toggle navigation"
                >
                  <span class="menu_icon"><i class="fas fa-bars"></i></span>
                </button>

                <div
                  class="collapse navbar-collapse main-menu-item"
                  id="navbarSupportedContent"
                >
                  <ul class="navbar-nav">
                    <li class="nav-item">
                      <a class="nav-link" href="index.html">Home</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="fighter.html">mission</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="team.html">gamification</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="team.html">reclamation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="team.html">evenement</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="team.html">education</a>
                      </li>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="contact.html">Contact</a>
                    </li>
                  </ul>
                </div>
                <?php
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {
                    // Utilisateur connecté - afficher le menu utilisateur
                    ?>
                    <div class="user-menu d-none d-sm-block">
                      <div class="user-wrapper" onclick="toggleUserMenu()">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <div class="user-avatar">
                          <?php if (isset($profile_picture) && !empty($profile_picture) && $profile_picture !== 'default_avatar.jpg'): ?>
                            <img src="assets/uploads/profiles/<?php echo htmlspecialchars($profile_picture); ?>" 
                                 alt="Photo de profil" 
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                            <i class="fas fa-user" style="display: none"></i>
                          <?php else: ?>
                            <i class="fas fa-user"></i>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="user-dropdown" id="userDropdown">
                        <a href="profile.php">
                          <i class="fas fa-user me-2"></i>Mon Profil
                        </a>
                        <a href="settings.php">
                          <i class="fas fa-cog me-2"></i>Paramètres
                        </a>
                        <a href="historiaue_user.php">
                          <i class="fas fa-history"></i> Mon Historique
                        </a>
                        <a href="logout.php">
                          <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                        </a>
                      </div>
                    </div>
                    <?php
                } else {
                    // Utilisateur non connecté - afficher le bouton de connexion
                    ?>
                    <a href="connexion.php" class="btn_1 d-none d-sm-block">se connecter</a>
                    <?php
                }
                ?>
              </nav>
            </div>
          </div>
        </div>
      </header>

      <!-- Section des articles de quiz -->
       <section class="banner_part">
    <div class="container1">
        
        <!-- Messages d'erreur -->
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="quiz-section">
            <div class="quiz-header">
                <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($article['titre']) ?>" class="article-image">
                <h2>Quiz : <?= htmlspecialchars($article['titre']) ?></h2>
                <p>Testez vos connaissances avec ce quiz</p>
                
                <!-- Panneau d'aides -->
                <div class="quiz-help-panel">
                    <div class="help-title">
                        <i class="fas fa-lightbulb"></i>
                        Aides disponibles
                    </div>
                    <div class="help-button help-1" id="help1" onclick="useHelp('1')">
                        <i class="fas fa-eye"></i>
                        <span class="help-count">1</span>
                        <div class="help-button-tooltip">
                            Révéler une mauvaise réponse
                        </div>
                    </div>
                    <div class="help-button help-2" id="help2" onclick="useHelp('2')">
                        <i class="fas fa-clock"></i>
                        <span class="help-count">1</span>
                        <div class="help-button-tooltip">
                            +30 secondes de temps
                        </div>
                    </div>
                    <div class="help-button help-3" id="help3" onclick="useHelp('3')">
                        <i class="fas fa-puzzle-piece"></i>
                        <span class="help-count">1</span>
                        <div class="help-button-tooltip">
                            Puzzle pour la bonne réponse
                        </div>
                    </div>
                </div>
                
                <div class="quiz-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <span class="progress-text" id="progressText">1/<?= count($questions) ?></span>
                </div>
            </div>

            <form id="quizForm" method="POST" action="quiz_results.php">
                <input type="hidden" name="article_id" value="<?= $article_id ?>">
                <input type="hidden" name="total_questions" value="<?= count($questions) ?>">
                <input type="hidden" name="time_spent" id="timeSpent" value="0">
                <input type="hidden" name="helps_used" id="helpsUsed" value="">
                
                <div id="quizContainer">
                    <?php foreach($questions as $index => $question): ?>
                    <div class="question-card <?= $index === 0 ? 'active' : '' ?>" id="question-<?= $index + 1 ?>" 
     style="<?= $index === 0 ? 'display: block;' : 'display: none;' ?>">
    
    <!-- TIMER DE QUESTION -->
    <div class="question-timer">
        <div class="timer-display">
            <i class="fas fa-hourglass-start timer-icon"></i>
            <span class="question-timer-display">00:30</span>
        </div>
      
    </div>
    
    <div class="question-header">
        <span class="question-number">Question <?= $index + 1 ?></span>
        <span class="question-points">10 points</span> <!-- Changé de 1 à 10 -->
        <input type="hidden" name="questions[<?= $index ?>][correct_answer]" value="<?= $question['bonne_reponse'] ?>">
    </div>
                        
    <div class="question-text">
        <?= htmlspecialchars($question['question_text'] ?? $question['question']) ?>
    </div>

    <input type="hidden" name="questions[<?= $index ?>][id]" value="<?= $question['id_quiz'] ?>">
    <input type="hidden" name="questions[<?= $index ?>][correct]" value="<?= $question['bonne_reponse'] ?>">
    <input type="hidden" name="questions[<?= $index ?>][text]" value="<?= htmlspecialchars($question['question_text'] ?? $question['question']) ?>">

    <div class="options-container">
        <?php 
        $options = [
            $question['reponse1'],
            $question['reponse2'], 
            $question['reponse3']
        ];
        ?>
        <?php foreach($options as $key => $option): ?>
            <?php if (!empty(trim($option))): ?>
                <label class="option-label" id="option-<?= $index + 1 ?>-<?= $key + 1 ?>" 
                       data-correct="<?= ($key + 1) == $question['bonne_reponse'] ? 'true' : 'false' ?>">
                    <input type="radio" 
                          name="answers[<?= $question['id_quiz'] ?>]" 
                          value="<?= $key + 1 ?>"
                          class="option-input"
                          data-question="<?= $index + 1 ?>"
                          required>
                    <span class="option-text">
                        <span class="option-letter"><?= chr(65 + $key) ?></span>
                        <?= htmlspecialchars($option) ?>
                    </span>
                </label>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
                    <?php endforeach; ?>
                </div>

                <div class="navigation-buttons">
                    <button type="button" class="btn-nav btn-prev" id="prevBtn" style="display: none;">
                        ← Précédent
                    </button>
                    <span class="current-question-indicator" id="currentQuestion">Question 1 sur <?= count($questions) ?></span>
                    <button type="button" class="btn-nav btn-next" id="nextBtn">
                        Suivant →
                    </button>
                    <button type="submit" class="btn-nav btn-submit" id="submitBtn" style="display: none;">
                        Terminer le quiz
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

      <!-- Modal pour le puzzle -->
      <div class="puzzle-modal" id="puzzleModal">
          <div class="puzzle-content">
              <div class="puzzle-header">
                  <div class="puzzle-title">
                      <i class="fas fa-puzzle-piece"></i>
                      Puzzle - Découvrez la réponse
                  </div>
                  <button class="close-puzzle" onclick="closePuzzle()">&times;</button>
              </div>
              <div class="puzzle-body" id="puzzleBody">
                  <!-- Le contenu du puzzle sera généré ici -->
              </div>
              <div class="puzzle-footer">
                  <button class="btn-puzzle btn-puzzle-cancel" onclick="closePuzzle()">
                      Annuler
                  </button>
                  <button class="btn-puzzle btn-puzzle-solve" id="solvePuzzleBtn" onclick="solvePuzzle()">
                      Résoudre le puzzle
                  </button>
              </div>
          </div>
      </div>

      <!-- Footer part -->
      <footer class="footer_part">
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <div class="single_footer_part">
                        <a href="index.html" class="footer_logo_iner">
                            <img src="img/logo.png" alt="logo">
                        </a>
                        <p>Engage - La plateforme de matchmaking pour le volontariat par le jeu vidéo</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="single_footer_part">
                        <h4>Contact Info</h4>
                        <p>Adresse : Tunis, Tunisie</p>
                        <p>Téléphone : +216 XX XXX XXX</p>
                        <p>Email : contact@engage.tn</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="single_footer_part">
                        <h4>Liens Importants</h4>
                        <ul class="list-unstyled">
                            <li><a href="store.html">Store</a></li>
                            <li><a href="partenaires.html">Partenaires</a></li>
                            <li><a href="missions.html">Missions</a></li>
                            <li><a href="evenements.html">Événements</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="single_footer_part">
                        <h4>Newsletter</h4>
                        <p>Inscrivez-vous pour recevoir nos nouveautés</p>
                        <div id="mc_embed_signup">
                            <form action="#" method="get" class="subscribe_form relative mail_part">
                                <input type="email" name="email" placeholder="Adresse Email" 
                                       class="placeholder hide-on-focus">
                                <button type="submit" class="email_icon newsletter-submit">
                                    <i class="far fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copygight_text">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="copyright_text">
                        <p>© <script>document.write(new Date().getFullYear());</script> Engage. Tous droits réservés</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="footer_icon social_icon">
                        <ul class="list-unstyled">
                            <li><a href="#" class="single_social_icon"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#" class="single_social_icon"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#" class="single_social_icon"><i class="fab fa-instagram"></i></a></li>
                            <li><a href="#" class="single_social_icon"><i class="fab fa-linkedin"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
    </div>

    <!-- Scripts JavaScript -->
    <script src="assets/js/jquery-1.12.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.js"></script>
    <script src="assets/js/swiper.min.js"></script>
    <script src="assets/js/masonry.pkgd.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.nice-select.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/contact.js"></script>
    <script src="assets/js/jquery.ajaxchimp.min.js"></script>
    <script src="assets/js/jquery.form.js"></script>
    <script src="assets/js/jquery.validate.min.js"></script>
    <script src="assets/js/mail-script.js"></script>
    <script src="assets/js/custom.js"></script>
    
    <script>
    // Déclarer article_id comme variable globale pour les aides
    const article_id = <?= $article_id ?>;
    
    // Variables globales pour les aides - RÉINITIALISÉES À CHAQUE QUIZ
    let helps = {
        '1': { used: false, count: 1 },
        '2': { used: false, count: 1 },
        '3': { used: false, count: 1 }
    };
    
    let helpsUsedList = [];
    
    // Fonction pour utiliser une aide - UNE SEULE FOIS PAR QUIZ
    function useHelp(helpId) {
        const helpButton = document.getElementById(`help${helpId}`);
        
        // Vérifier si l'aide est déjà utilisée DANS CETTE SESSION DE QUIZ
        if (helps[helpId].used || helps[helpId].count <= 0) {
            showToast('Cette aide a déjà été utilisée dans ce quiz !', 'warning');
            return;
        }
        
        // Vérifier si une question est active
        const activeQuestion = document.querySelector('.question-card.active');
        if (!activeQuestion) {
            showToast('Veuillez sélectionner une question d\'abord', 'warning');
            return;
        }
        
        // Décrémenter le compte et marquer comme utilisée
        helps[helpId].count = 0;
        helps[helpId].used = true;
        
        // Ajouter à la liste (une seule fois par quiz)
        if (!helpsUsedList.includes(helpId)) {
            helpsUsedList.push(helpId);
        }
        
        // Mettre à jour l'affichage du bouton
        updateHelpButton(helpButton, helpId);
        
        // Mettre à jour le champ caché
        document.getElementById('helpsUsed').value = helpsUsedList.join(',');
        
        // Exécuter l'aide spécifique
        switch(helpId) {
            case '1':
                useHelpRevealWrong();
                break;
            case '2':
                useHelpExtraTime();
                break;
            case '3':
                useHelpPuzzle();
                break;
        }
        
        // Animation sur le bouton
        helpButton.style.animation = 'none';
        setTimeout(() => {
            helpButton.style.animation = 'helpUsed 0.5s ease';
        }, 10);
        
        showToast('Aide utilisée avec succès !', 'success');
    }
    
    // Fonction pour mettre à jour l'apparence du bouton d'aide
    function updateHelpButton(button, helpId) {
        button.classList.add('used');
        
        const countElement = button.querySelector('.help-count');
        if (countElement) {
            countElement.textContent = helps[helpId].count;
            if (helps[helpId].count === 0) {
                countElement.style.display = 'none';
            }
        }
        
        // Désactiver le tooltip
        const tooltip = button.querySelector('.help-button-tooltip');
        if (tooltip) {
            tooltip.style.display = 'none';
        }
        
        // Désactiver le bouton
        button.style.pointerEvents = 'none';
        button.style.opacity = '0.6';
    }
    
    // Aide 1: Révéler une mauvaise réponse
    function useHelpRevealWrong() {
        const activeQuestion = document.querySelector('.question-card.active');
        if (!activeQuestion) {
            showToast('Aucune question active', 'warning');
            return;
        }
        
        const questionId = activeQuestion.id;
        const questionNumber = parseInt(questionId.replace('question-', ''));
        
        // Utiliser la fonction globale
        if (window.quizManager && window.quizManager.revealWrongAnswer) {
            const success = window.quizManager.revealWrongAnswer(questionNumber);
            if (success) {
                showToast('Une mauvaise réponse a été révélée !', 'info');
            } else {
                showToast('Aucune mauvaise réponse à révéler', 'info');
            }
        } else {
            // Fallback si la fonction globale n'est pas disponible
            const incorrectOptions = activeQuestion.querySelectorAll('.option-label[data-correct="false"]:not(.selected)');
            
            if (incorrectOptions.length > 0) {
                const randomIndex = Math.floor(Math.random() * incorrectOptions.length);
                const optionToReveal = incorrectOptions[randomIndex];
                
                optionToReveal.style.animation = 'none';
                setTimeout(() => {
                    optionToReveal.style.animation = 'shake 0.5s ease';
                    
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
                    
                    setTimeout(() => {
                        if (overlay.parentNode) {
                            overlay.remove();
                        }
                    }, 3000);
                }, 10);
                
                showToast('Une mauvaise réponse a été révélée !', 'info');
            }
        }
    }
    
    // Aide 2: +30 secondes de temps
    function useHelpExtraTime() {
        const activeQuestion = document.querySelector('.question-card.active');
        if (!activeQuestion) {
            showToast('Aucune question active', 'warning');
            return;
        }
        
        const questionId = activeQuestion.id;
        const questionNumber = parseInt(questionId.replace('question-', ''));
        
        // Utiliser la fonction globale
        if (window.quizManager && window.quizManager.addTime) {
            const success = window.quizManager.addTime(questionNumber, 30);
            if (success) {
                showToast('+30 secondes ajoutées !', 'success');
                return;
            }
        }
        
        // Fallback
        const timerDisplay = activeQuestion.querySelector('.question-timer-display');
        if (timerDisplay) {
            const currentTime = timerDisplay.textContent;
            const [minutes, seconds] = currentTime.split(':').map(Number);
            const totalSeconds = minutes * 60 + seconds + 30;
            
            const newMinutes = Math.floor(totalSeconds / 60);
            const newSeconds = totalSeconds % 60;
            timerDisplay.textContent = `${newMinutes.toString().padStart(2, '0')}:${newSeconds.toString().padStart(2, '0')}`;
            
            // Animation
            const timerContainer = timerDisplay.parentElement;
            timerContainer.style.animation = 'none';
            setTimeout(() => {
                timerContainer.style.animation = 'timeAdded 1s ease';
                timerContainer.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
                timerContainer.style.borderColor = 'rgba(40, 167, 69, 0.3)';
                
                setTimeout(() => {
                    timerContainer.style.animation = '';
                    timerContainer.style.backgroundColor = '';
                    timerContainer.style.borderColor = '';
                }, 1000);
            }, 10);
            
            showToast('+30 secondes ajoutées !', 'success');
        }
    }
    
    // Aide 3: Puzzle pour la bonne réponse
    function useHelpPuzzle() {
        const activeQuestion = document.querySelector('.question-card.active');
        if (!activeQuestion) {
            showToast('Aucune question active', 'warning');
            return;
        }
        
        // Récupérer la bonne réponse
        const correctOption = activeQuestion.querySelector('.option-label[data-correct="true"]');
        if (!correctOption) {
            showToast('Impossible de trouver la bonne réponse', 'warning');
            return;
        }
        
        // Récupérer le texte de la bonne réponse
        const optionText = correctOption.querySelector('.option-text');
        let answerText = optionText ? optionText.textContent : '';
        
        // Nettoyer le texte (enlever la lettre de l'option)
        answerText = answerText.replace(/^[A-Z]\.\s*/, '').trim();
        
        if (answerText.length === 0) {
            showToast('La réponse est vide', 'warning');
            return;
        }
        
        // Ouvrir le modal du puzzle
        openPuzzleModal(answerText);
    }
    
    // Ouvrir le modal du puzzle - VERSION CORRIGÉE
    function openPuzzleModal(answerText) {
        const modal = document.getElementById('puzzleModal');
        const puzzleBody = document.getElementById('puzzleBody');
        
        // Nettoyer le texte
        answerText = answerText.trim();
        
        console.log('Ouverture du puzzle avec la réponse:', answerText);
        
        // Créer le contenu du puzzle
        puzzleBody.innerHTML = `
            <div class="puzzle-instructions">
                <h4><i class="fas fa-info-circle me-2"></i>Instructions</h4>
                <p>Assemblez les pièces du puzzle pour découvrir la bonne réponse. 
                   Glissez-déposez les pièces dans l'ordre correct pour reconstituer le mot.</p>
                <p><strong>Conseil :</strong> Cliquez sur "Résoudre le puzzle" si vous avez des difficultés.</p>
            </div>
            
            <div class="puzzle-game-container">
                <div class="puzzle-answer-area">
                    <div class="puzzle-targets" id="puzzleTargets">
                        <!-- Cases cibles seront ajoutées ici -->
                    </div>
                </div>
                
                <div class="puzzle-pieces-container">
                    <div class="puzzle-pieces" id="puzzlePieces">
                        <!-- Pièces du puzzle seront ajoutées ici -->
                    </div>
                </div>
                
                <p class="puzzle-hint">
                    <i class="fas fa-lightbulb me-2"></i>
                    Glissez les lettres dans l'ordre pour former la réponse
                </p>
            </div>
        `;
        
        // Créer le puzzle
        createPuzzle(answerText);
        
        // Afficher le modal CORRECTEMENT
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    // Créer le puzzle interactif avec nouvelles couleurs
    function createPuzzle(answerText) {
        console.log('Création du puzzle avec:', answerText);
        
        const puzzlePiecesContainer = document.getElementById('puzzlePieces');
        const puzzleTargetsContainer = document.getElementById('puzzleTargets');
        
        if (!puzzlePiecesContainer || !puzzleTargetsContainer) {
            console.error('Conteneurs de puzzle non trouvés');
            return;
        }
        
        // Vider les conteneurs
        puzzlePiecesContainer.innerHTML = '';
        puzzleTargetsContainer.innerHTML = '';
        
        // Nettoyer le texte et le mettre en majuscules
        const cleanText = answerText.toUpperCase().replace(/\s+/g, ' ');
        
        // Séparer les mots
        const words = cleanText.split(' ');
        console.log('Mots du puzzle:', words);
        
        // Palette de couleurs pour les lettres
        const letterColors = [
            { bg: 'linear-gradient(135deg, #4dabf7, #339af0)', border: 'rgba(77, 171, 247, 0.5)' },
            { bg: 'linear-gradient(135deg, #ff6b7a, var(--primary-color))', border: 'rgba(255, 74, 87, 0.5)' },
            { bg: 'linear-gradient(135deg, #20c997, #12b886)', border: 'rgba(32, 201, 151, 0.5)' },
            { bg: 'linear-gradient(135deg, #ffd43b, #fab005)', border: 'rgba(255, 212, 59, 0.5)' },
            { bg: 'linear-gradient(135deg, #da77f2, #ae3ec9)', border: 'rgba(218, 119, 242, 0.5)' },
            { bg: 'linear-gradient(135deg, #ff922b, #e8590c)', border: 'rgba(255, 146, 43, 0.5)' }
        ];
        
        // Créer les cases cibles pour chaque mot
        let totalLetters = 0;
        words.forEach((word, wordIndex) => {
            // Ajouter un espace entre les mots (sauf pour le premier)
            if (wordIndex > 0) {
                const spaceSlot = document.createElement('div');
                spaceSlot.className = 'puzzle-target-slot space-slot';
                spaceSlot.style.width = '25px';
                puzzleTargetsContainer.appendChild(spaceSlot);
            }
            
            // Créer une case pour chaque lettre
            for (let i = 0; i < word.length; i++) {
                const target = document.createElement('div');
                target.className = 'puzzle-target-slot';
                target.dataset.index = totalLetters;
                target.dataset.expectedLetter = word[i];
                target.dataset.position = `${wordIndex}-${i}`;
                
                // Ajouter un effet de numérotation subtile
                const numberSpan = document.createElement('span');
                numberSpan.textContent = (i + 1);
                numberSpan.style.cssText = `
                    position: absolute;
                    bottom: 3px;
                    right: 3px;
                    font-size: 0.7rem;
                    color: rgba(255, 255, 255, 0.4);
                    font-weight: bold;
                `;
                target.appendChild(numberSpan);
                
                puzzleTargetsContainer.appendChild(target);
                totalLetters++;
            }
        });
        
        // Créer les pièces (lettres mélangées)
        const letters = cleanText.replace(/\s+/g, '').split('');
        const shuffledLetters = [...letters].sort(() => Math.random() - 0.5);
        
        console.log('Lettres mélangées:', shuffledLetters);
        
        shuffledLetters.forEach((letter, index) => {
            const piece = document.createElement('div');
            piece.className = 'puzzle-piece-letter';
            piece.textContent = letter;
            piece.dataset.letter = letter;
            piece.dataset.id = `piece-${index}`;
            piece.draggable = true;
            piece.setAttribute('aria-grabbed', 'false');
            
            // Appliquer une couleur aléatoire de la palette
            const colorIndex = index % letterColors.length;
            piece.style.background = letterColors[colorIndex].bg;
            piece.style.borderColor = letterColors[colorIndex].border;
            
            // Ajouter un effet de brillance
            const shine = document.createElement('div');
            shine.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                border-radius: 9px;
                pointer-events: none;
            `;
            piece.appendChild(shine);
            
            // Événements de drag and drop
            piece.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', letter);
                e.dataTransfer.setData('piece-id', this.dataset.id);
                this.classList.add('dragging');
                this.setAttribute('aria-grabbed', 'true');
                
                // Ajouter un effet visuel lors du drag
                this.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.4)';
            });
            
            piece.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                this.setAttribute('aria-grabbed', 'false');
                this.style.boxShadow = '';
            });
            
            puzzlePiecesContainer.appendChild(piece);
        });
        
        // Configurer les zones de dépôt avec effets améliorés
        const targets = document.querySelectorAll('.puzzle-target-slot:not(.space-slot)');
        targets.forEach(target => {
            // Ajouter un effet de halo subtil
            const halo = document.createElement('div');
            halo.style.cssText = `
                position: absolute;
                top: -5px;
                left: -5px;
                right: -5px;
                bottom: -5px;
                border-radius: 15px;
                background: rgba(77, 171, 247, 0.1);
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
                z-index: -1;
            `;
            target.appendChild(halo);
            
            target.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
                halo.style.opacity = '1';
            });
            
            target.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
                halo.style.opacity = '0';
            });
            
            target.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                halo.style.opacity = '0';
                
                const letter = e.dataTransfer.getData('text/plain');
                const pieceId = e.dataTransfer.getData('piece-id');
                const expectedLetter = this.dataset.expectedLetter;
                
                console.log(`Déposer ${letter} (attendue: ${expectedLetter})`);
                
                if (letter === expectedLetter) {
                    this.textContent = letter;
                    this.classList.add('correct');
                    this.classList.remove('wrong');
                    
                    // Animation de succès
                    this.style.animation = 'pulseSuccess 0.6s ease';
                    
                    // Retirer la pièce utilisée
                    const usedPiece = document.querySelector(`.puzzle-piece-letter[data-id="${pieceId}"]:not(.used)`);
                    if (usedPiece) {
                        usedPiece.classList.add('used');
                        usedPiece.draggable = false;
                        usedPiece.style.opacity = '0.4';
                        usedPiece.style.cursor = 'not-allowed';
                        usedPiece.style.transform = 'scale(0.9)';
                    }
                    
                    // Vérifier si le puzzle est complet
                    setTimeout(() => {
                        this.style.animation = '';
                        checkPuzzleComplete();
                    }, 600);
                } else {
                    this.classList.add('wrong');
                    this.style.animation = 'shakeError 0.5s ease';
                    
                    setTimeout(() => {
                        this.classList.remove('wrong');
                        this.style.animation = '';
                    }, 500);
                }
            });
        });
    }
    
    // Vérifier si le puzzle est complet
    function checkPuzzleComplete() {
        const targets = document.querySelectorAll('.puzzle-target-slot:not(.space-slot)');
        const filledTargets = Array.from(targets).filter(target => target.textContent !== '');
        
        if (filledTargets.length === targets.length) {
            // Toutes les cases sont remplies
            const isComplete = Array.from(targets).every(target => 
                target.textContent === target.dataset.expectedLetter
            );
            
            if (isComplete) {
                setTimeout(() => {
                    showPuzzleComplete();
                }, 500);
            }
        }
    }
    
    // Afficher l'écran de puzzle complet
    function showPuzzleComplete() {
        const puzzleBody = document.getElementById('puzzleBody');
        
        puzzleBody.innerHTML = `
            <div class="puzzle-complete">
                <i class="fas fa-trophy"></i>
                <h3>Puzzle Résolu !</h3>
                <p>Félicitations ! Vous avez résolu le puzzle et découvert la bonne réponse.</p>
                <p>La réponse correcte a été automatiquement sélectionnée pour vous.</p>
                
                <div style="margin-top: 30px; padding: 15px; background: #e9ecef; border-radius: 10px;">
                    <h4><i class="fas fa-check-circle me-2" style="color: #20c997"></i>Bonne réponse sélectionnée</h4>
                    <p style="margin-bottom: 0;">Vous pouvez maintenant passer à la question suivante.</p>
                </div>
            </div>
        `;
        
        // Sélectionner automatiquement la bonne réponse
        selectCorrectAnswer();
    }
    
    // Sélectionner automatiquement la bonne réponse
    function selectCorrectAnswer() {
        const activeQuestion = document.querySelector('.question-card.active');
        if (!activeQuestion) return;
        
        const correctOption = activeQuestion.querySelector('.option-label[data-correct="true"]');
        if (correctOption) {
            const radioInput = correctOption.querySelector('.option-input');
            if (radioInput) {
                radioInput.checked = true;
                
                // Déclencher l'événement change
                const event = new Event('change');
                radioInput.dispatchEvent(event);
                
                // Mettre en évidence
                correctOption.classList.add('selected');
                correctOption.style.animation = 'selectedPulse 2s ease';
                
                // Fermer le modal après 2 secondes
                setTimeout(() => {
                    closePuzzle();
                }, 2000);
            }
        }
    }
    
    // Résoudre le puzzle automatiquement - VERSION CORRIGÉE
    function solvePuzzle() {
        console.log('Résolution automatique du puzzle');
        
        const targets = document.querySelectorAll('.puzzle-target-slot:not(.space-slot)');
        const pieces = document.querySelectorAll('.puzzle-piece-letter:not(.used)');
        
        // Placer toutes les lettres correctement
        targets.forEach(target => {
            const expectedLetter = target.dataset.expectedLetter;
            if (!target.textContent) { // Seulement si vide
                target.textContent = expectedLetter;
                target.classList.add('correct');
                target.style.animation = 'pulseSuccess 0.5s ease';
                
                // Marquer une pièce correspondante comme utilisée
                const availablePiece = Array.from(pieces).find(piece => 
                    piece.dataset.letter === expectedLetter && !piece.classList.contains('used')
                );
                
                if (availablePiece) {
                    availablePiece.classList.add('used');
                    availablePiece.style.opacity = '0.3';
                    availablePiece.style.cursor = 'not-allowed';
                    availablePiece.draggable = false;
                }
            }
        });
        
        // Afficher l'écran de complétion après un court délai
        setTimeout(() => {
            showPuzzleComplete();
            
            // Sélectionner automatiquement la bonne réponse
            setTimeout(selectCorrectAnswer, 1000);
        }, 1000);
    }
    
    // Fermer le modal du puzzle
    function closePuzzle() {
        const modal = document.getElementById('puzzleModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Afficher un toast de notification
    function showToast(message, type = 'info') {
        // Créer le toast
        const toast = document.createElement('div');
        toast.className = `help-toast ${type}`;
        toast.innerHTML = `
            <div class="help-toast-icon">
                <i class="fas fa-${getToastIcon(type)}"></i>
            </div>
            <div class="help-toast-message">
                <h4>${getToastTitle(type)}</h4>
                <p>${message}</p>
            </div>
        `;
        
        // Ajouter au body
        document.body.appendChild(toast);
        
        // Retirer après 4 secondes
        setTimeout(() => {
            toast.style.animation = 'slideInRight 0.3s ease reverse';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 4000);
    }
    
    function getToastIcon(type) {
        switch(type) {
            case 'success': return 'check-circle';
            case 'warning': return 'exclamation-triangle';
            case 'info': return 'info-circle';
            default: return 'info-circle';
        }
    }
    
    function getToastTitle(type) {
        switch(type) {
            case 'success': return 'Succès';
            case 'warning': return 'Attention';
            case 'info': return 'Information';
            default: return 'Notification';
        }
    }
    
    // Réinitialiser les aides à chaque chargement de page
    document.addEventListener('DOMContentLoaded', function() {
        // Réinitialiser les aides pour ce quiz
        helps = {
            '1': { used: false, count: 1 },
            '2': { used: false, count: 1 },
            '3': { used: false, count: 1 }
        };
        
        // Réinitialiser les boutons d'aide
        for (let i = 1; i <= 3; i++) {
            const helpButton = document.getElementById(`help${i}`);
            if (helpButton) {
                helpButton.classList.remove('used');
                helpButton.style.pointerEvents = 'auto';
                helpButton.style.opacity = '1';
                
                const countElement = helpButton.querySelector('.help-count');
                if (countElement) {
                    countElement.style.display = 'block';
                    countElement.textContent = '1';
                }
                
                const tooltip = helpButton.querySelector('.help-button-tooltip');
                if (tooltip) {
                    tooltip.style.display = 'block';
                }
            }
        }
        
        // Réinitialiser la liste des aides utilisées
        helpsUsedList = [];
        document.getElementById('helpsUsed').value = '';
        
        console.log('Système d\'aides réinitialisé pour ce quiz');
        
        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('puzzleModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePuzzle();
            }
        });
    });
    
    // Fonction pour le menu utilisateur
    function toggleUserMenu() {
        document.getElementById('userDropdown').classList.toggle('show');
    }
    
    // Fermer le menu utilisateur en cliquant ailleurs
    window.onclick = function(event) {
        if (!event.target.matches('.user-wrapper') && !event.target.closest('.user-wrapper')) {
            const dropdowns = document.getElementsByClassName("user-dropdown");
            for (let i = 0; i < dropdowns.length; i++) {
                const openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    };
</script>
    <script src="quiz_pagee.js"></script>
  </body>
</html>