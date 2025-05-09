<?php
// Busca a lista de funcionários para o dropdown
$funcionarios = getFuncionarios($pdo);

// Lógica de processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_tarefa'])) {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
    $prioridade = filter_input(INPUT_POST, 'prioridade', FILTER_SANITIZE_SPECIAL_CHARS);
    // funcionario_id pode ser vazio (int ou null)
    $funcionario_id = filter_input(INPUT_POST, 'funcionario_id', FILTER_VALIDATE_INT);
    // Se o filtro retornar false (não for int válido), define como null
     if ($funcionario_id === false || $funcionario_id === 0) {
         $funcionario_id = null;
     }

    $erros = [];
    if (empty($nome)) $erros[] = "Nome da tarefa é obrigatório.";
    if (!in_array($prioridade, ['baixa', 'media', 'alta'])) $erros[] = "Prioridade inválida.";

    if (empty($erros)) {
        try {
            $sql = "INSERT INTO tarefas (nome, descricao, prioridade, funcionario_id, status)
                    VALUES (:nome, :descricao, :prioridade, :funcionario_id, 'a_fazer')"; // Status inicial
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':prioridade', $prioridade);
            // PDO trata corretamente o bindParam com NULL
            $stmt->bindParam(':funcionario_id', $funcionario_id, $funcionario_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->execute();

            setMensagemSucesso("Tarefa cadastrada com sucesso!");
            redirecionar('cadastro_tarefa');

        } catch (PDOException $e) {
            setMensagemErro("Erro ao cadastrar tarefa: " . $e->getMessage());
            error_log("Erro DB cad tarefa: " . $e->getMessage());
            redirecionar('cadastro_tarefa');
        }
    } else {
        setMensagemErro(implode("<br>", $erros));
        redirecionar('cadastro_tarefa');
    }
}

?>
<h2 class="mb-4"><i class="fas fa-plus-circle me-2"></i> Cadastro de Tarefa</h2>

<form method="post" action="index.php?pagina=cadastro_tarefa">
    <div class="mb-3">
        <label for="nome" class="form-label">Nome da Tarefa</label>
        <input type="text" class="form-control" id="nome" name="nome" required>
    </div>
    <div class="mb-3">
        <label for="descricao" class="form-label">Descrição (Opcional)</label>
        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="prioridade" class="form-label">Prioridade</label>
            <select class="form-select" id="prioridade" name="prioridade" required>
                <option value="baixa">Baixa</option>
                <option value="media" selected>Média</option>
                <option value="alta">Alta</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="funcionario_id" class="form-label">Atribuir ao Funcionário (Opcional)</label>
            <select class="form-select" id="funcionario_id" name="funcionario_id">
                <option value="">-- Não atribuído --</option>
                <?php foreach ($funcionarios as $funcionario): ?>
                    <option value="<?php echo htmlspecialchars($funcionario['id']); ?>">
                        <?php echo htmlspecialchars($funcionario['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <button type="submit" name="cadastrar_tarefa" class="btn btn-primary">
        <i class="fas fa-save me-1"></i> Cadastrar Tarefa
    </button>
</form>