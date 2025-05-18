<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../auth/check_session.php';

require_login();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];

switch ($action) {
    case 'add':
        $movie_id = $_POST['movie_id'] ?? 0;
        
        $stmt = $pdo->prepare("SELECT * FROM movies WHERE filmtv_id = ?");
        $stmt->execute([$movie_id]);
        $movie = $stmt->fetch();
        
        if (!$movie) {
            echo json_encode(['success' => false, 'message' => 'Film nije pronađen']);
            exit;
        }
        
        // Dodaj
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, movie_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $movie_id]);
        
        break;
        
    case 'remove':
        $movie_id = $_POST['movie_id'] ?? 0;
        
        // Ukloni
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND movie_id = ? LIMIT 1");
        $stmt->execute([$user_id, $movie_id]);
        
        break;
        
    case 'get':
        // Sadržaj
        $stmt = $pdo->prepare("
            SELECT m.*, ci.id as cart_item_id 
            FROM cart_items ci 
            JOIN movies m ON ci.movie_id = m.filmtv_id 
            WHERE ci.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart_items WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart_count = $stmt->fetchColumn();
        
        echo json_encode([
            'success' => true,
            'cart_items' => $cart_items,
            'cart_count' => $cart_count
        ]);
        exit;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Nepoznata akcija']);
        exit;
}

// Ažurirani sadržaj
$stmt = $pdo->prepare("
    SELECT m.*, ci.id as cart_item_id 
    FROM cart_items ci 
    JOIN movies m ON ci.movie_id = m.filmtv_id 
    WHERE ci.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Broj stavki
$stmt = $pdo->prepare("SELECT COUNT(*) FROM cart_items WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_count = $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'cart_items' => $cart_items,
    'cart_count' => $cart_count
]); 