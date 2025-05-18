<?php
require_once 'db.php';

// Funkcije za rad s korisnicima
function get_user_by_id($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Funkcije za rad s filmovima
function get_all_movies($filters = []) {
    global $pdo;
    
    $sql = "SELECT * FROM movies WHERE 1=1";
    $params = [];
    
    if (!empty($filters['genre'])) {
        $sql .= " AND genre = ?";
        $params[] = $filters['genre'];
    }
    
    if (!empty($filters['year'])) {
        $sql .= " AND year = ?";
        $params[] = $filters['year'];
    }
    
    if (!empty($filters['country'])) {
        $sql .= " AND country = ?";
        $params[] = $filters['country'];
    }
    
    if (!empty($filters['sort'])) {
        $sql .= " ORDER BY " . $filters['sort'];
    } else {
        $sql .= " ORDER BY title ASC";
    }
    
    $sql .= " LIMIT 25";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_movie_by_id($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE filmtv_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function validate_movie_data($data) {
    $errors = [];
    
    if (empty($data['title'])) {
        $errors[] = "Naslov je obavezan";
    }
    
    if (!empty($data['year']) && (!is_numeric($data['year']) || $data['year'] < 1888 || $data['year'] > date('Y'))) {
        $errors[] = "Godina mora biti između 1888 i " . date('Y');
    }
    
    if (!empty($data['duration']) && (!is_numeric($data['duration']) || $data['duration'] < 1)) {
        $errors[] = "Trajanje mora biti pozitivan broj";
    }
    
    return $errors;
}

function get_all_images() {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT i.*, u.username as author, 
               COALESCE(AVG(r.rating), 0) as avg_rating,
               COUNT(r.id) as rating_count,
               GROUP_CONCAT(r.rating ORDER BY r.created_at DESC) as all_ratings
        FROM images i
        LEFT JOIN users u ON i.user_id = u.id
        LEFT JOIN image_ratings r ON i.id = r.image_id
        GROUP BY i.id
        ORDER BY i.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_image_by_id($id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT i.*, u.username as author,
               COALESCE(AVG(r.rating), 0) as avg_rating,
               COUNT(r.id) as rating_count,
               GROUP_CONCAT(r.rating ORDER BY r.created_at DESC) as all_ratings
        FROM images i
        LEFT JOIN users u ON i.user_id = u.id
        LEFT JOIN image_ratings r ON i.id = r.image_id
        WHERE i.id = ?
        GROUP BY i.id
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function rate_image($image_id, $user_id, $rating) {
    global $pdo;
    
    // Dodaj novu ocjenu
    $stmt = $pdo->prepare("INSERT INTO image_ratings (image_id, user_id, rating) VALUES (?, ?, ?)");
    return $stmt->execute([$image_id, $user_id, $rating]);
}

function upload_image($user_id, $title, $description, $file) {
    global $pdo;
    
    $errors = validate_image_upload($file);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $upload_path = '../pictures/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => false, 'errors' => ['Greška prilikom spremanja datoteke']];
    }
    
    // Spremi podatke u bazu
    $stmt = $pdo->prepare("
        INSERT INTO images (user_id, title, description, filename, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    if ($stmt->execute([$user_id, $title, $description, $filename])) {
        return ['success' => true, 'image_id' => $pdo->lastInsertId()];
    } else {
        unlink($upload_path);
        return ['success' => false, 'errors' => ['Greška prilikom spremanja u bazu']];
    }
}

function validate_image_upload($file) {
    $errors = [];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024;
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Greška prilikom uploada datoteke";
        return $errors;
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        $errors[] = "Dozvoljeni formati su: JPG, PNG i GIF";
    }
    
    if ($file['size'] > $max_size) {
        $errors[] = "Maksimalna veličina datoteke je 5MB";
    }
    
    return $errors;
}

function add_to_cart($user_id, $filmtv_id) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id FROM cart_items WHERE user_id = ? AND filmtv_id = ?");
    $stmt->execute([$user_id, $filmtv_id]);
    
    if ($stmt->fetch()) {
        return false; 
    }
    
    $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, filmtv_id) VALUES (?, ?)");
    return $stmt->execute([$user_id, $filmtv_id]);
}

function remove_from_cart($user_id, $filmtv_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND filmtv_id = ?");
    return $stmt->execute([$user_id, $filmtv_id]);
}

function get_cart_items($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT m.* 
        FROM cart_items c
        JOIN movies m ON c.filmtv_id = m.filmtv_id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_cart_count($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
} 