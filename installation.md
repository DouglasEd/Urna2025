# Passos de instalação da aplicação

## 1 - Criar o .env

```bash
cp .env.example .env
```
- Após isso use o modelo do .env.example e defina suas variáveis

## 2 - Rodar o Docker
``` bash
docker compose up -d
```

## 3 - Rodar o script para criar as tabelas do banco de dados

### 3.1 - Exportar variáveis de ambiente para o terminal

``` bash
export DB_HOST=mariadb
export DB_USER=DouglasEd
export DB_PASSWORD=decb010203
export DB_NAME=urna
export DB_ROOT_PASSWORD=root
```

### 3.2 Executar o script 

``` bash
docker exec -i mariadb mariadb -u root -p${DB_ROOT_PASSWORD} ${DB_NAME} < /caminho/Urna2025/urna.sql
```
