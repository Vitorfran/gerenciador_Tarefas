<?php
require_once __DIR__ . '/../includes/conexao.php';

$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_usuario = trim($_POST['nome_usuario'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nome_completo = trim($_POST['nome_completo'] ?? '');
    $senha = $_POST['senha'] ?? ''; // Corrigido para match com o HTML
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    // Validações
    if (empty($nome_usuario) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $erros[] = "Todos os campos obrigatórios devem ser preenchidos.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Formato de e-mail inválido.";
    }

    if ($senha !== $confirmar_senha) {
        $erros[] = "As senhas não coincidem.";
    }

    if (strlen($senha) < 6) {
        $erros[] = "A senha deve ter pelo menos 6 caracteres.";
    }

    if (empty($erros)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        try {
          $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, data_cadastro) VALUES (?, ?, ?, NOW())");
          $stmt->execute([$nome_usuario, $email, $senha_hash]);
            $sucesso = true;
            
            // Redirecionar após sucesso
            header("Location: login.php?sucesso=1");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erros[] = "Usuário ou email já cadastrados.";
            } else {
                $erros[] = "Erro no banco de dados: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
      <div class="card-body">
        <h4 class="card-title text-center mb-4">Cadastro de Usuário</h4>

        <?php if ($sucesso): ?>
          <div class="alert alert-success">Cadastro realizado com sucesso!</div>
        <?php elseif (!empty($erros)): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Nome de Usuário *</label>
            <input type="text" name="nome_usuario" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nome Completo</label>
            <input type="text" name="nome_completo" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Senha *</label>
            <input type="password" name="senha" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirmar Senha *</label>
            <input type="password" name="confirmar_senha" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
        </form>
        <div class="mt-3 text-center">
                <p>Ja tem uma Conta?? <a href="index.php?pagina=login.php">Logue</a></p>
            </div>
      </div>
    </div>
  </div>
</body>
</html>
