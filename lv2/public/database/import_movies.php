<?php
$host = 'localhost';
$dbname = 'filmovi';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS movies (
        filmtv_id INT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        year INT,
        genre VARCHAR(50),
        duration INT,
        country VARCHAR(100),
        directors TEXT,
        actors TEXT,
        avg_vote DECIMAL(3,1),
        critics_vote DECIMAL(3,1),
        public_vote DECIMAL(3,1),
        total_votes INT,
        description TEXT,
        notes TEXT,
        humor INT,
        rhythm INT,
        effort INT,
        tension INT,
        erotism INT
    )");
    
    $csvFile = fopen(__DIR__ . '/movies.csv', 'r');
    if ($csvFile === false) {
        throw new Exception("Could not open CSV file");
    }
    
    fgetcsv($csvFile);
    
    $stmt = $pdo->prepare("INSERT INTO movies (
        filmtv_id, title, year, genre, duration, country, directors, actors,
        avg_vote, critics_vote, public_vote, total_votes, description, notes,
        humor, rhythm, effort, tension, erotism
    ) VALUES (
        :filmtv_id, :title, :year, :genre, :duration, :country, :directors, :actors,
        :avg_vote, :critics_vote, :public_vote, :total_votes, :description, :notes,
        :humor, :rhythm, :effort, :tension, :erotism
    )");
    
    $count = 0;
    while (($row = fgetcsv($csvFile)) !== false) {
        $stmt->execute([
            'filmtv_id' => $row[0],
            'title' => $row[1],
            'year' => $row[2],
            'genre' => $row[3],
            'duration' => $row[4],
            'country' => $row[5],
            'directors' => $row[6],
            'actors' => $row[7],
            'avg_vote' => $row[8] ?: null,
            'critics_vote' => $row[9] ?: null,
            'public_vote' => $row[10] ?: null,
            'total_votes' => $row[11] ?: null,
            'description' => $row[12],
            'notes' => $row[13],
            'humor' => $row[14] ?: null,
            'rhythm' => $row[15] ?: null,
            'effort' => $row[16] ?: null,
            'tension' => $row[17] ?: null,
            'erotism' => $row[18] ?: null
        ]);
        $count++;
    }
    
    fclose($csvFile);
    echo "Successfully imported $count movies into the database.";
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 