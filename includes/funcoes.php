<?php
// Garante que a sessão seja iniciada em todas as páginas que usarem funções
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Busca todos os funcionários ordenados por nome.
 * @param PDO $pdo Conexão PDO com o banco.
 * @return array Lista de funcionários ou array vazio.
 */
function getFuncionarios(PDO $pdo): array {
    try {
        $sql = "SELECT id, nome FROM funcionarios ORDER BY nome";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar funcionários: " . $e->getMessage());
        return []; // Retorna array vazio em caso de erro
    }
}

/**
 * Busca tarefas filtradas por status.
 * @param PDO $pdo Conexão PDO.
 * @param string $status Status desejado ('a_fazer', 'em_andamento', 'concluido').
 * @return array Lista de tarefas ou array vazio.
 */
function getTarefasPorStatus(PDO $pdo, string $status): array {
    try {
        $sql = "SELECT t.*, f.nome as funcionario_nome
                FROM tarefas t
                LEFT JOIN funcionarios f ON t.funcionario_id = f.id
                WHERE t.status = :status
                ORDER BY
                    CASE t.prioridade
                        WHEN 'alta' THEN 1
                        WHEN 'media' THEN 2
                        WHEN 'baixa' THEN 3
                        ELSE 4
                    END, t.data_criacao DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar tarefas por status ($status): " . $e->getMessage());
        return [];
    }
}

/**
 * Atualiza o status de uma tarefa e, opcionalmente, a data de conclusão.
 * @param PDO $pdo Conexão PDO.
 * @param int $id ID da tarefa.
 * @param string $novo_status Novo status.
 * @return bool True em sucesso, False em falha.
 */
function atualizarStatusTarefa(PDO $pdo, int $id, string $novo_status): bool {
    $data_conclusao = ($novo_status === 'concluido') ? date('Y-m-d H:i:s') : null;

    try {
        $sql = "UPDATE tarefas SET status = :status, data_conclusao = :data_conclusao WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':status', $novo_status, PDO::PARAM_STR);
        $stmt->bindParam(':data_conclusao', $data_conclusao); // PDO lida com NULL automaticamente
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erro ao atualizar status da tarefa ($id): " . $e->getMessage());
        $_SESSION['mensagem_erro'] = "Erro ao atualizar status da tarefa.";
        return false;
    }
}

/**
 * Define uma mensagem de sucesso na sessão.
 * @param string $mensagem Mensagem a ser exibida.
 */
function setMensagemSucesso(string $mensagem): void {
    $_SESSION['mensagem_sucesso'] = $mensagem;
}

/**
 * Define uma mensagem de erro na sessão.
 * @param string $mensagem Mensagem a ser exibida.
 */
function setMensagemErro(string $mensagem): void {
    $_SESSION['mensagem_erro'] = $mensagem;
}

/**
 * Redireciona o usuário para uma página específica.
 * @param string $pagina Nome da página (sem .php).
 */
function redirecionar(string $pagina): void {
    header("Location: index.php?pagina=" . $pagina);
    exit();
}
?>