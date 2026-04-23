<?php
// PHP/get_new_drops.php
declare(strict_types=1);
require_once 'conexao.php'; // Seu arquivo que instancia o $pdo

header('Content-Type: application/json; charset=utf-8');

try {
    // Busca os produtos mais recentes que estão disponíveis, trazendo o nome da categoria
    $stmt = $pdo->prepare("
        SELECT 
            p.id, 
            p.nome, 
            p.valor_diaria, 
            p.imagem_principal, 
            c.nome AS categoria_nome 
        FROM produtos p
        JOIN categorias c ON p.categoria_id = c.id
        WHERE p.status_disponibilidade = 'disponivel'
        ORDER BY p.created_at DESC
        LIMIT 6
    ");
    $stmt->execute();
    $drops = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $drops]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao carregar coleção.']);
}
?>