<?php
// Processa a atualização de status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_status'])) {
    $tarefa_id = filter_input(INPUT_POST, 'tarefa_id', FILTER_VALIDATE_INT);
    $novo_status = filter_input(INPUT_POST, 'novo_status', FILTER_SANITIZE_SPECIAL_CHARS);
    $status_permitidos = ['a_fazer', 'em_andamento', 'concluido'];

    if ($tarefa_id && in_array($novo_status, $status_permitidos)) {
        if (atualizarStatusTarefa($pdo, $tarefa_id, $novo_status)) {
            setMensagemSucesso("Status da tarefa atualizado com sucesso!");
        } else {
            // Mensagem de erro já é definida dentro da função
        }
    } else {
        setMensagemErro("Dados inválidos para atualizar status.");
    }
    redirecionar('gerenciar_tarefas'); // Redireciona após processar
}

// Busca as tarefas para cada coluna
$tarefas_a_fazer = getTarefasPorStatus($pdo, 'a_fazer');
$tarefas_em_andamento = getTarefasPorStatus($pdo, 'em_andamento');
$tarefas_concluidas = getTarefasPorStatus($pdo, 'concluido');

// Função auxiliar para obter classe CSS da prioridade
function getPrioridadeBadgeClass(string $prioridade): string {
    switch ($prioridade) {
        case 'alta': return 'bg-danger';
        case 'media': return 'bg-warning text-dark';
        case 'baixa': return 'bg-success';
        default: return 'bg-secondary';
    }
}

?>
<h2 class="mb-4"><i class="fas fa-clipboard-list me-2"></i> Gerenciamento de Tarefas (Quadro Kanban)</h2>

