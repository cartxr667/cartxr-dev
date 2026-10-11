<?php

require_once "../config/database.php";

$username = "admin";
$password = "admin123";

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO admins (username, password)
    VALUES (?, ?)
");

$stmt->execute([
    $username,
    $passwordHash
]);

echo "Administrateur créé avec succès.";