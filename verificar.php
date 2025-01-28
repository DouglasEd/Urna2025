<?php
// Configurações do banco de dados
include('credentials.php')

// Conecta ao banco de dados
$conexao = new mysqli($host, $usuario, $senha, $banco);

// Verifica se houve erro na conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// Recebe a matrícula enviada via POST
$matricula = $_POST['matricula'];

// Prepara a consulta SQL usando prepared statement para evitar SQL injection
$stmt = $conexao->prepare("SELECT Votou, Nome FROM discentes WHERE Matricula = ?");
$stmt->bind_param("s", $matricula);
$stmt->execute();
$resultado = $stmt->get_result();

// Verifica se encontrou a matrícula
if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    if ($row['votou'] == 1) {
        echo "ja_votou";
    } else {
        // Retorna um JSON com o nome e status
        echo json_encode(["status" => "nao_votou", "nome" => $row['Nome']]);
    }
} else {
    echo "nao_encontrado";
}

// Fecha a conexão
$stmt->close();
$conexao->close();
?>
