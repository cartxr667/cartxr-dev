<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

require_once "../config/database.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Produit introuvable.");
}

$id = (int) $_GET["id"];

try {

    // Récupérer le produit
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$id]);

    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produit) {
        die("Ce produit n'existe pas.");
    }

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}


// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = trim($_POST["nom"] ?? "");
    $categorie = trim($_POST["categorie"] ?? "");
    $collection = trim($_POST["collection"] ?? "");
    $prix = (int) ($_POST["prix"] ?? 0);
    $alt = trim($_POST["alt"] ?? "");
    $label = trim($_POST["label"] ?? "");

    if ($nom === "" || $categorie === "" || $prix <= 0) {
        die("Veuillez remplir correctement les champs obligatoires.");
    }

    try {

        // Image actuelle
        $image = $produit["image"];

        // Vérifier si une nouvelle image a été envoyée
        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] === UPLOAD_ERR_OK
        ) {

            $extension = strtolower(
                pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION)
            );

            $extensionsAutorisees = ["jpg", "jpeg", "png", "webp", "gif"];

            if (!in_array($extension, $extensionsAutorisees)) {
                die("Format d'image non autorisé.");
            }

            // Nouveau nom unique
            $nouveauNom = uniqid("produit_", true) . "." . $extension;

            $destination = __DIR__ . "/uploads/" . $nouveauNom;

            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $destination)) {
                die("Impossible d'envoyer la nouvelle image.");
            }

            // Supprimer l'ancienne image
            if (!empty($produit["image"])) {

                $ancienneImage = basename($produit["image"]);
                $ancienChemin = __DIR__ . "/uploads/" . $ancienneImage;

                if (file_exists($ancienChemin)) {
                    unlink($ancienChemin);
                }
            }

            // Chemin enregistré en base
            $image = "uploads/" . $nouveauNom;
        }

        // Mise à jour du produit
        $sql = "UPDATE produits SET
                    nom = ?,
                    categorie = ?,
                    collection = ?,
                    prix = ?,
                    image = ?,
                    alt = ?,
                    label = ?
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $nom,
            $categorie,
            $collection,
            $prix,
            $image,
            $alt,
            $label,
            $id
        ]);

        header("Location: dashboard.php?success=produit_modifie");
        exit;

    } catch (PDOException $e) {

        die("Erreur lors de la modification : " . $e->getMessage());

    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Modifier le produit</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 30px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
        }

        button {
            margin-top: 25px;
            padding: 12px 20px;
            cursor: pointer;
        }

        .current-image {
            width: 180px;
            height: 180px;
            object-fit: cover;
            margin-top: 10px;
            border-radius: 8px;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Modifier le produit</h1>

    <form method="POST" enctype="multipart/form-data">

        <label for="nom">Nom du produit</label>

        <input
            type="text"
            id="nom"
            name="nom"
            value="<?= htmlspecialchars($produit["nom"]) ?>"
            required
        >


        <label for="categorie">Catégorie</label>

        <select id="categorie" name="categorie" required>

            <option value="Mode"
                <?= $produit["categorie"] === "Mode" ? "selected" : "" ?>>
                Mode
            </option>

            <option value="Maison"
                <?= $produit["categorie"] === "Maison" ? "selected" : "" ?>>
                Maison
            </option>

            <option value="Accessoires"
                <?= $produit["categorie"] === "Accessoires" ? "selected" : "" ?>>
                Accessoires
            </option>

        </select>


        <label for="collection">Collection</label>

        <input
            type="text"
            id="collection"
            name="collection"
            value="<?= htmlspecialchars($produit["collection"] ?? "") ?>"
        >


        <label for="prix">Prix (FCFA)</label>

        <input
            type="number"
            id="prix"
            name="prix"
            value="<?= htmlspecialchars($produit["prix"]) ?>"
            min="1"
            required
        >


        <label>Image actuelle</label>

        <?php if (!empty($produit["image"])): ?>

            <img
                class="current-image"
                src="uploads/<?= htmlspecialchars(basename($produit["image"])) ?>"
                alt="<?= htmlspecialchars($produit["alt"] ?? "") ?>"
            >

        <?php endif; ?>


        <label for="image">Nouvelle image</label>

        <input
            type="file"
            id="image"
            name="image"
            accept="image/jpeg,image/png,image/webp,image/gif"
        >


        <label for="alt">Texte alternatif de l'image</label>

        <input
            type="text"
            id="alt"
            name="alt"
            value="<?= htmlspecialchars($produit["alt"] ?? "") ?>"
        >


        <label for="label">Label</label>

        <input
            type="text"
            id="label"
            name="label"
            value="<?= htmlspecialchars($produit["label"] ?? "") ?>"
            placeholder="Ex : Nouveau, Coup de cœur..."
        >


        <button type="submit">
            ENREGISTRER LES MODIFICATIONS
        </button>

    </form>

    <a class="back" href="dashboard.php">
        ← Retour au tableau de bord
    </a>

</div>

</body>

</html>