<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $veiculo_id = $_POST['veiculo_id'];
    $data = $_POST['data'];
    $km_hodometro = $_POST['km_hodometro'];
    $litros = $_POST['litros'];
    $valor_gasto = $_POST['valor_gasto'];
    $tanque_completo = isset($_POST['tanque_completo']) ? 1 : 0;

    $stmt = $pdo->prepare('INSERT INTO abastecimentos (veiculo_id, data, km_hodometro, litros, valor_gasto, tanque_completo) VALUES (:veiculo_id, :data, :km_hodometro, :litros, :valor_gasto, :tanque_completo)');
    $stmt->execute([
        'veiculo_id' => $veiculo_id,
        'data' => $data,
        'km_hodometro' => $km_hodometro,
        'litros' => $litros,
        'valor_gasto' => $valor_gasto,
        'tanque_completo' => $tanque_completo
    ]);

    header('Location: index.php');
}