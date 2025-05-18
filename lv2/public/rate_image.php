<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'auth/check_session.php';

// Provjera prijave
require_login();

// Provjera POST zahtjeva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_id = filter_input(INPUT_POST, 'image_id', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    
    if ($image_id && $rating && $rating >= 1 && $rating <= 5) {
        // Dohvaćanje korisnika
        $user = get_user_by_id($_SESSION['user_id']);
        
        if ($user) {
            // Spremanje ocjene
            if (rate_image($image_id, $user['id'], $rating)) {
                // Preusmjeravanje natrag na galeriju
                header('Location: pages/slike.php');
                exit;
            }
        }
    }
}

// Ako dođe do greške, preusmjeri na galeriju
header('Location: pages/slike.php');
exit; 