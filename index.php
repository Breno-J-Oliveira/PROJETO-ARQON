<?php
/**
 * ARQON - THE VAULT | Professional Central Router
 * Status: Validado e Blindado
 */

declare(strict_types=1);

require_once 'config.php';
require_once 'Database.php';

// --- [PASSO 5] MIDDLEWARE DE SEGURANÇA E CORS ---
header("Access-Control-Allow-Origin: " . ALLOWED_ORIGIN);
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Arqon-Token");
header("Content-Type: application/json; charset=UTF-8");

// Proteções de Hardening
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// Resposta para Preflight do Navegador
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Helper para padronizar as respostas da API ARQON
 */
function sendResponse(array $data, int $code = 200) {
    http_response_code($code);
    echo json_encode(array_merge([
        "status" => $code < 400 ? "success" : "error",
        "timestamp" => date('Y-m-d H:i:s')
    ], $data));
    exit;
}

// Captura de Input JSON global
$input = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Processamento da Rota
    $basePath = '/arqon/'; 
    $route = trim(str_replace($basePath, '', $uri), '/');
    $parts = explode('/', $route);

    // --- ROTEAMENTO (O MAESTRO) ---
    switch ($parts[0]) {
        case 'api':
            $resource = $parts[1] ?? null;
            
            if ($resource === 'produtos') {
                // Aqui o Maestro passará o bastão para o Controller no futuro
                sendResponse([
                    "message" => "Acessando o Vault", 
                    "metodo" => $method,
                    "payload_recebido" => $input // Mostra que o JSON já está funcionando
                ]);
            }
            
            sendResponse(["error" => "Recurso não encontrado"], 404);
            break;

        case '':
            sendResponse([
                "system" => SYSTEM_NAME, 
                "status" => "online", 
                "environment" => ENVIRONMENT
            ]);
            break;

        default:
            sendResponse(["error" => "Rota inválida"], 404);
    }

} catch (Throwable $e) {
    // Registro de erro silencioso no servidor
    error_log("ARQON_CRITICAL: " . $e->getMessage());

    // Resposta blindada para o cliente
    sendResponse([
        "error" => "Falha interna no Cofre",
        "details" => (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? $e->getMessage() : "Contate o suporte VIP."
    ], 500);
}