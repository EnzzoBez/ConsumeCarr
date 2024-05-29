<?php

    include ('db.php');

    if ($_SERVER ['REQUEST_METHOD'] =='POST'){
        $placa = $_POST['placa'];
        $stmt = $pdo->prepare('INSERT INTO veiculos (placa) VALUES (:placa)');
        $stmt->execute (['placa' => $placa]);
    header ('Location> index.php');
}