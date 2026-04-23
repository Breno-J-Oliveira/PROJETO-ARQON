// carregar header e footer
async function loadComponent(id, file) {
  const el = document.getElementById(id);
  const res = await fetch(file);
  el.innerHTML = await res.text();
}

// quando abrir o site
document.addEventListener("DOMContentLoaded", async () => {

  await loadComponent("header", "components/header.html");
  await loadComponent("main", "components/main.html");
  await loadComponent("footer", "components/footer.html");
  await loadComponent("jp-matrix", "components/jp-matrix.html");
  
  // coloca o conteúdo dentro dela
  renderMain();
});


const canvas = document.getElementById('matrix');
const ctx = canvas.getContext('2d');

// Ajusta o tamanho do canvas para a tela toda
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

// Letras japonesas (Katakana)
const katakana = 'アァカサタナハマヤャラワガザダバパイィキシチニヒミリヰギジヂビピウゥクスツヌフムユュルグズブヅプエェケセテネヘメレゲゼデベペオォコソトノホモヨョロゴゾドボポヴッン';
const letters = katakana.split('');

const fontSize = 18;
const columns = canvas.width / fontSize;

// Array para controlar a queda de cada coluna
const drops = [];
for (let x = 0; x < columns; x++) {
    drops[x] = 1;
}

function draw() {
    // Fundo semitransparente para criar o rastro da chuva
    ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Cor do texto (Cinza com a opacidade controlada no último número)
    ctx.fillStyle = 'rgba(170, 170, 170, 0.3)'; // Pode alterar o 0.3 para deixar mais ou menos transparente
    ctx.font = fontSize + 'px "Courier New", monospace';

    // Loop pelas colunas
    for (let i = 0; i < drops.length; i++) {
        const text = letters[Math.floor(Math.random() * letters.length)];
        
        // Desenha o caractere
        ctx.fillText(text, i * fontSize, drops[i] * fontSize);

        // Se a gota chegou no fim da tela e aleatoriamente resetar
        if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
            drops[i] = 0;
        }
        
        // Faz a gota descer
        drops[i]++;
    }
}

// Velocidade da animação (quanto menor, mais rápido)
setInterval(draw, 50);

// Recalcula o tamanho se a janela for redimensionada
window.addEventListener('resize', () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
});