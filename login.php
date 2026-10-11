<?php

session_start();

require_once "../config/database.php";

// Si déjà connecté
if (isset($_SESSION["admin_id"])) {
    header("Location: dashboard.php");
    exit;
}

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $erreur = "Veuillez remplir tous les champs.";

    } else {

        try {

            $stmt = $pdo->prepare("
                SELECT id, username, password
                FROM admins
                WHERE username = ?
                LIMIT 1
            ");

            $stmt->execute([$username]);

            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin["password"])) {

                session_regenerate_id(true);

                $_SESSION["admin_id"] = $admin["id"];
                $_SESSION["admin_username"] = $admin["username"];

                header("Location: dashboard.php");
                exit;

            } else {

                $erreur = "Identifiant ou mot de passe incorrect.";

            }

        } catch (PDOException $e) {

            $erreur = "Une erreur est survenue.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Connexion — CARTER SHOP</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: #f5f5f3;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }

        .logo {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #111;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 6px;
            background: #d32f2f;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #111;
        }

        .error {
            background: #ffe5e5;
            color: #a00000;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: white;
            width: 100%;
            background: #111;
            font-weight: bold;
            padding: 12px;
            cursor: pointer;
            font-size: 15px;
            border-radius: 6px;
            text-decoration: none;
        }     
        a:hover {
            background: #d32f2f
        }
            
            
            

    </style>

</head>

<body>

<div class="login-box">

    <div class="logo">
        CARTER SHOP
    </div>

    <p class="subtitle">
        Administration
    </p>

    <?php if ($erreur): ?>

        <div class="error">
            <?= htmlspecialchars($erreur) ?>
        </div>

    <?php endif; ?>
    <form method="POST">
        <label for="username">
            Identifiant
        </label>
        <input
            type="text"
            id="username"
            name="username"
            placeholder="Votre identifiant"
            autocomplete="username"
            required
        >
        <label for="password">
            Mot de passe
        </label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Votre mot de passe"
            autocomplete="current-password"
            required
        >
        <button type="submit">
            SE CONNECTER
        </button>
    </form>
    <a href="../index.php" class="back">
        ← Retour à la boutique
    </a>

</div>

</body>

</html>