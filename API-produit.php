<?php

require_once "config/database.php";

header("Content-Type: application/json; charset=UTF-8");

try {

    $sql = "SELECT
                id,
                nom AS name,
                categorie AS category,
                collection,
                prix AS price,
                CONCAT('admin/',image) AS image,
                alt,
                label
            FROM produits
            ORDER BY id DESC";

    $stmt = $pdo->query($sql);

    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(
        $produits,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "error" => "Impossible de récupérer les produits.",
        "details" => $e->getMessage()
    ]);

}