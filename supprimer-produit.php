<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

require_once "../config/database.php";

// Vérifier qu'un ID est fourni
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Produit introuvable.");
}

$id = (int) $_GET["id"];

try {

    // Récupérer les informations du produit
    $stmt = $pdo->prepare("SELECT image FROM produits WHERE id = ?");
    $stmt->execute([$id]);

    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produit) {
        die("Ce produit n'existe pas.");
    }

    // Supprimer l'image du serveur
    if (!empty($produit["image"])) {

        // La base contient par exemple : uploads/image.jpg
        $nomImage = basename($produit["image"]);

        $cheminImage = __DIR__ . "/uploads/" . $nomImage;

        if (file_exists($cheminImage)) {
            unlink($cheminImage);
        }
    }

    // Supprimer le produit de la base de données
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    $stmt->execute([$id]);

    // Retourner au tableau de bord
    header("Location: dashboard.php?success=produit_supprime");
    exit;

} catch (PDOException $e) {

    die("Erreur lors de la suppression : " . $e->getMessage());

}