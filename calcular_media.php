<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $veiculo_id = $_GET['veiculo_id'];

    $stmt = $pdo->prepare('SELECT * FROM abastecimentos WHERE veiculo_id = :veiculo_id AND tanque_completo = 1 ORDER BY data ASC');
    $stmt->execute(['veiculo_id' => $veiculo_id]);
    $abastecimentos = $stmt->fetchAll();

    if (count($abastecimentos) < 2) {
        echo "Não há dados suficientes para calcular a média de consumo.";
        exit;
    }

    $totalKm = 0;
    $totalLitros = 0;

    for ($i = 1; $i < count($abastecimentos); $i++) {
        $kmRodados = $abastecimentos[$i]['km_hodometro'] - $abastecimentos[$i-1]['km_hodometro'];
        $litrosConsumidos = $abastecimentos[$i]['litros'];
        $totalKm += $kmRodados;
        $totalLitros += $litrosConsumidos;
    }

    if ($totalLitros == 0) {
        echo "Erro no cálculo: Total de litros é zero.";
        exit;
    }

    $mediaConsumo = $totalKm / $totalLitros;
    echo "Média de consumo do veículo: " . number_format($mediaConsumo, 2) . " km/l";
} 