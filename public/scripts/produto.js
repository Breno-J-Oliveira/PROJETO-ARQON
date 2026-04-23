/* product-details.js - Lógica interativa para PDP */

function initProductDetails() {
    // 1. Galeria de Imagens (Thumbnails para Main Image)
    const mainImg = document.getElementById('mainImageDisplay');
    const thumbBtns = document.querySelectorAll('.thumb-btn');

    if (mainImg && thumbBtns.length > 0) {
        thumbBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Atualizar classe active
                thumbBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Pegar a imagem do thumbnail clicado
                const newSrc = this.querySelector('img').src;
                
                // Animação de fade rápida
                mainImg.style.opacity = '0.4';
                setTimeout(() => {
                    mainImg.src = newSrc;
                    mainImg.style.opacity = '0.9';
                }, 150);
            });
        });
    }

    // 2. Seletor de Tamanhos
    const sizeBtns = document.querySelectorAll('.pdp-selector-group .size-btn');
    sizeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.disabled) return;
            
            sizeBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // 3. Acordeões de Detalhes
    const accItems = document.querySelectorAll('.accordion-item');
    accItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        
        header.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Opcional: Fechar outros (Comportamento exclusivo)
            accItems.forEach(otherItem => {
                otherItem.classList.remove('active');
                otherItem.querySelector('.accordion-content').style.maxHeight = null;
                otherItem.querySelector('.acc-icon').textContent = '+';
            });

            // Toggle do atual
            if (!isActive) {
                item.classList.add('active');
                const content = item.querySelector('.accordion-content');
                content.style.maxHeight = content.scrollHeight + "px";
                item.querySelector('.acc-icon').textContent = '−';
            }
        });
    });

    // Configura altura inicial do acordeão ativo
    const activeAcc = document.querySelector('.accordion-item.active .accordion-content');
    if(activeAcc) {
        activeAcc.style.maxHeight = activeAcc.scrollHeight + "px";
    }

    // 4. Lógica de Mock de Carrinho/Reserva
    const btnAddToCart = document.getElementById('btnAddToCart');
    if (btnAddToCart) {
        btnAddToCart.addEventListener('click', function() {
            const originalText = this.querySelector('.btn-text').textContent;
            const originalIcon = this.querySelector('.btn-icon').textContent;
            
            // Efeito visual de carregamento/sucesso
            this.querySelector('.btn-text').textContent = 'ADDING TO VAULT...';
            this.querySelector('.btn-icon').textContent = '⟳';
            
            setTimeout(() => {
                this.style.background = '#4caf50';
                this.style.color = '#fff';
                this.querySelector('.btn-text').textContent = 'PIECE SECURED';
                this.querySelector('.btn-icon').textContent = '✓';
                
                // Dispararia abertura lateral do carrinho aqui (Módulo 8)
                // if(typeof openRentCart === 'function') openRentCart();

                setTimeout(() => {
                    this.style.background = '';
                    this.style.color = '';
                    this.querySelector('.btn-text').textContent = originalText;
                    this.querySelector('.btn-icon').textContent = originalIcon;
                }, 3000);
            }, 800);
        });
    }
}

// Inicializa a lógica ao renderizar a view via SPA
initProductDetails();