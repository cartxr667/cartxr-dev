<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f8f7f4">
    <meta name="description" content="Découvrez la sélection CARTER SHOP : mode, accessoires et essentiels du quotidien choisis avec soin.">
    <title>CARTER SHOP — Les belles choses du quotidien</title>
    <link rel="stylesheet" href="index.css">
    <script src="index.js" defer></script>
</head>
<body>
    <div class="announcement">LIVRAISON OFFERTE DÈS 20.000 F <strong>—</strong> Un petit plaisir en plus, chez vous.</div>
    <header class="site-header">
        <nav class="nav-links" aria-label="Navigation principale">
            <a href="#boutique">La boutique</a>
            <a href="#boutique" data-category-link="Mode">Mode</a>
            <a href="#boutique" data-category-link="Maison">Maison</a>
        </nav>
        <a class="logo" href="#" aria-label="CARTER SHOP, accueil">CARTER SHOP</a>
        <div class="header-actions">
            <button class="icon-button cart-trigger" id="open-cart" type="button" aria-label="Ouvrir le panier"><span>Panier</span>
                <span class="cart-count" id="cart-count">0</span>
            </button>
        </div>
    </header>
    <main>
        <section class="hero" aria-labelledby="hero-title">
            <div class="hero-image" aria-hidden="true"></div>
            <div class="hero-copy">
                <p class="eyebrow">La sélection du moment · N° 01</p>
                <h1 id="hero-title">Moins, mais<br>tellement mieux.</h1>
                <p>Des pièces bien pensées, des matières qu’on aime et des objets faits pour durer. Bienvenue chez CARTER SHOP.</p>
                <a class="button-dark" href="#boutique">DÉCOUVRIR LA SÉLECTION <span aria-hidden="true">↗</span></a>
            </div>
        </section>
        <section class="shop-section" id="boutique" aria-labelledby="shop-title">
            <div class="section-heading">
                <div><p class="eyebrow">Choisis avec attention</p><h2 id="shop-title">Nos coups de cœur</h2></div>
                <div class="shop-tools">
                    <label class="search-wrap"><span class="sr-only">Rechercher un produit</span>
                        <input id="product-search" type="search" placeholder="Rechercher un article…" autocomplete="off"></label>
                </div>
            </div>
            <div class="category-list" id="category-list" aria-label="Filtrer par catégorie"></div>
            <div class="product-grid" id="product-grid" aria-live="polite"></div>
        </section>
        <section class="promise-strip" aria-label="Nos engagements">
            <div><h3>Livraison offerte</h3><p>Dès 20.000 F d’achat, partout en France.</p></div>
            <div><h3>Choix responsable</h3><p>Des essentiels sélectionnés pour durer.</p></div>
            <div><h3>Contactez-nous</h3><p>+225 123 456 789</p></div>
        </section>
    </main>
    <footer>
        <a class="logo" href="#" aria-label="CARTER SHOP, accueil">CARTER SHOP</a>
        <p >© 2026 CARTER SHOP. Une boutique en ligne  — Disign by Cartxr_Dev.</p>
        <p>Une question ? bonjour@carter-shop.fr</p>
    </footer>
    <div class="overlay" id="overlay"></div>
    <aside class="cart-panel" id="cart-panel" aria-label="Votre panier" aria-hidden="true">
        <div class="cart-heading"><h2>Votre panier <span id="cart-heading-count">(0)</span></h2>
            <button class="close-cart" id="close-cart" type="button" aria-label="Fermer le panier">×</button></div>
        <div class="cart-items" id="cart-items"></div>
        <div class="cart-footer">
            <div class="subtotal"><span>Sous-total</span><span id="cart-subtotal">0,00F</span></div>
            <p class="shipping-note">Les frais de livraison sont calculés à l’étape suivante.</p>
            <button class="button-dark checkout-button" id="checkout" type="button">PASSER LA COMMANDE</button>
            <p class="checkout-message" id="checkout-message" role="status"></p>
        </div>
    </aside>
</body>
</html>