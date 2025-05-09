<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

$erros = [];
$sucesso = false;

// Verifica se o usuário já está logado
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: home.php"); // Redireciona para a página do painel
    exit;
}

// Processamento do formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // Validações básicas
    if (empty($login) || empty($senha)) {
        $erros[] = "Todos os campos são obrigatórios.";
    }

    // Se não houver erros, verifica no banco
    if (empty($erros)) {
        try {
            // Busca usuário por nome de usuário ou email
            $stmt = $pdo->prepare("SELECT id, nome_usuario, email, nome_completo, senha_hash FROM usuarios WHERE nome_usuario = :login OR email = :login");
            $stmt->bindParam(':login', $login);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica a senha
            if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
                // Login bem-sucedido
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_name'] = $usuario['nome_usuario'];
                $_SESSION['user_email'] = $usuario['email'];
                $_SESSION['nome_completo'] = $usuario['nome_completo'];
                $_SESSION['logged_in'] = true;
                
                $sucesso = true;
                
                // Redireciona após login bem-sucedido
                header("Location: home.php");
                exit;
            } else {
                $erros[] = "Usuário ou senha incorretos.";
            }
        } catch (PDOException $e) {
            $erros[] = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2 class="text-center mb-4">Login</h2>
            
            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($erros as $erro): ?>
                            <li><?= htmlspecialchars($erro) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['registro']) && $_GET['registro'] === 'sucesso'): ?>
                <div class="alert alert-success">
                    Cadastro realizado com sucesso! Faça login para continuar.
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="login" class="form-label">Usuário ou Email</label>
                    <input type="text" class="form-control" id="login" name="login" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
            
            <div class="mt-3 text-center">
                <p>Não tem uma conta? <a href="index.php?pagina=registro.php">Registre-se</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>