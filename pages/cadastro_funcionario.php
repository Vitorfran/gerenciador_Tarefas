<?php
// Lógica de processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_funcionario'])) {
    // Sanitizar e validar entradas (básico)
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $departamento = filter_input(INPUT_POST, 'departamento', FILTER_SANITIZE_SPECIAL_CHARS);

    $erros = [];
    if (empty($nome)) $erros[] = "Nome é obrigatório.";
    if (!$email) $erros[] = "Email inválido ou vazio.";
    if (empty($departamento)) $erros[] = "Departamento é obrigatório.";

    if (empty($erros)) {
        try {
            $sql = "INSERT INTO funcionarios (nome, email, departamento) VALUES (:nome, :email, :departamento)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':departamento', $departamento);
            $stmt->execute();

            setMensagemSucesso("Funcionário cadastrado com sucesso!");
            redirecionar('cadastro_funcionario'); // Redireciona para evitar reenvio

        } catch (PDOException $e) {
            // Verifica erro de email duplicado (código 1062 no MySQL)
            if ($e->getCode() == '23000' || $e->errorInfo[1] == 1062) {
                setMensagemErro("Erro: Este email já está cadastrado.");
            } else {
                setMensagemErro("Erro ao cadastrar funcionário: " . $e->getMessage());
                error_log("Erro DB cad funcionário: " . $e->getMessage()); // Log detalhado
            }
            redirecionar('cadastro_funcionario');
        }
    } else {
        // Se houver erros de validação, exibe-os
        setMensagemErro(implode("<br>", $erros));
        redirecionar('cadastro_funcionario');
    }
}
?>

<h2 class="mb-4"><i class="fas fa-user-plus me-2"></i> Cadastro de Funcionário</h2>

<form method="post" action="index.php?pagina=cadastro_funcionario">
    <div class="mb-3">
        <label for="nome" class="form-label">Nome Completo</label>
        <input type="text" class="form-control" id="nome" name="nome" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required placeholder="exemplo@dominio.com">
    </div>
    <div class="mb-3">
        <label for="departamento" class="form-label">Departamento</label>
        <input type="text" class="form-control" id="departamento" name="departamento" required>
    </div>
    <button type="submit" name="cadastrar_funcionario" class="btn btn-primary">
       <i class="fas fa-save me-1"></i> Cadastrar
    </button>
</form>