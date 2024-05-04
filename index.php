<?php
session_start();
const PASSWORD = '230788'; // Senha para acesso

// Check login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['password'])) {
    if ($_POST['password'] === PASSWORD) {
        $_SESSION['authenticated'] = true;
    } else {
        echo "<script>alert('Senha incorreta');</script>";
    }
}

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <form method="post">
        <label for="password">Senha:</label>
        <input type="password" name="password" id="password" required>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
<?php
    exit();
}

include 'conexao.php';

// Função para calcular o total de dívidas
function getTotalDividas($conn) {
    $sql = "SELECT SUM(valor_total) as total FROM dividas";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Função para calcular o total a pagar no mês
function getTotalMes($conn) {
    $sql = "SELECT SUM(valor_total / parcelas) as total_mes FROM dividas WHERE MONTH(data_vencimento) = MONTH(CURDATE()) AND YEAR(data_vencimento) = YEAR(CURDATE())";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_mes'];
}

// Função para obter dívidas vencendo hoje
function getDividasVencendoHoje($conn) {
    $sql = "SELECT descricao, valor_total FROM dividas WHERE data_vencimento = CURDATE()";
    return $conn->query($sql);
}

// Atualizar ou excluir dívidas
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['atualizar'])) {
        $id = $_POST['id'];
        $descricao = $_POST['descricao'];
        $valor_total = $_POST['valor_total'];
        $data_vencimento = $_POST['data_vencimento'];
        $parcelas = $_POST['parcelas'];

        $sql = "UPDATE dividas SET descricao = ?, valor_total = ?, data_vencimento = ?, parcelas = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssi", $descricao, $valor_total, $data_vencimento, $parcelas, $id);
        $stmt->execute();
    } elseif (isset($_POST['repetir'])) {
        $descricao = $_POST['descricao'];
        $valor_total = $_POST['valor_total'];
        $data_vencimento = $_POST['data_vencimento'];
        $parcelas = $_POST['parcelas'];
        $repeticoes = $_POST['repeticoes'];

        for ($i = 0; $i < $repeticoes; $i++) {
            $nova_data_vencimento = date('Y-m-d', strtotime("+$i month", strtotime($data_vencimento)));
            $sql = "INSERT INTO dividas (descricao, valor_total, data_vencimento, parcelas) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdsi", $descricao, $valor_total, $nova_data_vencimento, $parcelas);
            $stmt->execute();
        }
    } elseif (isset($_POST['pago']) || isset($_POST['excluir'])) {
        $id = $_POST['id'];
        if (isset($_POST['pago'])) {
            $sql = "UPDATE dividas SET parcelas = parcelas - 1 WHERE id = ? AND parcelas > 0";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        if (isset($_POST['excluir'])) {
            $sql = "DELETE FROM dividas WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }
}

// Função para renderizar uma linha de dívida
function renderizarLinhaDivida($row) {
    $id = htmlspecialchars($row['id']);
    $descricao = htmlspecialchars($row['descricao']);
    $valor_total = htmlspecialchars($row['valor_total']);
    $data_vencimento = htmlspecialchars($row['data_vencimento']);
    $parcelas = htmlspecialchars($row['parcelas']);
    $form_id = "form-$id";

    return "
    <tr>
        <form method='post' id='{$form_id}'>
            <input type='hidden' name='id' value='{$id}'>
            <td><input type='text' name='descricao' value='{$descricao}' form='{$form_id}'></td>
            <td><input type='number' name='valor_total' value='{$valor_total}' step='1' form='{$form_id}'></td>
            <td><input type='date' name='data_vencimento' value='{$data_vencimento}' form='{$form_id}'></td>
            <td><input type='number' name='parcelas' value='{$parcelas}' form='{$form_id}'></td>
            <td>
                <button type='submit' class='btn btn-warning' name='atualizar' form='{$form_id}'>Atualizar</button>
                <button type='submit' class='btn btn-success' name='pago' form='{$form_id}'>Pago</button>
                <button type='submit' class='btn btn-danger' name='excluir' form='{$form_id}'>Excluir</button>
            </td>
        </form>
    </tr>";
}

// Buscar dívidas
$sql = "SELECT * FROM dividas";
$result = $conn->query($sql);
$totalDividas = getTotalDividas($conn);
$totalMes = getTotalMes($conn);
$dividasVencendoHoje = getDividasVencendoHoje($conn);

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerenciador de Dívidas</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2>Gerenciador de Dívidas</h2>

    <!-- Notificações -->
    <?php if ($dividasVencendoHoje->num_rows > 0): ?>
        <div class="alert alert-warning">
            <strong>Atenção!</strong> Você tem dívidas vencendo hoje:
            <ul>
                <?php while ($row = $dividasVencendoHoje->fetch_assoc()): ?>
                    <li><?= htmlspecialchars($row['descricao']) ?>: ¥<?= number_format($row['valor_total'], 0, ',', '.') ?></li>
                <?php endwhile; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <form method="post">
                <div class="form-group">
                    <label for="descricao">Descrição da Dívida:</label>
                    <input type="text" class="form-control" id="descricao" name="descricao" required>
                </div>
                <div class="form-group">
                    <label for="valor_total">Valor Total (¥):</label>
                    <input type="number" class="form-control" id="valor_total" name="valor_total" step="1" required>
                </div>
                <div class="form-group">
                    <label for="data_vencimento">Data de Vencimento:</label>
                    <input type="date" class="form-control" id="data_vencimento" name="data_vencimento" required>
                </div>
                <div class="form-group">
                    <label for="parcelas">Quantidade de Parcelamento:</label>
                    <input type="number" class="form-control" id="parcelas" name="parcelas" required>
                </div>
                <div class="form-group">
                    <label for="repeticoes">Repetir por quantos meses?</label>
                    <select class="form-control" id="repeticoes" name="repeticoes">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="6">6</option>
                        <option value="12">12</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" name="repetir">Repetir</button>
            </form>
        </div>
        <div class="col-md-4">
            <h3>Total de Dívidas: ¥ <?= number_format($totalDividas, 0, ',', '.') ?></h3>
            <h3>Total a Pagar Este Mês: ¥ <?= number_format($totalMes, 0, ',', '.') ?></h3>
        </div>
    </div>
    <h3>Dívidas Cadastradas</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Valor Total (¥)</th>
                <th>Data de Vencimento</th>
                <th>Parcelas Restantes</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            echo $result->num_rows > 0 
                ? array_reduce($result->fetch_all(MYSQLI_ASSOC), fn($acc, $row) => $acc . renderizarLinhaDivida($row), '')
                : "<tr><td colspan='5'>Nenhuma dívida cadastrada.</td></tr>";
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
