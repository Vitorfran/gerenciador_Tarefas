<?php
$host = 'localhost';
$dbname = 'gerenciador_tarefas';
$username = 'root';
$password = '';     

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>

<!-- 

PDO	                        ----- Cria a conexão com o banco de dados usando PHP e MySQL
setAttribute()	            ----- Define comportamentos do PDO (erros, retorno de dados, etc)
try { ... } catch { ... }	----- Tenta algo e captura erros para evitar que o site "quebre"
die()	                    ----- Encerra o script e mostra uma mensagem 

-->