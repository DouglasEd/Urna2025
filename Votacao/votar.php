<?php
// Configurações do banco de dados
$host = "localhost";
$usuario = "root";
$senha = "#Decb@2025";
$banco = "urna";

// Conecta ao banco de dados
$conexao = new mysqli($host, $usuario, $senha, $banco);

// Verifica se houve erro na conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// Recebe os dados via GET
$matricula = isset($_GET['matricula']) ? $_GET['matricula'] : null;
$nomeChapa = isset($_GET['chapa']) ? $_GET['chapa'] : null;

if ($matricula && $nomeChapa) {
    // Atualiza a tabela Chapas para adicionar 1 no número de votos
    $query_update_votos = "UPDATE chapas SET Votos = Votos + 1 WHERE Nome_Chapa = ?";
    $stmt_update_votos = $conexao->prepare($query_update_votos);
    $stmt_update_votos->bind_param("s", $nomeChapa);
    $stmt_update_votos->execute();
    $stmt_update_votos->close();

    // Atualiza a tabela discentes para marcar que o aluno votou
    $query_update_votou = "UPDATE discentes SET Votou = 1 WHERE Matricula = ?";
    $stmt_update_votou = $conexao->prepare($query_update_votou);
    $stmt_update_votou->bind_param("s", $matricula); // Ou "i" se for um número
    $stmt_update_votou->execute();
    $stmt_update_votou->close();
    
    exit();
} else {
    echo "Erro ao processar o voto.";
}

$conexao->close();
?>
