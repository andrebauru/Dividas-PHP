<?php
// Configuração dos detalhes do banco de dados
$host = 'localhost';  // Endereço do servidor MySQL
$dbname = 'terreiro_dividas';  // Nome do banco de dados
$username = 'terreiro_andretsc';  // Nome do usuário
$password = 'Wilhelm1988';  // Senha do usuário

// Criar a conexão com o banco de dados usando o MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Checar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
