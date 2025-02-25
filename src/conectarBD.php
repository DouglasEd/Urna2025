<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Garante o autoload do Composer

use Dotenv\Dotenv;

// Carrega as variáveis do .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();    

// Conecta ao banco de dados usando as variáveis do .env
$conexao = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

// Verifica se houve erro na conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}
?>