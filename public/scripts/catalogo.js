/* catalog.js - Lógica Premium do Arquivo */

function initArchiveSystems() {
    // 1. Controle Mobile do Sidebar de Filtros
    const openBtn = document.getElementById('openSidebar');
    const closeBtn = document.getElementById('closeSidebar');
    const sidebar = document.getElementById('archiveSidebar');

    if (openBtn && sidebar) {
        openBtn.addEventListener('click', () => {
            sidebar.classList.add('open');
            document.body.style.overflow = 'hidden'; // Impede scroll no fundo
        });
    }

    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', () => {
            sidebar.classList.remove('open');
            document.body.style.overflow = '';
        });
    }

    // 2. Filtro de Categorias com Reflow de Grid
    const catItems = document.querySelectorAll('#categoryFilter li');
    const gridItems = document.querySelectorAll('.arq-item');

    if (catItems.length > 0) {
        catItems.forEach(item => {
            item.addEventListener('click', (e) => {
                // Remove active class
                catItems.forEach(i => i.classList.remove('active'));
                e.currentTarget.classList.add('active');

                const filter = e.currentTarget.getAttribute('data-filter');

                let delayCounter = 0;
                gridItems.forEach(gridItem => {
                    const category = gridItem.getAttribute('data-category');
                    
                    // Reset animation state
                    gridItem.style.animation = 'none';
                    gridItem.style.opacity = '0';
                    
                    if (filter === 'all' || filter === category) {
                        gridItem.style.display = 'flex';
                        // Força o reflow para reiniciar animação
                        void gridItem.offsetWidth; 
                        
                        // Cascata editorial
                        gridItem.style.animation = `gridItemReveal 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards ${delayCounter * 0.1}s`;
                        delayCounter++;
                    } else {
                        gridItem.style.display = 'none';
                    }
                });
                
                // Se mobile, fecha sidebar ao escolher categoria
                if(window.innerWidth <= 1024 && sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                    document.body.style.overflow = '';
                }
            });
        });
    }

    // 3. Cascateamento de animação inicial
    let initialDelay = 0;
    gridItems.forEach(item => {
        item.style.animationDelay = `${initialDelay * 0.15}s`;
        initialDelay++;
    });

    // 4. Seletor Visual de Tamanho / Cor (Apenas UI por enquanto)
    const sizeBtns = document.querySelectorAll('.size-btn');
    sizeBtns.forEach(btn => {
        btn.addEventListener('click', () => btn.classList.toggle('active'));
    });

    const colorBtns = document.querySelectorAll('.color-btn');
    colorBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            colorBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });
}

// Inicializa scripts ao renderizar via SPA
initArchiveSystems();