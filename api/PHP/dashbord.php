<?php
require_once '../php/admin_auth.php';
checkAuth();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARQON | Gerenciar Produtos</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-layout">
    
    <aside class="sidebar">
        <div class="sidebar-header"><h2 class="logo-gold">ARQON</h2><span class="badge-role">ADMIN</span></div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-link"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="produtos.php" class="nav-link active"><i class="fa-solid fa-box-archive"></i> Produtos do Cofre</a>
        </nav>
        <div class="sidebar-footer"><a href="../php/admin_auth.php?action=logout" class="nav-link text-danger"><i class="fa-solid fa-power-off"></i> Sair</a></div>
    </aside>

    <main class="main-content">
        <header class="topbar glass-panel">
            <div class="page-title-header">
                <h2>Gerenciamento de Produtos</h2>
            </div>
            <button class="btn-gold-solid" onclick="openProductModal()"><i class="fa-solid fa-plus"></i> NOVO PRODUTO</button>
        </header>

        <section class="content-area">
            <div class="table-container glass-panel">
                <table class="arqon-table" id="productsTable">
                    <thead>
                        <tr>
                            <th>IMG</th>
                            <th>NOME DO PRODUTO</th>
                            <th>CATEGORIA</th>
                            <th>DIÁRIA (R$)</th>
                            <th>STATUS</th>
                            <th>FLAGS</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody id="productsList">
                        </tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="productModal" class="modal-overlay">
        <div class="modal-content glass-panel">
            <div class="modal-header">
                <h3 id="modalTitle" class="text-gold">Adicionar ao Cofre</h3>
                <button class="close-modal" onclick="closeProductModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <input type="hidden" id="produto_id" name="produto_id">
                <input type="hidden" name="action" value="save_product">
                
                <div class="form-grid">
                    <div class="form-group span-2">
                        <label>Nome da Peça</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Categoria</label>
                        <select id="categoria" name="categoria" required>
                            <option value="Streetwear">Streetwear</option>
                            <option value="Gala Exclusivo">Gala Exclusivo</option>
                            <option value="Cyber Luxury">Cyber Luxury</option>
                            <option value="Acessórios">Acessórios</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Valor Diária (R$)</label>
                        <input type="number" step="0.01" id="valor_diaria" name="valor_diaria" required>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select id="status_disponibilidade" name="status_disponibilidade">
                            <option value="disponivel">Disponível</option>
                            <option value="alugado">Alugado</option>
                            <option value="manutencao">Manutenção/Ozônio</option>
                        </select>
                    </div>

                    <div class="form-group checkbox-group flex-row">
                        <label><input type="checkbox" id="destaque" name="destaque" value="1"> Destaque Home</label>
                        <label><input type="checkbox" id="novo" name="novo" value="1" checked> New Drop</label>
                        <label><input type="checkbox" id="mais_alugado" name="mais_alugado" value="1"> Hot</label>
                    </div>

                    <div class="form-group span-2">
                        <label>Imagem Principal</label>
                        <input type="file" id="imagem_principal" name="imagem_principal" accept="image/png, image/jpeg, image/webp" onchange="previewImage(this)">
                        <div class="image-preview-box" id="imgPreview">
                            <span class="preview-text">Preview da Imagem</span>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-outline" onclick="closeProductModal()">CANCELAR</button>
                    <button type="submit" class="btn-gold-solid">SALVAR PEÇA <i class="fa-solid fa-check"></i></button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/admin.js"></script>
    <script>
        // Inicializa a listagem ao carregar a página
        document.addEventListener('DOMContentLoaded', loadProducts);
    </script>
</body>
</html>