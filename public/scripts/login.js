// scripts/login.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('arqon-register-form');
  let mode = "login"; // Inicia na tela de login por padrão

  // =========================
  // TEMPLATES SPA
  // =========================
  
  const registerTemplate = `
    <div class="arqon-form-header">
      <h2>JOIN THE ELITE</h2>
    </div>

    <div class="arqon-input-group">
      <input type="text" id="arqon-name" name="name" required placeholder=" ">
      <label for="arqon-name">NOME COMPLETO</label>
      <span class="arqon-error-msg" id="error-name"></span>
    </div>

    <div class="arqon-input-group">
      <input type="email" id="arqon-email" name="email" required placeholder=" ">
      <label for="arqon-email">E-MAIL</label>
      <span class="arqon-error-msg" id="error-email"></span>
    </div>

    <div class="arqon-input-group">
      <input type="password" id="arqon-password" name="password" required placeholder=" ">
      <label for="arqon-password">SENHA</label>
      <span class="arqon-error-msg" id="error-password"></span>
    </div>

    <div class="arqon-input-group">
      <input type="password" id="arqon-confirm-password" name="confirm-password" required placeholder=" ">
      <label for="arqon-confirm-password">CONFIRMAR SENHA</label>
      <span class="arqon-error-msg" id="error-confirm-password"></span>
    </div>

    <button type="submit" class="arqon-btn-primary">
      <span class="btn-text">CRIAR CONTA</span>
    </button>

    <div class="arqon-form-footer">
      <p>JÁ TEM CONTA? <a href="#" id="go-login" class="arqon-link">LOGIN</a></p>
    </div>
  `;

  const loginTemplate = `
    <div class="arqon-form-header">
      <h2>AUTHENTICATION</h2>
      <p>Acesse seu acervo digital. Insira suas credenciais para continuar.</p>
    </div>
    
    <div class="arqon-input-group">
      <input type="email" id="login-email" name="email" required placeholder=" ">
      <label for="login-email">E-MAIL</label>
      <span class="arqon-error-msg" id="error-email"></span>
    </div>

    <div class="arqon-input-group">
      <input type="password" id="login-password" name="password" required placeholder=" ">
      <label for="login-password">SENHA</label>
      <span class="arqon-error-msg" id="error-password"></span>
    </div>

    <div class="arqon-form-options">
      <label class="arqon-checkbox-label">
        <input type="checkbox" id="login-remember" name="remember">
        <span class="arqon-checkbox-custom"></span>
        LEMBRAR DE MIM
      </label>
      <a href="#" id="go-recovery" class="arqon-link-subtle">ESQUECEU A SENHA?</a>
    </div>

    <button type="submit" class="arqon-btn-primary">
      <span class="btn-text">ENTRAR</span>
      <span class="btn-glow"></span>
    </button>

    <div class="arqon-form-footer">
      <p>NOVO NA ARQON? <a href="#" id="go-register" class="arqon-link">CRIAR CONTA</a></p>
    </div>
  `;

  const recoveryTemplate = `
    <div class="arqon-form-header">
      <h2>RECOVER ACCESS</h2>
      <p>Insira seu e-mail para redefinir sua senha.</p>
    </div>
    
    <div class="arqon-input-group">
      <input type="email" id="recovery-email" name="email" required placeholder=" ">
      <label for="recovery-email">E-MAIL</label>
      <span class="arqon-error-msg" id="error-email"></span>
    </div>

    <button type="submit" class="arqon-btn-primary">
      <span class="btn-text">ENVIAR RECUPERAÇÃO</span>
      <span class="btn-glow"></span>
    </button>

    <div class="arqon-form-footer">
      <p>LEMBROU A SENHA? <a href="#" id="go-login" class="arqon-link">VOLTAR PARA LOGIN</a></p>
    </div>
  `;

  // =========================
  // SISTEMA DE RENDERIZAÇÃO (SPA)
  // =========================

  function renderForm(type) {
    mode = type;
    
    // Animação: Fade Out
    form.style.opacity = '0';

    setTimeout(() => {
      // Injeção dinâmica do template
      if (type === "register") form.innerHTML = registerTemplate;
      if (type === "login") form.innerHTML = loginTemplate;
      if (type === "recovery") form.innerHTML = recoveryTemplate;
      
      // Animação: Fade In
      form.style.opacity = '1';

      // Reativa captura de inputs, eventos e botões do novo DOM gerado
      setupFormEvents(); 
    }, 300); // Aguarda o CSS transition (0.3s)
  }

  // =========================
  // SETUP DE EVENTOS E VALIDAÇÃO
  // =========================

  function setupFormEvents() {
    const nameInput = form.querySelector('[name="name"]');
    const emailInput = form.querySelector('[name="email"]');
    const passwordInput = form.querySelector('[name="password"]');
    const confirmPasswordInput = form.querySelector('[name="confirm-password"]');

    // Funções de Feedback Visual (Erros)
    const showError = (inputElement, message) => {
      if (!inputElement) return;
      const errorSpan = document.getElementById(`error-${inputElement.name}`);
      if (!errorSpan) return;

      errorSpan.textContent = message;
      errorSpan.classList.add('active');
      inputElement.style.borderBottomColor = '#ff4a4a';
    };

    const clearError = (inputElement) => {
      if (!inputElement) return;
      const errorSpan = document.getElementById(`error-${inputElement.name}`);
      if (!errorSpan) return;

      errorSpan.textContent = '';
      errorSpan.classList.remove('active');
      inputElement.style.borderBottomColor = 'var(--arqon-border)';
    };

    // Funções de Validação Modulares
    const validateName = () => {
      if (!nameInput) return true;
      if (nameInput.value.trim().length < 3) {
        showError(nameInput, 'Mínimo 3 caracteres exigidos.');
        return false;
      }
      clearError(nameInput);
      return true;
    };

    const validateEmail = () => {
      if (!emailInput) return true;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(emailInput.value.trim())) {
        showError(emailInput, 'Insira um e-mail com formato válido.');
        return false;
      }
      clearError(emailInput);
      return true;
    };

    const validatePassword = () => {
      if (!passwordInput) return true;
      const minLength = mode === "register" ? 6 : 1;
      const msgError = mode === "register" ? 'Mínimo de 6 caracteres.' : 'A senha é obrigatória.';

      if (passwordInput.value.length < minLength) {
        showError(passwordInput, msgError);
        return false;
      }
      clearError(passwordInput);
      return true;
    };

    const validateConfirmPassword = () => {
      if (!confirmPasswordInput) return true; 
      if (confirmPasswordInput.value !== passwordInput.value) {
        showError(confirmPasswordInput, 'As senhas não coincidem.');
        return false;
      }
      clearError(confirmPasswordInput);
      return true;
    };

    // Listeners de tempo real para inputs
    if (nameInput) nameInput.addEventListener('blur', validateName);
    if (emailInput) emailInput.addEventListener('blur', validateEmail);
    if (passwordInput) passwordInput.addEventListener('input', validatePassword);
    if (confirmPasswordInput) confirmPasswordInput.addEventListener('input', validateConfirmPassword);

    // =========================
    // NAVEGAÇÃO SPA (Botões de Troca)
    // =========================
    
    const goLoginBtn = document.getElementById("go-login");
    const goRegisterBtn = document.getElementById("go-register");
    const goRecoveryBtn = document.getElementById("go-recovery");

    if (goLoginBtn) {
      goLoginBtn.addEventListener("click", (e) => {
        e.preventDefault();
        renderForm("login");
      });
    }

    if (goRegisterBtn) {
      goRegisterBtn.addEventListener("click", (e) => {
        e.preventDefault();
        renderForm("register");
      });
    }

    if (goRecoveryBtn) {
      goRecoveryBtn.addEventListener("click", (e) => {
        e.preventDefault();
        renderForm("recovery");
      });
    }

    // =========================
    // SUBMIT DO FORMULÁRIO (CONEXÃO REAL COM O BACK-END)
    // =========================
    form.onsubmit = async (e) => {
      e.preventDefault();

      const isNameValid = validateName();
      const isEmailValid = validateEmail();
      const isPasswordValid = validatePassword();
      const isConfirmValid = validateConfirmPassword();

      if (isNameValid && isEmailValid && isPasswordValid && isConfirmValid) {
        const btnText = form.querySelector('.btn-text');
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = btnText.textContent;
        
        btnText.textContent = mode === "recovery" ? 'ENVIANDO...' : 'PROCESSANDO...';
        submitBtn.style.pointerEvents = 'none';
        submitBtn.style.opacity = '0.8';

        // LÓGICA DE LOGIN REAL
        if (mode === "login") {
          try {
            // APONTA PARA A PASTA 'PHP'
            const response = await fetch('PHP/login.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ 
                email: emailInput.value.trim(), 
                senha: passwordInput.value 
              })
            });

            const data = await response.json();

            if (data.status === 'success') {
              // Sucesso: Botão Dourado
              btnText.textContent = 'ACESSO LIBERADO';
              submitBtn.style.color = 'var(--arqon-bg-dark)';
              submitBtn.style.background = 'var(--arqon-gold)';
              submitBtn.style.opacity = '1';

              // Pega a rota que o PHP enviou. Se der qualquer erro, força ir para o index
              const rotaDestino = data.redirect ? data.redirect : 'index.html';
              
              // Mostra no console para onde ele está tentando ir (Aperte F12 para ver)
              console.log("O sistema está tentando te mandar para: " + rotaDestino);

              // Redirecionamento forçado usando window.location.assign
              setTimeout(() => {
                window.location.assign(rotaDestino);
              }, 1000);
              
            } else {
              // Erro de credencial retornado pelo PHP
              showError(passwordInput, data.message);
              resetBtn();
            }

          } catch (error) {
            console.error("Erro na comunicação com o servidor:", error);
            showError(passwordInput, "Erro de rota ou servidor inativo.");
            resetBtn();
          }
        } 
        else {
          // Lógica de Registrar e Recuperar Senha (Futuro)
          setTimeout(() => { resetBtn(); }, 1000);
        }

        // Função para resetar botão em caso de erro
        function resetBtn() {
          btnText.textContent = originalBtnText;
          submitBtn.style.pointerEvents = 'auto';
          submitBtn.style.opacity = '1';
          submitBtn.style.background = 'transparent';
          submitBtn.style.color = 'var(--arqon-gold)';
        }
      }
    };
  }

  // =========================
  // INIT
  // =========================
  renderForm("login"); 

});