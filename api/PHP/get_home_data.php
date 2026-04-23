<?php
// php/get_home_data.php
declare(strict_types=1);
require_once 'conexao.php'; // Seu arquivo com a conexão $pdo

header('Content-Type: application/json');

try {
    // 1. Busca Categorias Rápidas
    $stmtCat = $pdo->prepare("SELECT id, nome, slug FROM categorias WHERE ativo = 1 ORDER BY ordem ASC LIMIT 8");
    $stmtCat->execute();
    $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

    // 2. Busca Produtos em Destaque (Flash Drops / Novidades)
    $stmtDestaques = $pdo->prepare("
        SELECT id, nome, slug, valor_diaria, imagem_principal, categoria, marca, status_aluguel 
        FROM produtos 
        WHERE destaque = 1 AND disponibilidade = 'disponivel' 
        ORDER BY created_at DESC LIMIT 6
    ");
    $stmtDestaques->execute();
    $destaques = $stmtDestaques->fetchAll(PDO::FETCH_ASSOC);

    // 3. Busca o Feed Principal (Vitrine densa estilo e-commerce)
    $stmtFeed = $pdo->prepare("
        SELECT id, nome, slug, valor_diaria, imagem_principal, categoria, marca, status_aluguel 
        FROM produtos 
        WHERE disponibilidade = 'disponivel' 
        ORDER BY rand() LIMIT 24
    ");
    $stmtFeed->execute();
    $feed = $stmtFeed->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'categorias' => $categorias,
        'destaques' => $destaques,
        'feed' => $feed
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao processar catálogo ARQON.']);
}
?>