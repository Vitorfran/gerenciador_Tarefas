<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/header.php';

// --- Roteamento Básico ---
// Verifica se a página foi passada via GET, caso contrário, define 'home' como padrão
// Isso evita que o usuário tenha que digitar a URL completa para acessar a página inicial
$pagina = $_GET['pagina'] ?? 'login';

// Define as páginas permitidas
// Isso ajuda a evitar que usuários acessem arquivos indesejados ou não permitidos
$paginasPermitidas = [
    'home',
    'cadastro_funcionario',
    'cadastro_tarefa',
    'gerenciar_tarefas',
    'login',
    'registro'
];

$caminhoPagina = __DIR__ . "/pages/{$pagina}.php";

if (in_array($pagina, $paginasPermitidas) && file_exists($caminhoPagina)) {
    require_once $caminhoPagina;
} else {
    echo "<div class='alert alert-danger'>Página não encontrada!</div>";
}

require_once __DIR__ . '/includes/footer.php';
?>