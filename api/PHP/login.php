<?php
// PHP/login.php
session_start();
header('Content-Type: application/json');

// 1. CORREÇÃO DO CAMINHO DO BANCO DE DADOS
// Aponta para o arquivo conexao.php que está na mesma pasta
require_once 'conexao.php';

// Captura o JSON enviado pelo seu login.js
$input = json_decode(file_get_contents('php://input'), true);

$email = $input['email'] ?? '';
$senha = $input['senha'] ?? '';

if (empty($email) || empty($senha)) {
    echo json_encode(['status' => 'error', 'message' => 'Preencha suas credenciais.']);
    exit;
}

try {
    // Busca o usuário no banco (certifique-se de que a variável de conexão em conexao.php se chama $pdo)
    $stmt = $pdo->prepare("SELECT id, senha_hash, perfil, status FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Valida a senha criptografada
    if ($user && password_verify($senha, $user['senha_hash'])) {
        
        if ($user['status'] !== 'ativo') {
            echo json_encode(['status' => 'error', 'message' => 'Conta inativa ou suspensa.']);
            exit;
        }

        // Renova o ID da sessão por segurança
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_perfil'] = $user['perfil'];

        // 2. CORREÇÃO DAS ROTAS (Baseado na sua árvore de arquivos)
        $rotas = [
            'usuario'       => 'index.html',
            'admin'         => 'admin.html',
            // Adicione os outros arquivos HTML aqui quando você os criar:
            'desenvolvedor' => 'console.html', 
            'entregador'    => 'panel.html'    
        ];

        echo json_encode([
            'status' => 'success',
            'redirect' => $rotas[$user['perfil']] ?? 'index.html' // Redireciona para o index.html por padrão
        ]);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Credenciais de acesso inválidas.']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro interno do servidor.']);
}
?>