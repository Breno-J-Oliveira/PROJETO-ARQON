<?php
session_start();

// Se não houver ID na sessão, redireciona para o login
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.html");
    exit;
}

// Função utilitária para barrar perfis não autorizados em páginas específicas
function requireProfile($allowedProfiles) {
    if (!in_array($_SESSION['user_perfil'], $allowedProfiles)) {
        header("Location: /acesso-negado.html");
        exit;
    }
}
?>