-- =========================================================
-- BANCO DE DADOS ARQON - VERSÃO PROFISSIONAL 2.0 (MODULAR)
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS arqon;
CREATE DATABASE arqon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE arqon;

-- ---------------------------------------------------------
-- MÓDULO 1: IAM (IDENTIDADE E ACESSOS)
-- ---------------------------------------------------------
CREATE TABLE niveis_acesso (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    permissoes_json JSON NOT NULL -- Armazena as flags de permissão
) ENGINE=InnoDB;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL DEFAULT (UUID()) UNIQUE,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) UNIQUE,
    id_nivel_acesso INT UNSIGNED NOT NULL,
    status ENUM('ativo', 'inativo', 'bloqueado') DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_nivel FOREIGN KEY (id_nivel_acesso) REFERENCES niveis_acesso(id)
) ENGINE=InnoDB;

CREATE TABLE usuarios_enderecos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    titulo VARCHAR(50) DEFAULT 'Casa', -- Casa, Trabalho, Hotel
    cep VARCHAR(9) NOT NULL,
    logradouro VARCHAR(255) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    padrao_entrega BOOLEAN DEFAULT FALSE,
    CONSTRAINT fk_end_user FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE api_keys (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chave_hash VARCHAR(255) NOT NULL UNIQUE,
    descricao VARCHAR(100), -- Ex: "Leitor RFID Galpão 01"
    status_ativo BOOLEAN DEFAULT TRUE,
    data_expiracao DATETIME,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- MÓDULO 2: ATIVOS DE LUXO (CATÁLOGOS E SKUS)
-- ---------------------------------------------------------
CREATE TABLE marcas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT,
    logo_url VARCHAR(255),
    comissao_percentual DECIMAL(5,2) DEFAULT 25.00
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(60) NOT NULL UNIQUE,
    macro_categoria ENUM('Parte Superior', 'Parte Inferior', 'Corpo Inteiro', 'Acessórios', 'Calçados')
) ENGINE=InnoDB;

CREATE TABLE estilos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE -- Streetwear, Gala, Casual Chic
) ENGINE=InnoDB;

CREATE TABLE cores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(30) NOT NULL,
    hex_code CHAR(7) NOT NULL -- #000000
) ENGINE=InnoDB;

CREATE TABLE produtos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_marca INT UNSIGNED NOT NULL,
    id_categoria INT UNSIGNED NOT NULL,
    id_estilo INT UNSIGNED NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    composicao VARCHAR(255),
    valor_mercado DECIMAL(10,2) NOT NULL,
    valor_diaria DECIMAL(10,2) NOT NULL,
    status_venda BOOLEAN DEFAULT TRUE,
    CONSTRAINT fk_prod_marca FOREIGN KEY (id_marca) REFERENCES marcas(id),
    CONSTRAINT fk_prod_cat FOREIGN KEY (id_categoria) REFERENCES categorias(id),
    CONSTRAINT fk_prod_estilo FOREIGN KEY (id_estilo) REFERENCES estilos(id)
) ENGINE=InnoDB;

CREATE TABLE itens_estoque (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- SKU Real
    id_produto INT UNSIGNED NOT NULL,
    id_cor INT UNSIGNED NOT NULL,
    tamanho VARCHAR(10) NOT NULL,
    rfid_nfc_tag VARCHAR(100) UNIQUE NOT NULL,
    status_atual ENUM('Disponível', 'No Vault', 'Alugado', 'Transporte', 'Manutenção', 'Higienização') DEFAULT 'Disponível',
    condicao_fisica VARCHAR(100) DEFAULT 'Perfeita',
    qtd_locacoes INT DEFAULT 0,
    CONSTRAINT fk_item_prod FOREIGN KEY (id_produto) REFERENCES produtos(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_cor FOREIGN KEY (id_cor) REFERENCES cores(id)
) ENGINE=InnoDB;

CREATE TABLE produto_midia (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_produto INT UNSIGNED NOT NULL,
    tipo_midia ENUM('imagem', 'video', '3d_model') DEFAULT 'imagem',
    url_arquivo VARCHAR(255) NOT NULL,
    ordem INT DEFAULT 0,
    CONSTRAINT fk_midia_prod FOREIGN KEY (id_produto) REFERENCES produtos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- MÓDULO 3: LOGÍSTICA E ALUGUÉIS
-- ---------------------------------------------------------
CREATE TABLE locacoes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    id_item_estoque INT UNSIGNED NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    valor_aluguel DECIMAL(10,2) NOT NULL,
    valor_caucao DECIMAL(10,2) NOT NULL,
    status_pedido ENUM('pendente', 'pago', 'enviado', 'entregue', 'devolvido', 'concluido', 'cancelado') DEFAULT 'pendente',
    CONSTRAINT fk_loc_user FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    CONSTRAINT fk_loc_item FOREIGN KEY (id_item_estoque) REFERENCES itens_estoque(id)
) ENGINE=InnoDB;

CREATE TABLE entregas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_locacao INT UNSIGNED NOT NULL,
    codigo_rastreio VARCHAR(100),
    status_gps VARCHAR(100),
    data_postagem DATETIME,
    CONSTRAINT fk_ent_loc FOREIGN KEY (id_locacao) REFERENCES locacoes(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- MÓDULO 4: FINANCEIRO (SPLIT E CAUÇÃO)
-- ---------------------------------------------------------
CREATE TABLE transacoes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_locacao INT UNSIGNED NOT NULL,
    gateway_id VARCHAR(100), -- ID da Stripe/Pagarme
    valor_total DECIMAL(10,2) NOT NULL,
    metodo ENUM('cartao', 'pix'),
    status ENUM('sucesso', 'falha', 'estornado'),
    data_transacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_trans_loc FOREIGN KEY (id_locacao) REFERENCES locacoes(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- MÓDULO 5: INTELIGÊNCIA E MARKETING
-- ---------------------------------------------------------
CREATE TABLE wishlist (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    id_produto INT UNSIGNED NOT NULL,
    CONSTRAINT fk_wish_user FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    CONSTRAINT fk_wish_prod FOREIGN KEY (id_produto) REFERENCES produtos(id)
) ENGINE=InnoDB;

CREATE TABLE lookbooks_ia (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    produtos_json JSON, -- IDs sugeridos pela IA
    estilo_referencia VARCHAR(50),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ia_user FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- MÓDULO 6: AUDITORIA E SAÚDE
-- ---------------------------------------------------------
CREATE TABLE sistema_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED,
    acao VARCHAR(255),
    tabela VARCHAR(50),
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- MÓDULO 7: CONFIGURAÇÕES E WEBHOOKS
-- ---------------------------------------------------------
CREATE TABLE configuracoes_sistema (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descricao TEXT
) ENGINE=InnoDB;

CREATE TABLE logs_webhooks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    servico_origem VARCHAR(50), -- Stripe, Correios, RFID
    payload_recebido JSON,
    status_processamento VARCHAR(20),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;