<div class="row kanban-board">

    <div class="col-md-4">
        <div class="card kanban-column">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-list-ul me-2"></i> A Fazer
                <span class="badge bg-light text-dark float-end"><?php echo count($tarefas_a_fazer); ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($tarefas_a_fazer)): ?>
                    <p class="text-muted text-center">Nenhuma tarefa aqui.</p>
                <?php endif; ?>
                <?php foreach ($tarefas_a_fazer as $tarefa): ?>
                    <div class="card task-card mb-3 priority-<?php echo $tarefa['prioridade']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($tarefa['nome']); ?></h5>
                            <?php if (!empty($tarefa['descricao'])): ?>
                                <p class="card-text small"><?php echo nl2br(htmlspecialchars($tarefa['descricao'])); ?></p>
                            <?php endif; ?>
                             <p class="card-text mb-1">
                                <span class="badge <?php echo getPrioridadeBadgeClass($tarefa['prioridade']); ?>">
                                    <?php echo ucfirst($tarefa['prioridade']); ?>
                                </span>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($tarefa['funcionario_nome'] ?? 'Não atribuído'); ?><br>
                                    <i class="far fa-calendar-alt me-1"></i> Criado em: <?php echo date('d/m/Y H:i', strtotime($tarefa['data_criacao'])); ?>
                                </small>
                            </p>
                            <form method="post" action="index.php?pagina=gerenciar_tarefas" class="d-inline">
                                <input type="hidden" name="tarefa_id" value="<?php echo $tarefa['id']; ?>">
                                <input type="hidden" name="novo_status" value="em_andamento">
                                <button type="submit" name="atualizar_status" class="btn btn-sm btn-warning text-dark" title="Iniciar Tarefa">
                                   <i class="fas fa-play"></i> Iniciar
                                </button>
                            </form>
                             <form method="post" action="index.php?pagina=gerenciar_tarefas" class="d-inline">
                                <input type="hidden" name="tarefa_id" value="<?php echo $tarefa['id']; ?>">
                                <input type="hidden" name="novo_status" value="concluido">
                                <button type="submit" name="atualizar_status" class="btn btn-sm btn-success" title="Marcar como Concluída">
                                   <i class="fas fa-check"></i> Concluir Direto
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card kanban-column">
            <div class="card-header bg-warning text-dark">
                 <i class="fas fa-spinner fa-spin me-2"></i> Em Andamento
                 <span class="badge bg-light text-dark float-end"><?php echo count($tarefas_em_andamento); ?></span>
            </div>
            <div class="card-body">
                 <?php if (empty($tarefas_em_andamento)): ?>
                    <p class="text-muted text-center">Nenhuma tarefa aqui.</p>
                <?php endif; ?>
                <?php foreach ($tarefas_em_andamento as $tarefa): ?>
                     <div class="card task-card mb-3 priority-<?php echo $tarefa['prioridade']; ?>">
                        <div class="card-body">
                             <h5 class="card-title"><?php echo htmlspecialchars($tarefa['nome']); ?></h5>
                            <?php if (!empty($tarefa['descricao'])): ?>
                                <p class="card-text small"><?php echo nl2br(htmlspecialchars($tarefa['descricao'])); ?></p>
                            <?php endif; ?>
                             <p class="card-text mb-1">
                                <span class="badge <?php echo getPrioridadeBadgeClass($tarefa['prioridade']); ?>">
                                    <?php echo ucfirst($tarefa['prioridade']); ?>
                                </span>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($tarefa['funcionario_nome'] ?? 'Não atribuído'); ?><br>
                                    <i class="far fa-calendar-alt me-1"></i> Criado em: <?php echo date('d/m/Y H:i', strtotime($tarefa['data_criacao'])); ?>
                                </small>
                            </p>
                            <form method="post" action="index.php?pagina=gerenciar_tarefas" class="d-inline">
                                <input type="hidden" name="tarefa_id" value="<?php echo $tarefa['id']; ?>">
                                <input type="hidden" name="novo_status" value="a_fazer">
                                <button type="submit" name="atualizar_status" class="btn btn-sm btn-secondary" title="Mover para A Fazer">
                                   <i class="fas fa-arrow-left"></i> Voltar
                                </button>
                            </form>
                            <form method="post" action="index.php?pagina=gerenciar_tarefas" class="d-inline">
                                <input type="hidden" name="tarefa_id" value="<?php echo $tarefa['id']; ?>">
                                <input type="hidden" name="novo_status" value="concluido">
                                <button type="submit" name="atualizar_status" class="btn btn-sm btn-success" title="Concluir Tarefa">
                                    <i class="fas fa-check"></i> Concluir
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card kanban-column">
            <div class="card-header bg-success text-white">
                 <i class="fas fa-check-circle me-2"></i> Concluído
                 <span class="badge bg-light text-dark float-end"><?php echo count($tarefas_concluidas); ?></span>
            </div>
            <div class="card-body">
                 <?php if (empty($tarefas_concluidas)): ?>
                    <p class="text-muted text-center">Nenhuma tarefa aqui.</p>
                <?php endif; ?>
                <?php foreach ($tarefas_concluidas as $tarefa): ?>
                     <div class="card task-card mb-3 bg-light"> <div class="card-body">
                             <h5 class="card-title text-decoration-line-through"><?php echo htmlspecialchars($tarefa['nome']); ?></h5>
                            <?php if (!empty($tarefa['descricao'])): ?>
                                <p class="card-text small text-decoration-line-through"><?php echo nl2br(htmlspecialchars($tarefa['descricao'])); ?></p>
                            <?php endif; ?>
                            <p class="card-text mb-1">
                                <span class="badge <?php echo getPrioridadeBadgeClass($tarefa['prioridade']); ?>">
                                    <?php echo ucfirst($tarefa['prioridade']); ?>
                                </span>
                            </p>
                             <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($tarefa['funcionario_nome'] ?? 'Não atribuído'); ?><br>
                                    <?php if ($tarefa['data_conclusao']): ?>
                                        <i class="far fa-calendar-check me-1"></i> Concluído em: <?php echo date('d/m/Y H:i', strtotime($tarefa['data_conclusao'])); ?>
                                    <?php else: ?>
                                         <i class="far fa-calendar-alt me-1"></i> Criado em: <?php echo date('d/m/Y H:i', strtotime($tarefa['data_criacao'])); ?>
                                    <?php endif; ?>
                                </small>
                            </p>
                             <form method="post" action="index.php?pagina=gerenciar_tarefas" class="d-inline">
                                <input type="hidden" name="tarefa_id" value="<?php echo $tarefa['id']; ?>">
                                <input type="hidden" name="novo_status" value="em_andamento">
                                <button type="submit" name="atualizar_status" class="btn btn-sm btn-warning text-dark" title="Reabrir Tarefa">
                                   <i class="fas fa-undo"></i> Reabrir
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>