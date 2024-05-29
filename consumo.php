<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "abastecimento_veiculos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function calcularMediaConsumo($veiculo_id, $km_atual, $litros_atual) {
    global $conn;
    $sql = "SELECT km_hodometro, litros FROM abastecimentos WHERE veiculo_id = $veiculo_id AND tanque_completo = 1 ORDER BY data DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $km_anterior = $row['km_hodometro'];
        $litros_anterior = $row['litros'];

        $km_percorridos = $km_atual - $km_anterior;
        $media_consumo = $km_percorridos / $litros_atual;
        return $media_consumo;
    }
    return null;
}

// Inserção de um novo veículo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['novo_veiculo'])) {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $placa = $_POST['placa'];
    $ano = $_POST['ano'];

    $sql = "INSERT INTO veiculos (marca, modelo, placa, ano) VALUES ('$marca', '$modelo', '$placa', $ano)";
    $conn->query($sql);
}

// Inserção de um novo abastecimento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['novo_abastecimento'])) {
    $veiculo_id = $_POST['veiculo_id'];
    $data = $_POST['data'];
    $km_hodometro = $_POST['km_hodometro'];
    $litros = $_POST['litros'];
    $valor = $_POST['valor'];
    $tanque_completo = isset($_POST['tanque_completo']) ? 1 : 0;

    $sql = "INSERT INTO abastecimentos (veiculo_id, data, km_hodometro, litros, valor, tanque_completo) VALUES ($veiculo_id, '$data', $km_hodometro, $litros, $valor, $tanque_completo)";
    $conn->query($sql);

    if ($tanque_completo) {
        $media_consumo = calcularMediaConsumo($veiculo_id, $km_hodometro, $litros);
        if ($media_consumo !== null) {
            echo "Média de consumo: " . round($media_consumo, 2) . " km/l";
        }
    }
}

// Listar todos os abastecimentos
$sql = "SELECT v.marca, v.modelo, a.data, a.km_hodometro, a.litros, a.valor, a.tanque_completo FROM abastecimentos a JOIN veiculos v ON a.veiculo_id = v.id ORDER BY a.data DESC";
$abastecimentos = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Controle de Abastecimento</title>
</head>
<body>
    <h1>Controle de Abastecimento</h1>

    <h2>Cadastrar Novo Veículo</h2>
    <form method="POST">
        Marca: <input type="text" name="marca" required><br>
        Modelo: <input type="text" name="modelo" required><br>
        Placa: <input type="text" name="placa" required><br>
        Ano: <input type="number" name="ano" required><br>
        <button type="submit" name="novo_veiculo">Cadastrar Veículo</button>
    </form>

    <h2>Cadastrar Novo Abastecimento</h2>
    <form method="POST">
        Veículo: <select name="veiculo_id" required>
            <?php
            // Recuperar a lista de veículos
            $conn = new mysqli($servername, $username, $password, $dbname);
            $sql = "SELECT id, marca, modelo FROM veiculos";
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['marca']} {$row['modelo']}</option>";
            }
            $conn->close();
            ?>
        </select><br>
        Data: <input type="date" name="data" required><br>
        Km Hodômetro: <input type="number" name="km_hodometro" required><br>
        Litros: <input type="number" step="0.01" name="litros" required><br>
        Valor: <input type="number" step="0.01" name="valor" required><br>
        Tanque Completo: <input type="checkbox" name="tanque_completo"><br>
        <button type="submit" name="novo_abastecimento">Cadastrar Abastecimento</button>
    </form>

    <h2>Lista de Abastecimentos</h2>
    <table border="1">
        <tr>
            <th>Veículo</th>
            <th>Data</th>
            <th>Km Hodômetro</th>
            <th>Litros</th>
            <th>Valor</th>
            <th>Tanque Completo</th>
        </tr>
        <?php
        if ($abastecimentos->num_rows > 0) {
            while($row = $abastecimentos->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['marca']} {$row['modelo']}</td>
                        <td>{$row['data']}</td>
                        <td>{$row['km_hodometro']}</td>
                        <td>{$row['litros']}</td>
                        <td>{$row['valor']}</td>
                        <td>" . ($row['tanque_completo'] ? 'Sim' : 'Não') . "</td>
                    </tr>";
            }
        }
        ?>
    </table>
</body>
</html>