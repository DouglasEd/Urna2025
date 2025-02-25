<?php
require_once __DIR__ . '/../src/conectarBD.php';

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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans bg-gray-100 m-0 p-0">

    <div id="main" class="p-5">
        <div class="flex flex-wrap gap-[2vh] gap-[4vw] justify-center items-start">

            <?php foreach ($chapas as $index => $chapa): ?>
                <!-- Cada quadrado representando uma chapa -->
                <div class="w-2/5 bg-white border-2 border-gray-300 p-5 rounded-lg shadow-md text-center cursor-pointer" onclick="votarChapa('<?php echo htmlspecialchars($chapa['Nome_Chapa']); ?>', '<?php echo htmlspecialchars($matricula); ?>')">
                    <h3 class="text-lg mb-2"><?php echo htmlspecialchars($chapa['Nome_Chapa']); ?></h3>
                    
                    <!-- Exibe os integrantes da chapa com cargo -->
                    <div class="integrantes">
                        <?php
                        // Conecta novamente para pegar os integrantes
                        $conexao = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
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
                    <div class="w-full"></div>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>
    </div>

    <div id="mensagem" class="absolute top-1 left-1 w-full h-full flex items-center justify-center bg-[#360034a2] text-white text-[10vw] font-impact">
        VOTO EFETUADO
    </div>

    <script src="../src/scriptVote.js"></script>
</body>
</html>
