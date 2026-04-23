// main.js - Ajuste do arquivo principal (Adicione ou atualize suas funções)

// Busca dados da Home
async function fetchHomeData() {
    try {
        const response = await fetch('php/get_home_data.php');
        const data = await response.json();

        if (data.success) {
            renderNavCategories(data.categorias);
            renderProductGrid(data.destaques, 'destaques-container');
            renderProductGrid(data.feed, 'feed-container');
        }
    } catch (error) {
        console.error("Erro ao carregar DB:", error);
    }
}

// Renderiza o scroll de categorias
function renderNavCategories(categorias) {
    const container = document.getElementById('category-container');
    if (!container) return;
    container.innerHTML = '';

    // Links fixos base do ecossistema
    let html = `<span class="cat-link" onclick="goTo('home')">HOME</span>`;
    html += `<span class="cat-link" onclick="goTo('catalogo')">VER TUDO</span>`;

    categorias.forEach(cat => {
        html += `<span class="cat-link" onclick="goToCategoria('${cat.slug}')">${cat.nome}</span>`;
    });
    
    container.innerHTML = html;
}

// Renderiza a Vitrine (Cards Reais do Banco)
function renderProductGrid(produtos, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = '';

    produtos.forEach(prod => {
        // Fallback de imagem
        const imgSrc = prod.imagem_principal || 'assets/img/placeholder.jpg';
        
        const card = `
            <div class="product-card">
                <div class="p-image-wrap" onclick="goToProduto(${prod.id})" style="cursor:pointer;">
                    <img src="${imgSrc}" alt="${prod.nome}" loading="lazy">
                </div>
                <div class="p-info">
                    <span class="p-brand">${prod.marca || 'ARQON EXCLUSIVE'}</span>
                    <h3 class="p-title">${prod.nome}</h3>
                    <div class="p-bottom">
                        <div class="p-price">R$ ${parseFloat(prod.valor_diaria).toFixed(2)} <span>/dia</span></div>
                        <button class="btn-rent" onclick="addToCart(${prod.id})">RESERVAR</button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', card);
    });
}

// Atualização no motor do SPA para chamar o DB na Home
async function renderMain(pageName) {
    const main = document.getElementById("app-main");
    if (!main) return;

    main.style.opacity = 0;

    try {
        const fileName = (pageName === "home") ? "main.html" : `${pageName}.html`;
        const response = await fetch(fileName);
        if (!response.ok) throw new Error(`Página ${fileName} não encontrada.`);
        
        main.innerHTML = await response.text();

        setTimeout(() => {
            if (typeof setupMainInteractions === 'function') setupMainInteractions();
            
            // Dispara a busca no MySQL apenas se for a Home
            if (pageName === "home") {
                fetchHomeData();
            }
            
            main.style.opacity = 1;
        }, 300);

    } catch (error) {
        console.error("Erro no roteamento:", error);
    }
}

// Auxiliares
function scrollToElement(id) {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' });
}
function goToProduto(id) {
    console.log("Navegando para o produto:", id);
    // Aqui você chama seu renderMain('produto') passando o ID via localStorage ou URL params
}
function addToCart(id) {
    console.log("Produto adicionado ao carrinho:", id);
    // Lógica do carrinho
    alert("Peça adicionada ao Vault (Carrinho)!");
}

document.addEventListener('DOMContentLoaded', () => {
    initScrollReveal();
    fetchDynamicDrops();
});

// ==========================================
// 1. ANIMAÇÕES DE SCROLL (Intersection Observer)
// ==========================================
function initScrollReveal() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target); // Anima só uma vez
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal-section').forEach(section => {
        observer.observe(section);
    });
}

// ==========================================
// 2. INTEGRAÇÃO COM BACKEND (API FETCH)
// ==========================================
async function fetchDynamicDrops() {
    const grid = document.getElementById('dynamic-drops-grid');
    if (!grid) return;

    try {
        // Simulação de delay para demonstrar o Skeleton Loading premium (opcional, remova em prod)
        await new Promise(resolve => setTimeout(resolve, 800));

        const response = await fetch('php/get_destaques.php');
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            grid.innerHTML = ''; // Limpa os skeletons
            
            result.data.forEach(produto => renderProductCard(produto, grid));
        } else {
            grid.innerHTML = '<p style="color:#aaa;">Cofre em atualização. Novos drops em breve.</p>';
        }
    } catch (error) {
        console.error("Erro na API ARQON:", error);
        grid.innerHTML = '<p style="color:#ff4444;">Erro na conexão com o mainframe.</p>';
    }
}

// ==========================================
// 3. RENDERIZAÇÃO DO CARD (DOM BUILDER)
// ==========================================
function renderProductCard(produto, container) {
    const valorFormatado = parseFloat(produto.valor_diaria).toFixed(2).replace('.', ',');
    const imgUrl = produto.imagem_principal || 'assets/img/placeholder.webp';
    
    // Lógica de Badges
    let badgeHTML = '';
    if (produto.novo) badgeHTML = `<span class="badge new">NEW DROP</span>`;
    else if (produto.mais_alugado) badgeHTML = `<span class="badge hot">HOT</span>`;

    const cardHTML = `
        <article class="proto-card fade-up">
            ${badgeHTML}
            <img src="${imgUrl}" alt="${produto.nome}" class="proto-card-img" loading="lazy">
            <div class="proto-card-info">
                <span class="proto-card-cat" style="color:var(--proto-gold); font-size: 0.7rem; letter-spacing: 1px;">
                    ${produto.categoria.toUpperCase()}
                </span>
                <h4 class="proto-card-title" style="margin: 5px 0;">${produto.nome}</h4>
                <div class="proto-card-price" style="font-family: Arial; font-size: 0.9rem;">
                    R$ ${valorFormatado} <span style="font-size: 0.7rem; color:#aaa;">/ dia</span>
                </div>
            </div>
            <button class="btn-alugar" onclick="goTo('produto', ${produto.id})">
                RESERVAR AGORA
            </button>
        </article>
    `;
    
    container.insertAdjacentHTML('beforeend', cardHTML);
}

// ==========================================
// 4. ROUTER SIMULADO
// ==========================================
function goTo(route, id = null) {
    let url = `${route}.php`;
    if (id) url += `?id=${id}`;
    console.log(`[ARQON ROUTER] Navegando para: ${url}`);
    // Descomente em produção: window.location.href = url;
}