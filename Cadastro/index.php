<?php
require_once "../credentials.php";

$ipv4 = $_SERVER['SERVER_ADDR'] ?? 'Não foi possível obter o IPv4';

// Conecta ao banco de dados
$conexao = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Verifica a conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta e sanitiza os dados
    $nomeChapa = $conexao->real_escape_string($_POST['nome_chapa']);
    
    try {
        // Inicia transação
        $conexao->begin_transaction();

        // Insere na tabela chapas
        $stmtChapa = $conexao->prepare("INSERT INTO chapas (Nome_Chapa) VALUES (?)");
        $stmtChapa->bind_param("s", $nomeChapa);
        
        if (!$stmtChapa->execute()) {
            throw new Exception("Erro ao inserir chapa: " . $stmtChapa->error);
        }
        $stmtChapa->close();

        // Insere na tabela integrantes
        if (isset($_POST['nome']) && isset($_POST['cargo'])) {
            $stmtIntegrante = $conexao->prepare("INSERT INTO integrantes (Nome, Chapa, Cargo) VALUES (?, ?, ?)");
            
            foreach ($_POST['nome'] as $index => $nome) {
                $nomeMembro = $conexao->real_escape_string($nome);
                $cargo = $conexao->real_escape_string($_POST['cargo'][$index]);
                
                $stmtIntegrante->bind_param("sss", $nomeMembro, $nomeChapa, $cargo);
                
                if (!$stmtIntegrante->execute()) {
                    throw new Exception("Erro ao inserir integrante: " . $stmtIntegrante->error);
                }
            }
            $stmtIntegrante->close();
        }

        // Confirma a transação
        $conexao->commit();
        echo "Chapa e integrantes cadastrados com sucesso!";
        
    } catch (Exception $e) {
        // Reverte em caso de erro
        $conexao->rollback();
        echo "Erro: " . $e->getMessage();
    }
    
    $conexao->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Chapa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="ip-info">
        IPv4 do Servidor: <?php echo htmlspecialchars($ipv4); ?>
    </div>
    <div class="container">
        <h1>Cadastro de Chapa</h1>
        <form method="POST">
            <div>
                <label>Nome da Chapa:</label>
                <input type="text" name="nome_chapa" required style="width: 300px; padding: 8px;">
            </div>

            <h3>Membros da Chapa</h3>
            <div id="membros-container">
                <!-- Blocos de membros serão adicionados aqui -->
                <div class="membro-block">
                    <input type="text" name="nome[]" placeholder="Nome" required>
                    <input type="text" name="cargo[]" placeholder="Cargo" value="Presidente" required>
                    <button type="button" class="btn btn-remover" onclick="removerBloco(this)">Remover</button>
                </div>
                <div class="membro-block">
                    <input type="text" name="nome[]" placeholder="Nome" required>
                    <input type="text" name="cargo[]" placeholder="Cargo" value="Vice-Presidente" required>
                    <button type="button" class="btn btn-remover" onclick="removerBloco(this)">Remover</button>
                </div>
            </div>

            <button type="button" class="btn" onclick="adicionarBloco()">Adicionar Membro</button>
            <br><br>
            <button type="submit" class="btn">Enviar Formulário</button>
        </form>
    </div>

    <script>
        function adicionarBloco() {
            const container = document.getElementById('membros-container');
            const novoBloco = document.createElement('div');
            novoBloco.className = 'membro-block';
            novoBloco.innerHTML = `
                <input type="text" name="nome[]" placeholder="Nome" required>
                <input type="text" name="cargo[]" placeholder="Cargo" required>
                <button type="button" class="btn btn-remover" onclick="removerBloco(this)">Remover</button>
            `;
            container.appendChild(novoBloco);
        }

        function removerBloco(botao) {
            const bloco = botao.parentElement;
            if (document.querySelectorAll('.membro-block').length > 1) {
                bloco.remove();
            } else {
                alert('Deve haver pelo menos um membro!');
            }
        }
    </script>
</body>
</html>