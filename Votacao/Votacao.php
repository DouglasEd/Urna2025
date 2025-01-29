<?php
// Configurações do banco de dados
require_once '../credentials.php';
// Conecta ao banco de dados
$conexao = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

// Verifica se houve erro na conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// Recebe a matrícula passada pela URL
$matricula = isset($_GET['matricula']) ? $_GET['matricula'] : null;

if ($matricula) {
    // Atualiza a tabela discentes para indicar que o aluno votou
    $query_update_votou = "UPDATE discentes SET Votou = 1 WHERE Matricula = ?";
    $stmt_update_votou = $conexao->prepare($query_update_votou);
    $stmt_update_votou->bind_param("s", $matricula);
    $stmt_update_votou->execute();
    $stmt_update_votou->close();
}

// Consulta todas as chapas
$query = "SELECT * FROM Chapas";
$result = $conexao->query($query);

// Verifica se há chapas
$chapas = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $chapas[] = $row;
    }
} else {
    echo "Nenhuma chapa encontrada.";
}

$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapinhas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="main">
        <div class="container">
            <?php foreach ($chapas as $index => $chapa): ?>
                <!-- Cada quadrado representando uma chapa -->
                <div class="chapa" onclick="votarChapa('<?php echo htmlspecialchars($chapa['Nome_Chapa']); ?>', '<?php echo htmlspecialchars($matricula); ?>')">
                    <h3><?php echo htmlspecialchars($chapa['Nome_Chapa']); ?></h3>
                    
                    <!-- Exibe os integrantes da chapa com cargo -->
                    <div class="integrantes">
                        <?php
                        // Conecta novamente para pegar os integrantes
                        $conexao = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
                        if ($conexao->connect_error) {
                            die("Erro na conexão: " . $conexao->connect_error);
                        }

                        // Busca os integrantes dessa chapa com prioridade para Presidente e Vice-Presidente
                        $nomeChapa = $chapa['Nome_Chapa'];
                        $query_integrantes = "SELECT Nome, Cargo FROM Integrantes WHERE Chapa = '$nomeChapa' ORDER BY FIELD(Cargo, 'Presidente') DESC, FIELD(Cargo, 'Vice-Presidente') DESC, Cargo";
                        $result_integrantes = $conexao->query($query_integrantes);
                        
                        // Exibe os integrantes da chapa
                        if ($result_integrantes->num_rows > 0) {
                            while ($integrante = $result_integrantes->fetch_assoc()) {
                                echo "<p><strong>" . htmlspecialchars($integrante['Cargo']) . ":</strong> " . htmlspecialchars($integrante['Nome']) . "</p>";
                            }
                        } else {
                            echo "<p>Sem integrantes.</p>";
                        }

                        $conexao->close();
                        ?>
                    </div>
                </div>

                <!-- Verifica se deve iniciar uma nova linha após 2 chapas -->
                <?php if (($index + 1) % 2 == 0): ?>
                    <div class="clear"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <div id='mensagem'> VOTO EFETUADO</div>
    <script src="scriptVote.js"></script>
</body>
</html>
