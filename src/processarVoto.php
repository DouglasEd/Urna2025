<?php
require_once __DIR__ . '/conectarBD.php';

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

    $primeiros4 = substr($matricula, 0, 4); // Primeiros 4 dígitos
    $ultimos3 = substr($matricula, -3); // Últimos 3 dígitos
    $matricula_mascarada = $primeiros4 . '*****' . $ultimos3; // Formata a matrícula
    $hora_voto = date("H:i:s"); // Obtém o horário atual
    
    // Formata a entrada do log
    $log_entry = "$hora_voto $matricula_mascarada $nomeChapa" . PHP_EOL;
    
    // Escreve no arquivo de log (cria se não existir)
    file_put_contents('votos.log', $log_entry, FILE_APPEND | LOCK_EX);
    
    exit();
} else {
    echo "Erro ao processar o voto.";
}

$conexao->close();
?>
