# Arquivos importantes
- Adicionar arquivo credentials.php com as informações do Banco de Dados
```bash
<?php
if (!defined('PHP_VERSION')) {
    die('Acesso direto não permitido');
}
define('DB_HOST', 'Seu Host');
define('DB_USER', 'Seu Usuario');
define('DB_PASSWORD', 'Sua Senha');
define('DB_NAME', 'Nome do BD');
?>
```
