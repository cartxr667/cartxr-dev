<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

require_once "../config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = trim($_POST["nom"]);
    $categorie = trim($_POST["categorie"]);
    $collection = trim($_POST["collection"]);
    $prix = (int) $_POST["prix"];
    $alt = trim($_POST["alt"]);
    $label = trim($_POST["label"]);

    // Vérifier qu'une image a été envoyée
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {

        $image = $_FILES["image"];

        // Extensions autorisées
        $extensionsAutorisees = ["jpg", "jpeg", "png", "webp"];

        $extension = strtolower(
            pathinfo($image["name"], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $extensionsAutorisees)) {

            $message = "Format d'image non autorisé.";

        } else {

            // Créer un nom unique
            $nouveauNom = uniqid("produit_", true) . "." . $extension;

            $destination = "uploads/" . $nouveauNom;

            // Déplacer l'image
            if (move_uploaded_file($image["tmp_name"], $destination)) {

                // Enregistrer dans MySQL
                $sql = "INSERT INTO produits
                        (nom, categorie, collection, prix, image, alt, label)
                        VALUES
                        (:nom, :categorie, :collection, :prix, :image, :alt, :label)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":nom" => $nom,
                    ":categorie" => $categorie,
                    ":collection" => $collection,
                    ":prix" => $prix,
                    ":image" => $destination,
                    ":alt" => $alt,
                    ":label" => $label
                ]);

                $message = "Produit ajouté avec succès !";

            } else {

                $message = "Erreur lors de l'envoi de l'image.";
            }
        }

    } else {

        $message = "Veuillez sélectionner une image.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
    
    <!-- Polices Google Font (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #111; 
            --primary-hover: #d32f2f;
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-color: #1f2937;
            --label-color: #111;
            --border-color: #d1d5db;
            --focus-ring: rgba(79, 70, 229, 0.2);
            --message-bg: #e0e7ff;
            --message-text: green;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: var(--card-bg);
            width: 100%;
            max-width: 550px;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--text-color);
            text-align: center;
        }

        .alert-message {
            background-color: var(--message-bg);
            color: var(--message-text);
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            border-left: 4px solid var(--primary-color);
        }

        .form-group {
            margin-bottom: 18px;
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--label-color);
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px 14px;
            font-size: 0.95rem;
            font-family: inherit;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--focus-ring);
        }

        input[type="file"] {
            font-size: 0.9rem;
            color: var(--label-color);
            padding: 8px 0;
        }

        input[type="file"]::file-selector-button {
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background-color: #f9fafb;
            color: var(--text-color);
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-right: 12px;
        }

        input[type="file"]::file-selector-button:hover {
            background-color: #f3f4f6;
        }

        button[type="submit"] {
            width: 100%;
            background-color: var(--primary-color);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background-color: var(--primary-hover);
        }

        button[type="submit"]:active {
            transform: scale(0.99);
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Ajouter un produit</h1>

        <?php if ($message): ?>
            <p class="alert-message">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label for="nom">Nom du produit *</label>
                <input type="text" id="nom" name="nom" required placeholder="Ex : Sac en cuir" require>
            </div>

            <div class="form-group">
                <label for="categorie">Catégorie *</label>
                <select id="categorie" name="categorie" required>
                    <option value="">-- Choisir --</option>
                    <option value="Mode">Mode</option>
                    <option value="Maison">Maison</option>
                    <option value="Accessoires">Accessoires</option>
                </select>
            </div>

            <div class="form-group">
                <label for="collection">Collection</label>
                <input type="text" id="collection" name="collection" placeholder="Ex : Été 2024" >
            </div>

            <div class="form-group">
                <label for="prix">Prix en FCFA *</label>
                <input type="number" id="prix" name="prix" min="0" required placeholder="Ex : 15000">
            </div>

            <div class="form-group">
                <label for="image">Image du produit *</label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" required>
            </div>

            <div class="form-group">
                <label for="alt">Description de l'image (alt)</label>
                <input type="text" id="alt" name="alt" placeholder="Ex : Sac en cuir marron vue de face">
            </div>

            <div class="form-group">
                <label for="label">Label</label>
                <select name="label" id="label">
                    <option value="">-- choisir -- </option>
                    <option value="Nouveau">Nouveau</option>
                    <option value="Promo">Promo</option>
                    <option value="Top vente">Top vente</option>
                </select>
            </div>

            <button type="submit">
                AJOUTER LE PRODUIT
            </button>

        </form>
    </div>

</body>

</html>