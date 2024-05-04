<?php
// Configuração dos detalhes do banco de dados
$host = 'localhost';  // Endereço do servidor MySQL
$dbname = 'Banco_de_Dados';  // Nome do banco de dados
$username = 'Usuario';  // Nome do usuário
$password = 'Senha';  // Senha do usuário

// Criar a conexão com o banco de dados usando o MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Checar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
