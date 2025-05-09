<?php
// Define a URL base para facilitar a criação de links
// Detecta se está usando HTTPS
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
// Pega o nome do host
$host = $_SERVER['HTTP_HOST'];
// Pega o diretório onde o script está rodando (remove o nome do script se houver)
$script_dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
// Monta a URL base - Certifique-se que o diretório está correto
$baseUrl = $protocol . $host . $script_dir;

$paginaAtual = $_GET['pagina'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gerenciamento de Tarefas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php?pagina=home">
                <i class="fas fa-tasks me-2"></i> Gerenciador de Tarefas
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($paginaAtual === 'home') ? 'active' : ''; ?>" href="index.php?pagina=home">
                           <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($paginaAtual === 'cadastro_funcionario') ? 'active' : ''; ?>" href="index.php?pagina=cadastro_funcionario">
                            <i class="fas fa-user-plus me-1"></i> Cadastrar Funcionário
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($paginaAtual === 'cadastro_tarefa') ? 'active' : ''; ?>" href="index.php?pagina=cadastro_tarefa">
                           <i class="fas fa-plus-circle me-1"></i> Cadastrar Tarefa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($paginaAtual === 'gerenciar_tarefas') ? 'active' : ''; ?>" href="index.php?pagina=gerenciar_tarefas">
                            <i class="fas fa-clipboard-list me-1"></i> Gerenciar Tarefas
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php
        // Exibe mensagens de feedback (se houver)
        if (isset($_SESSION['mensagem_sucesso'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['mensagem_sucesso'] . '
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['mensagem_sucesso']); // Limpa a mensagem após exibir
        }
        if (isset($_SESSION['mensagem_erro'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['mensagem_erro'] . '
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['mensagem_erro']); // Limpa a mensagem após exibir
        }
        ?>