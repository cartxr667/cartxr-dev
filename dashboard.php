<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

require_once "../config/database.php";

try {

    $stmt = $pdo->query("
        SELECT *
        FROM produits
        ORDER BY id DESC
    ");

    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die("Erreur lors de la récupération des produits : " . $e->getMessage());

}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administration — CARTER SHOP</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f3;
            color: #222;
        }

        .header {
            background: #111;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }

        .top h1 {
            margin: 0;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .btn-add {
            background: #111;
            color: white;
        }

        .btn-edit {
            background: #e9e9e9;
            color: #111;
        }

        .btn-delete {
            background: #d32f2f;
            color: white;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .stat span {
            display: block;
            font-size: 14px;
            color: #777;
            margin-bottom: 10px;
        }

        .stat strong {
            font-size: 30px;
        }

        .products-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .products-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .products-header h2 {
            margin: 0;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
        }

        .product-table th,
        .product-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .product-table th {
            background: #fafafa;
            font-size: 14px;
        }

        .product-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
        }

        .product-name {
            font-weight: bold;
        }

        .product-price {
            font-weight: bold;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 6px;
            background: #dff5e1;
            color: #176b21;
        }

        .empty {
            padding: 50px;
            text-align: center;
            color: #777;
        }

        @media (max-width: 800px) {

            .header {
                padding: 20px;
            }

            .top {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .products-container {
                overflow-x: auto;
            }

            .product-table {
                min-width: 800px;
            }

        }

    </style>

</head>

<body>

<header class="header">

    <div class="logo">
        CARTER SHOP
    </div>

<div>

    <span>
        Bonjour <?= htmlspecialchars($_SESSION["admin_username"]) ?>
    </span>

    &nbsp;&nbsp;

    <a
        href="logout.php"
        style="color:white;"
    >
        Déconnexion
    </a>

</div>

</header>


<main class="container">

    <div class="top">

        <div>
            <h1>Tableau de bord</h1>
            <p>Gérez les produits de votre boutique.</p>
        </div>

        <a
            href="ajouter-produit.php"
            class="btn btn-add"
        >
            + Ajouter un produit
        </a>

    </div>


    <?php if (isset($_GET["success"])): ?>

        <div class="message">

            <?php if ($_GET["success"] === "produit_supprime"): ?>

                Produit supprimé avec succès.

            <?php elseif ($_GET["success"] === "produit_modifie"): ?>

                Produit modifié avec succès.

            <?php elseif ($_GET["success"] === "produit_ajoute"): ?>

                Produit ajouté avec succès.

            <?php endif; ?>

        </div>

    <?php endif; ?>


    <section class="stats">

        <div class="stat">

            <span>Total des produits</span>

            <strong>
                <?= count($produits) ?>
            </strong>

        </div>


        <div class="stat">

            <span>Produits Mode</span>

            <strong>

                <?php

                $mode = 0;

                foreach ($produits as $produit) {

                    if ($produit["categorie"] === "Mode") {
                        $mode++;
                    }

                }

                echo $mode;

                ?>

            </strong>

        </div>


        <div class="stat">

            <span>Produits Maison</span>

            <strong>

                <?php

                $maison = 0;

                foreach ($produits as $produit) {

                    if ($produit["categorie"] === "Maison") {
                        $maison++;
                    }

                }

                echo $maison;

                ?>

            </strong>

        </div>

    </section>


    <section class="products-container">

        <div class="products-header">

            <h2>Produits</h2>

        </div>


        <?php if (empty($produits)): ?>

            <div class="empty">

                <p>Aucun produit pour le moment.</p>

                <a
                    href="ajouter-produit.php"
                    class="btn btn-add"
                >
                    Ajouter le premier produit
                </a>

            </div>

        <?php else: ?>


            <table class="product-table">

                <thead>

                    <tr>

                        <th>Image</th>

                        <th>Produit</th>

                        <th>Catégorie</th>

                        <th>Collection</th>

                        <th>Prix</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($produits as $produit): ?>

                    <tr>

                        <td>

                            <img
                                class="product-image"
                                src="uploads/<?= htmlspecialchars(basename($produit["image"])) ?>"
                                alt="<?= htmlspecialchars($produit["alt"] ?? "") ?>"
                            >

                        </td>


                        <td>

                            <div class="product-name">

                                <?= htmlspecialchars($produit["nom"]) ?>

                            </div>

                        </td>


                        <td>

                            <?= htmlspecialchars($produit["categorie"]) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars($produit["collection"] ?? "") ?>

                        </td>


                        <td>

                            <span class="product-price">

                                <?= number_format(
                                    $produit["prix"],
                                    0,
                                    ",",
                                    " "
                                ) ?> FCFA

                            </span>

                        </td>


                        <td>

                            <div class="actions">

                                <a
                                    href="modifier-produit.php?id=<?= $produit["id"] ?>"
                                    class="btn btn-edit"
                                >
                                    Modifier
                                </a>


                                <a
                                    href="supprimer-produit.php?id=<?= $produit["id"] ?>"
                                    class="btn btn-delete"
                                    onclick="return confirm('Voulez-vous vraiment supprimer ce produit ?');"
                                >
                                    Supprimer
                                </a>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </section>

</main>

</body>

</html>