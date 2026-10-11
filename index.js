let products = [];
        const categories = ["Tout voir", "Mode", "Maison", "Accessoires"];
        const productGrid = document.getElementById("product-grid");
        const categoryList = document.getElementById("category-list");
        const searchInput = document.getElementById("product-search");
        const cartPanel = document.getElementById("cart-panel");
        const overlay = document.getElementById("overlay");
        let activeCategory = "Tout voir";
        let cart = [];

async function loadProducts() {
    try {
        const response = await fetch("API-produit.php");
        if (!response.ok) {
            throw new Error("Erreur lors du chargement des produits");
        }
        const data = await response.json();

        products = data.map(product => ({
            ...product,
            id: String(product.id),
            price: Number(product.price) || 0,
            category: product.category || "",
            collection: product.collection || "",
            alt: product.alt || product.name || "",
            label: product.label || ""
        }));

        cart = loadCart();
        renderCategories();
        renderProducts();
        renderCart();
    } catch (error) {
        console.error("Erreur :", error);
        productGrid.innerHTML = `<p class="empty-results">Impossible de charger les produits.</p>`;
    }
}

        function loadCart() {
            try {
                const saved = JSON.parse(localStorage.getItem("nova-cart") || "[]");
                if (!Array.isArray(saved)) return [];
                return saved.filter(item => item && products.some(product => product.id === item.id) && Number.isInteger(item.quantity) && item.quantity > 0);
            } catch (error) {
                console.warn("Le panier enregistré n’a pas pu être lu.", error);
                return [];
            }
        }
        function saveCart() {
            try { localStorage.setItem("nova-cart", JSON.stringify(cart)); }
            catch (error) { console.warn("Le panier n’a pas pu être enregistré sur cet appareil.", error); }
        }
        function formatPrice(amount) {
            return new Intl.NumberFormat("fr-FR", { style: "currency", currency: "XOF" }).format(amount);
        }
        function renderCategories() {
            categoryList.innerHTML = categories.map(category =>
                `<button class="category-button${category === activeCategory ? " active" : ""}" type="button" data-category="${category}" aria-pressed="${category === activeCategory}">${category}</button>`
            ).join("");
        }
        function renderProducts() {
            const query = searchInput.value.trim().toLocaleLowerCase("fr");
            const visibleProducts = products.filter(product => {
                const matchesCategory = activeCategory === "Tout voir" || product.category === activeCategory;
                const matchesSearch = `${product.name} ${product.category} ${product.collection}`.toLocaleLowerCase("fr").includes(query);
                return matchesCategory && matchesSearch;
            });
            if (!visibleProducts.length) {
                productGrid.innerHTML = '<p class="empty-results">Aucun article ne correspond à votre recherche.</p>';
                return;
            }
            productGrid.innerHTML = visibleProducts.map(product => `
                <article class="product-card">
                    <div class="product-image-wrap">
                        <img src="${product.image}" alt="${product.alt}" loading="lazy">
                        ${product.label ? `<span class="product-label">${product.label}</span>` : ""}
                        <button class="quick-add" type="button" data-add="${product.id}" aria-label="Ajouter ${product.name} au panier">AJOUTER AU PANIER ＋</button>
                    </div>
                    <div class="product-info"><div><h3>${product.name}</h3><p>${product.collection}</p></div><span class="product-price">${formatPrice(product.price)}</span></div>
                </article>
            `).join("");
        }
        function renderCart() {
            const count = cart.reduce((total, item) => total + item.quantity, 0);
            const subtotal = cart.reduce((total, item) => total + products.find(entry => entry.id === item.id).price * item.quantity, 0);
            document.getElementById("cart-count").textContent = count;
            document.getElementById("cart-heading-count").textContent = `(${count})`;
            document.getElementById("cart-subtotal").textContent = formatPrice(subtotal);
            document.getElementById("checkout-message").textContent = "";
            if (!cart.length) {
                document.getElementById("cart-items").innerHTML = '<p class="cart-empty">Votre panier est encore tout léger.<br>Un coup de cœur vous attend ?</p>';
                return;
            }
            document.getElementById("cart-items").innerHTML = cart.map(item => {
                const product = products.find(entry => entry.id === item.id);
                return `
                    <article class="cart-item">
                        <img src="${product.image}" alt="">
                        <div><h3>${product.name}</h3><p>${formatPrice(product.price)}</p>
                            <div class="quantity-controls" aria-label="Quantité de ${product.name}">
                                <button type="button" data-quantity="${product.id}" data-delta="-1" aria-label="Retirer un article">−</button>
                                <span>${item.quantity}</span>
                                <button type="button" data-quantity="${product.id}" data-delta="1" aria-label="Ajouter un article">+</button>
                            </div>
                        </div>
                        <span class="cart-item-price">${formatPrice(product.price * item.quantity)}</span>
                    </article>
                `;
            }).join("");
        }
        function addToCart(id) {
            const existing = cart.find(item => item.id === id);
            if (existing) existing.quantity += 1;
            else cart.push({ id, quantity: 1 });
            saveCart();
            renderCart();
        }
        function changeQuantity(id, delta) {
            const item = cart.find(entry => entry.id === id);
            if (!item) return;
            item.quantity += delta;
            cart = cart.filter(entry => entry.quantity > 0);
            saveCart();
            renderCart();
        }
        function setCartOpen(isOpen) {
            cartPanel.classList.toggle("open", isOpen);
            overlay.classList.toggle("open", isOpen);
            cartPanel.setAttribute("aria-hidden", String(!isOpen));
            document.body.style.overflow = isOpen ? "hidden" : "";
            if (isOpen) document.getElementById("close-cart").focus();
        }
        categoryList.addEventListener("click", event => {
            const button = event.target.closest("[data-category]");
            if (!button) return;
            activeCategory = button.dataset.category;
            renderCategories();
            renderProducts();
        });
        productGrid.addEventListener("click", event => {
            const button = event.target.closest("[data-add]");
            if (!button) return;
            addToCart(button.dataset.add);
            button.textContent = "AJOUTÉ AU PANIER ✓";
            window.setTimeout(() => { button.textContent = "AJOUTER AU PANIER ＋"; }, 1000);
        });
        document.getElementById("cart-items").addEventListener("click", event => {
            const button = event.target.closest("[data-quantity]");
            if (button) changeQuantity(button.dataset.quantity, Number(button.dataset.delta));
        });
        document.querySelectorAll("[data-category-link]").forEach(link => {
            link.addEventListener("click", () => {
                activeCategory = link.dataset.categoryLink;
                renderCategories();
                renderProducts();
            });
        });
        searchInput.addEventListener("input", renderProducts);
        document.getElementById("open-cart").addEventListener("click", () => setCartOpen(true));
        document.getElementById("close-cart").addEventListener("click", () => setCartOpen(false));
        overlay.addEventListener("click", () => setCartOpen(false));
        document.addEventListener("keydown", event => { if (event.key === "Escape") setCartOpen(false); });
        document.getElementById("checkout").addEventListener("click", () => {
            const message = document.getElementById("checkout-message");
            message.textContent = cart.length
                ? "Cette boutique est une démonstration : le paiement en ligne n’est pas activé."
                : "Ajoutez un article à votre panier avant de continuer.";
        });
loadProducts();