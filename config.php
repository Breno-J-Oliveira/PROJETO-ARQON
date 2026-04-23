<?php
/**
 * ARQON - THE VAULT | Global Configuration Center
 * Finalidade: Centralizar constantes, chaves de segurança e definições de ambiente.
 * * SEGURANÇA: Este arquivo contém credenciais sensíveis. 
 * NUNCA envie este arquivo para repositórios públicos (GitHub/GitLab).
 */

declare(strict_types=1);

// 1. CONFIGURAÇÕES DE AMBIENTE
// Mude para 'production' quando o site estiver online
define('ENVIRONMENT', 'development'); 

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// 2. CREDENCIAIS DO BANCO DE DADOS (The Vault Core)
define('DB_HOST', 'localhost');
define('DB_NAME', 'arqon');
define('DB_USER', 'root');
define('DB_PASS', 'Senai@118');
define('DB_CHARSET', 'utf8mb4');

// 3. IDENTIDADES MESTRES (Security Identities)
define('ADMIN_EMAIL', 'admin@arqon.com');
define('SYSTEM_NAME', 'ARQON - THE VAULT');

// 4. SEGURANÇA E CRIPTOGRAFIA
define('JWT_SECRET_KEY', 'arqon_vault_secure_key_2024_!@#'); 
define('HASH_ALGO', PASSWORD_ARGON2ID); 

// 5. [PASSO 5] CONFIGURAÇÕES DE API / CORS
// Define quem tem permissão para acessar o Cofre
define('ALLOWED_ORIGIN', (ENVIRONMENT === 'development') 
    ? 'http://localhost:3000' // Porta comum para React/Vue/LiveServer
    : 'https://www.arqon.com.br'
);

// 6. CAMINHOS E URLS (Paths)
define('BASE_URL', 'http://localhost/arqon/'); 
define('UPLOAD_PATH', __DIR__ . '/public/uploads/');

// 7. DEFINIÇÕES DE LOCALIZAÇÃO (Fuso Horário)
date_default_timezone_set('America/Sao_Paulo');

/*
 * NOTA DE USO:
 * O arquivo index.php usará a constante ALLOWED_ORIGIN para aplicar os headers de segurança.
 */