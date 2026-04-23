<?php
/**
 * ARQON - THE VAULT | Database Engineering & Setup Tool
 * Versão: 4.0 (Global Infrastructure & Media Assets)
 * Finalidade: Reconstrução total, Seeding de alta fidelidade e Inspetor de Objetos.
 */

declare(strict_types=1);

// --- CONFIGURAÇÕES DE AMBIENTE ---
error_reporting(E_ALL);
ini_set('display_errors', '1');

$config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => 'Senai@118',
    'db'   => 'arqon',
    'driver_options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]
];

// --- INTERFACE CSS (DARK LUXURY UI) ---
echo "<style>
    :root {
        --bg-primary: #0a0a0a;
        --bg-secondary: #121212;
        --accent: #8b0000;
        --gold: #d4af37;
        --text-main: #e0e0e0;
        --text-dim: #888;
        --border: #222;
        --success: #28a745;
        --info: #007bff;
    }
    body { 
        background: var(--bg-primary); 
        color: var(--text-main); 
        font-family: 'Inter', 'Segoe UI', Helvetica, sans-serif; 
        margin: 0; padding: 40px; line-height: 1.6;
    }
    .container { max-width: 1300px; margin: 0 auto; }
    .header { border-bottom: 1px solid var(--accent); padding-bottom: 20px; margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;}
    .header h1 { text-transform: uppercase; letter-spacing: 4px; font-weight: 300; margin: 0; }
    
    .grid-nav { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); 
        gap: 10px; margin-bottom: 30px; 
    }
    .nav-card { 
        background: var(--bg-secondary); border: 1px solid var(--border); 
        padding: 12px; text-align: center; text-decoration: none; 
        color: var(--text-dim); transition: all 0.2s ease; border-radius: 4px; font-size: 11px; text-transform: uppercase;
    }
    .nav-card:hover { border-color: var(--gold); color: #fff; background: #161616; }
    .nav-card.active { border-color: var(--gold); color: var(--gold); background: #1a1a1a; box-shadow: 0 0 15px rgba(212, 175, 55, 0.1); }

    .data-section { background: var(--bg-secondary); border: 1px solid var(--border); padding: 25px; margin-top: 20px; border-radius: 4px; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
    th { text-align: left; padding: 12px; border-bottom: 2px solid var(--border); color: var(--gold); font-weight: 500; text-transform: uppercase; font-size: 11px;}
    td { padding: 12px; border-bottom: 1px solid var(--border); color: var(--text-main); }
    tr:hover { background: rgba(255,255,255,0.03); }
    
    .badge { padding: 4px 10px; border-radius: 12px; font-size: 9px; font-weight: bold; background: #222; border: 1px solid var(--border); }
    .btn { 
        display: inline-block; padding: 12px 24px; background: var(--accent); 
        color: #fff; text-decoration: none; font-size: 12px; text-transform: uppercase; 
        letter-spacing: 2px; border: none; cursor: pointer; transition: 0.3s;
    }
    .btn-gold { background: var(--gold); color: #000; font-weight: bold; }
    .btn:hover { opacity: 0.8; transform: translateY(-2px); }
    .status-msg { padding: 20px; background: rgba(40, 167, 69, 0.05); border: 1px solid var(--success); color: var(--success); margin-bottom: 20px; border-radius: 4px; }
    code { font-family: 'Consolas', monospace; color: var(--gold); }
</style>";

try {
    $pdo = new PDO("mysql:host={$config['host']}", $config['user'], $config['pass'], $config['driver_options']);

    // --- LÓGICA DE RESET COMPLETO ---
    if (isset($_GET['op']) && $_GET['op'] === 'reset') {
        $dbName = $config['db'];
        $setupSql = "
            SET FOREIGN_KEY_CHECKS = 0;
            DROP DATABASE IF EXISTS $dbName;
            CREATE DATABASE $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            USE $dbName;

            -- 1. IAM & SECURITY
            CREATE TABLE niveis_acesso (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(50) NOT NULL UNIQUE,
                permissoes_json JSON NOT NULL,
                descricao TEXT
            ) ENGINE=InnoDB;

            CREATE TABLE usuarios (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                uuid CHAR(36) NOT NULL DEFAULT (UUID()),
                nome VARCHAR(150) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                senha_hash VARCHAR(255) NOT NULL,
                id_nivel_acesso INT UNSIGNED NOT NULL,
                status ENUM('ativo', 'inativo', 'bloqueado') DEFAULT 'ativo',
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_u_nivel FOREIGN KEY (id_nivel_acesso) REFERENCES niveis_acesso(id)
            ) ENGINE=InnoDB;

            CREATE TABLE usuarios_enderecos (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                id_usuario INT UNSIGNED NOT NULL,
                titulo VARCHAR(50) DEFAULT 'Casa',
                cep VARCHAR(10),
                logradouro VARCHAR(255),
                numero VARCHAR(20),
                bairro VARCHAR(100),
                cidade VARCHAR(100),
                padrao_entrega BOOLEAN DEFAULT FALSE,
                CONSTRAINT fk_end_user FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;

            -- 2. CATALOGUE & LUXURY ASSETS
            CREATE TABLE marcas (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL UNIQUE,
                origem_pais VARCHAR(50) DEFAULT 'Italia',
                logo_url VARCHAR(255)
            ) ENGINE=InnoDB;

            CREATE TABLE categorias (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(60) NOT NULL UNIQUE,
                tipo_grade ENUM('Vestuario', 'Calcado', 'Acessorio'),
                slug VARCHAR(100)
            ) ENGINE=InnoDB;

            CREATE TABLE produtos (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                id_marca INT UNSIGNED NOT NULL,
                id_categoria INT UNSIGNED NOT NULL,
                nome VARCHAR(255) NOT NULL,
                descricao_longa TEXT,
                valor_diaria DECIMAL(10,2) NOT NULL,
                valor_reposicao DECIMAL(10,2),
                status_vitrine BOOLEAN DEFAULT TRUE,
                CONSTRAINT fk_p_marca FOREIGN KEY (id_marca) REFERENCES marcas(id),
                CONSTRAINT fk_p_cat FOREIGN KEY (id_categoria) REFERENCES categorias(id)
            ) ENGINE=InnoDB;

            CREATE TABLE produto_midia (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                id_produto INT UNSIGNED NOT NULL,
                tipo_midia ENUM('imagem_4k', 'video_lookbook', 'modelo_3d') NOT NULL,
                url_arquivo VARCHAR(255) NOT NULL,
                ordem_exibicao INT DEFAULT 0,
                CONSTRAINT fk_midia_prod FOREIGN KEY (id_produto) REFERENCES produtos(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;

            -- 3. LOGISTICS (THE VAULT CORE)
            CREATE TABLE itens_estoque (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                id_produto INT UNSIGNED NOT NULL,
                tamanho VARCHAR(10) NOT NULL,
                cor VARCHAR(30),
                rfid_tag VARCHAR(100) UNIQUE NOT NULL,
                status_fisico ENUM('Disponivel', 'Alugado', 'Higienizacao', 'Manutencao', 'Indisponivel') DEFAULT 'Disponivel',
                ultima_leitura_rfid TIMESTAMP NULL,
                CONSTRAINT fk_i_prod FOREIGN KEY (id_produto) REFERENCES produtos(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;

            -- 4. TRANSACTIONAL & WORKFLOW
            CREATE TABLE locacoes (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                id_usuario INT UNSIGNED NOT NULL,
                id_item INT UNSIGNED NOT NULL,
                data_reserva DATE NOT NULL,
                data_devolucao DATE NOT NULL,
                total_pago DECIMAL(10,2),
                metodo_pagamento VARCHAR(50),
                status_workflow ENUM('Aguardando Pagamento', 'Preparando Envio', 'Em Transito', 'Com o Cliente', 'Retorno Galpao', 'Higienizando', 'Finalizado') DEFAULT 'Aguardando Pagamento',
                CONSTRAINT fk_l_user FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
                CONSTRAINT fk_l_item FOREIGN KEY (id_item) REFERENCES itens_estoque(id)
            ) ENGINE=InnoDB;

            -- 5. SYSTEM INTELLIGENCE & AUDIT
            CREATE TABLE logs_auditoria (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                id_usuario INT UNSIGNED,
                acao VARCHAR(100),
                tabela_afetada VARCHAR(50),
                dados_json JSON,
                ip_address VARCHAR(45),
                data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;

            CREATE TABLE configuracoes_sistema (
                chave_unica VARCHAR(100) PRIMARY KEY,
                valor TEXT,
                descricao VARCHAR(255),
                tipo_dado ENUM('string', 'int', 'json', 'boolean') DEFAULT 'string'
            ) ENGINE=InnoDB;

            SET FOREIGN_KEY_CHECKS = 1;
        ";
        
        $pdo->exec($setupSql);
        $pdo->exec("USE $dbName");

        // --- SEEDING: PERFIS ---
        $perfis = [
            ['Admin Global', '{"all": true}', 'Acesso total.'],
            ['Developer', '{"debug": true, "api": true}', 'Acesso técnico.'],
            ['Logistics Manager', '{"stock": true, "rfid": true}', 'Operação Galpão.'],
            ['Luxury Partner', '{"analytics": "brand"}', 'Dashboard de marcas.'],
            ['VIP Member', '{"priority": true, "rent": true}', 'Clientes fidelizados.'],
            ['Standard User', '{"rent": true}', 'Usuário comum.']
        ];
        $stmtNivel = $pdo->prepare("INSERT INTO niveis_acesso (nome, permissoes_json, descricao) VALUES (?, ?, ?)");
        foreach ($perfis as $p) $stmtNivel->execute($p);

        // --- SEEDING: USUÁRIOS & ENDEREÇOS ---
        $hash = password_hash('123456', PASSWORD_ARGON2ID);
        $usuarios = [
            ['Arthur Arqon', 'admin@arqon.com', 1],
            ['Augusto Luxury', 'augusto@vip.com', 5]
        ];
        $stmtUser = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, id_nivel_acesso) VALUES (?, ?, ?, ?)");
        foreach ($usuarios as $u) {
            $stmtUser->execute([$u[0], $u[1], $hash, $u[2]]);
            $userId = $pdo->lastInsertId();
            $pdo->exec("INSERT INTO usuarios_enderecos (id_usuario, logradouro, numero, cidade, padrao_entrega) VALUES ($userId, 'Av. Luxury Design', '1000', 'São Paulo', 1)");
        }

        // --- SEEDING: CATALOGO & ASSETS ---
        $pdo->exec("INSERT INTO marcas (nome, origem_pais) VALUES ('Versace', 'Italia'), ('Balmain', 'França'), ('Off-White', 'EUA')");
        $pdo->exec("INSERT INTO categorias (nome, tipo_grade, slug) VALUES ('Evening Dresses', 'Vestuario', 'evening-dresses'), ('Streetwear Premium', 'Vestuario', 'streetwear')");
        
        $pdo->exec("INSERT INTO produtos (id_marca, id_categoria, nome, valor_diaria, valor_reposicao) VALUES (1, 1, 'Medusa Gala Gown', 1200.00, 15000.00)");
        $prodId = $pdo->lastInsertId();
        
        $pdo->exec("INSERT INTO produto_midia (id_produto, tipo_midia, url_arquivo) VALUES ($prodId, 'imagem_4k', 'vsc_medusa_front.jpg'), ($prodId, 'modelo_3d', 'vsc_medusa.glb')");
        $pdo->exec("INSERT INTO itens_estoque (id_produto, tamanho, rfid_tag, status_fisico) VALUES ($prodId, 'M', 'ARQ-RFID-7788', 'Disponivel')");

        // --- SEEDING: CONFIGS ---
        $pdo->exec("INSERT INTO configuracoes_sistema (chave_unica, valor, tipo_dado) VALUES ('TAXA_HIGIENIZACAO', '85.00', 'int'), ('MANUTENCAO_PREVENTIVA', 'true', 'boolean')");

        header("Location: ?success=1"); exit;
    }

    // --- RENDERIZAÇÃO ---
    echo "<div class='container'>";
    echo "<div class='header'>
            <div>
                <h1>ARQON <span style='color:var(--gold)'>/</span> INFRASTRUCTURE</h1>
                <p style='color: var(--text-dim); margin-top: 5px;'>Enterprise Database Management & Object Inspector</p>
            </div>
            <a href='?op=reset' class='btn' onclick='return confirm(\"Deseja resetar toda a infraestrutura?\")'>Rebuild Environment</a>
          </div>";

    if (isset($_GET['success'])) echo "<div class='status-msg'><b>Sucesso:</b> O ecossistema Arqon foi reconstruído com suporte a Mídia 3D, Logs e RFID.</div>";

    $pdo->exec("USE {$config['db']}");
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    echo "<div class='grid-nav'>";
    foreach ($tables as $t) {
        $activeClass = (isset($_GET['view']) && $_GET['view'] === $t) ? 'active' : '';
        echo "<a href='?view=$t' class='nav-card $activeClass'>$t</a>";
    }
    echo "</div>";

    if (isset($_GET['view'])) {
        $tbl = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['view']);
        echo "<div class='data-section'>";
        
        echo "<h4 style='color: var(--gold);'>Schema Definition: $tbl</h4>";
        $columns = $pdo->query("DESCRIBE $tbl")->fetchAll();
        echo "<table><thead><tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr></thead><tbody>";
        foreach ($columns as $c) {
            echo "<tr><td><b>{$c['Field']}</b></td><td><code>{$c['Type']}</code></td><td><span class='badge'>{$c['Null']}</span></td><td><span style='color: var(--gold)'>{$c['Key']}</span></td><td>" . ($c['Default'] ?? 'NULL') . "</td></tr>";
        }
        echo "</tbody></table>";

        echo "<h4 style='color: var(--gold); margin-top: 40px;'>Active Records: $tbl</h4>";
        $rows = $pdo->query("SELECT * FROM $tbl LIMIT 50")->fetchAll();
        if ($rows) {
            echo "<table><thead><tr>";
            foreach (array_keys($rows[0]) as $h) echo "<th>$h</th>";
            echo "</tr></thead><tbody>";
            foreach ($rows as $r) {
                echo "<tr>";
                foreach ($r as $val) {
                    $display = (strlen((string)$val) > 50) ? substr((string)$val, 0, 47) . '...' : $val;
                    echo "<td>" . htmlspecialchars((string)$display) . "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p style='color: var(--text-dim)'>Nenhum registro encontrado nesta tabela.</p>";
        }
        echo "</div>";
    } else {
        // Dashboard Inicial de Credenciais
        echo "<div class='data-section'>
                <h3 style='color: var(--gold); margin:0 0 20px 0;'>System Access Control (Development Keys)</h3>
                <p style='font-size:14px;'>Ambiente configurado com <code>Argon2id</code>. Senha universal de teste: <code>123456</code></p>
                <table>
                    <thead><tr><th>Security Level</th><th>Master Identity</th><th>System Scope</th></tr></thead>
                    <tbody>
                        <tr><td>Admin Global</td><td>admin@arqon.com</td><td><span style='color:var(--success)'>[TOTAL_CONTROL]</span></td></tr>
                        <tr><td>VIP Member</td><td>augusto@vip.com</td><td><span style='color:var(--info)'>[PRIORITY_ACCESS]</span></td></tr>
                        <tr><td>Logistics</td><td>logistica@arqon.com</td><td><span style='color:var(--gold)'>[VAULT_MGMT]</span></td></tr>
                    </tbody>
                </table>
              </div>";
    }
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='color: #ff4444; background: #1a0000; padding: 40px; border: 1px solid #ff0000; margin: 40px; border-radius:4px;'>";
    echo "<h2 style='margin-top:0'>Infrastructure Failure</h2>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "</div>";
}