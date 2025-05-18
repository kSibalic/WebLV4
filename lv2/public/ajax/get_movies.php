<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM movies ORDER BY title");
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'movies' => $movies
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Greška pri dohvaćanju filmova: ' . $e->getMessage()
    ]);
} 