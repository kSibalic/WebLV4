<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../auth/check_session.php';

require_login();
header('Content-Type: application/json');

try {
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT m.filmtv_id 
        FROM cart_items ci 
        JOIN movies m ON ci.movie_id = m.filmtv_id 
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $movies = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($movies)) {
        echo json_encode([
            'success' => false,
            'message' => 'Vaša košarica je prazna. Dodajte filmove prije posudbe.'
        ]);
        exit;
    }
    
    $pdo->beginTransaction();
    
    // Spremi posudbu
    $stmt = $pdo->prepare("
        INSERT INTO rentals (user_id, movie_ids) 
        VALUES (?, ?)
    ");
    $stmt->execute([$user_id, json_encode($movies)]);
    
    // Očisti košaricu
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Uspješno ste posudili filmove!'
    ]);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Greška pri obradi posudbe: ' . $e->getMessage()
    ]);
} 