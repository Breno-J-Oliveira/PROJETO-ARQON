const canvas = document.getElementById('matrix');
const ctx = canvas.getContext('2d');

let width, height, columns, drops;
const fontSize = 18;
const katakana = 'アァカサタナハマヤャラワガザダバパイィキシチニヒミリヰギジヂビピウゥクスツヌフムユュルグズブヅプエェケセテネヘメレゲゼデベペオォコソトノホモヨョロゴゾドボポヴッン';
const letters = katakana.split('');

// Função para iniciar ou recalcular a tela
function initMatrix() {
    width = canvas.width = window.innerWidth;
    height = canvas.height = window.innerHeight;
    
    columns = Math.floor(width / fontSize);
    drops = [];
    for (let x = 0; x < columns; x++) {
        drops[x] = 1;
    }
}

// Inicia a Matrix pela primeira vez
initMatrix();

function draw() {
    // Fundo que cria o rastro (0.1 é a transparência do rastro preto)
    ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
    ctx.fillRect(0, 0, width, height);

    // Cor da letra (0.3 é a opacidade da letra)
    ctx.fillStyle = 'rgba(170, 170, 170, 0.3)'; 
    ctx.font = fontSize + 'px "Courier New", monospace';

    for (let i = 0; i < drops.length; i++) {
        const text = letters[Math.floor(Math.random() * letters.length)];
        
        ctx.fillText(text, i * fontSize, drops[i] * fontSize);

        // Reseta a gota quando chega no final
        if (drops[i] * fontSize > height && Math.random() > 0.975) {
            drops[i] = 0;
        }
        
        drops[i]++;
    }
}

// Roda a animação
setInterval(draw, 50);

// Se o usuário redimensionar a tela, a Matrix se ajusta sem quebrar
window.addEventListener('resize', initMatrix);