<?php
session_start();
if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header('Location: index.php');
    exit();
}

include 'conexao.php';

// Função para calcular o total pago no mês
function getTotalPagoMes($conn) {
    $sql = "SELECT SUM(valor_total / parcelas) as total_mes FROM dividas WHERE pago = 1 AND MONTH(data_vencimento) = MONTH(CURDATE()) AND YEAR(data_vencimento) = YEAR(CURDATE())";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_mes'];
}

// Função para calcular o total pago durante o ano
function getTotalPagoAno($conn) {
    $sql = "SELECT SUM(valor_total) as total_ano FROM dividas WHERE pago = 1 AND YEAR(data_vencimento) = YEAR(CURDATE())";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_ano'];
}

// Função para renderizar uma linha de dívida
function renderizarLinhaDivida($row) {
    $descricao = htmlspecialchars($row['descricao']);
    $valor_total = htmlspecialchars($row['valor_total']);
    $data_vencimento = htmlspecialchars($row['data_vencimento']);
    $parcelas = htmlspecialchars($row['parcelas']);

    return "
    <tr>
        <td>{$descricao}</td>
        <td>{$valor_total}</td>
        <td>{$data_vencimento}</td>
        <td>{$parcelas}</td>
    </tr>";
}

// Buscar dívidas pagas
$sql = "SELECT * FROM dividas WHERE pago = 1";
$result = $conn->query($sql);
$totalPagoMes = getTotalPagoMes($conn);
$totalPagoAno = getTotalPagoAno($conn);

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Contas Pagas</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2>Contas Pagas</h2>
    <h3>Total Pago Este Mês: ¥ <?= number_format($totalPagoMes, 0, ',', '.') ?></h3>
    <h3>Total Pago Durante o Ano: ¥ <?= number_format($totalPagoAno, 0, ',', '.') ?></h3>
    <h3><a href="index.php" class="btn btn-info">Voltar ao Gerenciador de Dívidas</a></h3>
    <h3><a href="index.php" class="btn btn-warning">Retornar a Contas a Pagar</a></h3>
    <h3>Dívidas Pagas</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Valor Total (¥)</th>
                <th>Data de Vencimento</th>
                <th>Parcelas Restantes</th>
            </tr>
        </thead>
        <tbody>
            <?php
            echo $result->num_rows > 0 
                ? array_reduce($result->fetch_all(MYSQLI_ASSOC), fn($acc, $row) => $acc . renderizarLinhaDivida($row), '')
                : "<tr><td colspan='4'>Nenhuma dívida paga encontrada.</td></tr>";
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
