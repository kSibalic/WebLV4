<?php
$host = 'localhost';
$dbname = 'filmovi';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Database tables:\n";
    foreach ($tables as $table) {
        echo "\nTable: $table\n";
        $columns = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "  {$column['Field']} - {$column['Type']}";
            if ($column['Key'] == 'PRI') echo " (PRIMARY KEY)";
            if ($column['Null'] == 'NO') echo " (NOT NULL)";
            echo "\n";
        }
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 