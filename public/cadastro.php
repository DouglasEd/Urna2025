<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Garante o autoload do Composer

use Dotenv\Dotenv;

// Carrega as variáveis do .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();    

$ipv4 = $_SERVER['SERVER_ADDR'] ?? 'Não foi possível obter o IPv4';

// Conecta ao banco de dados usando as variáveis do .env
$conexao = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans bg-gray-100">

    <div class="bg-blue-600 p-4 text-center text-white font-mono border-b-2 border-blue-700">
        Gestor de Candidaturas
    </div>

    <div class="max-w-4xl mx-auto p-8 bg-white mt-8 rounded-lg shadow-xl">
        <h1 class="text-4xl font-bold mb-8 text-blue-600">Cadastro de Chapa</h1>
        <form method="POST">

            <div class="mb-6">
                <label for="nome_chapa" class="block text-lg font-medium text-gray-700 mb-2">Nome da Chapa:</label>
                <input type="text" name="nome_chapa" id="nome_chapa" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <h3 class="text-2xl font-semibold mt-8 mb-6 text-blue-600">Membros da Chapa</h3>

            <div id="membros-container">
                <!-- Blocos de membros serão adicionados aqui -->
                <div class="membro-block border border-gray-300 p-6 mb-6 rounded-lg bg-gray-50">
                    <input type="text" name="nome[]" placeholder="Nome" required class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <input type="text" name="cargo[]" placeholder="Cargo" value="Presidente" required class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button type="button" class="btn btn-remover bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300" onclick="removerBloco(this)">Remover</button>
                </div>
                <div class="membro-block border border-gray-300 p-6 mb-6 rounded-lg bg-gray-50">
                    <input type="text" name="nome[]" placeholder="Nome" required class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <input type="text" name="cargo[]" placeholder="Cargo" value="Vice-Presidente" required class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button type="button" class="btn btn-remover bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300" onclick="removerBloco(this)">Remover</button>
                </div>
            </div>

            <button type="button" class="btn bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition duration-300" onclick="adicionarBloco()">Adicionar Membro</button>
            <br><br>
            <button type="submit" class="btn bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">Confirmar Cadastro de Chapa</button>
        </form>
    </div>

    <script>
        function adicionarBloco() {
            const container = document.getElementById('membros-container');
            const novoBloco = document.createElement('div');
            novoBloco.className = 'membro-block border border-gray-300 p-6 mb-6 rounded-lg bg-gray-50';
            novoBloco.innerHTML = `
                <input type="text" name="nome[]" placeholder="Nome" required class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <input type="text" name="cargo[]" placeholder="Cargo" required class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="button" class="btn btn-remover bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300" onclick="removerBloco(this)">Remover</button>
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