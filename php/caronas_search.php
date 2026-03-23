<?php
include_once('conexao.php');

$origem = trim($_GET['origem'] ?? '');
$destino = trim($_GET['destino'] ?? '');

$query = 'SELECT * FROM caronas';
$cond = [];
$params = [];
$types = '';

if ($origem) {
    $cond[] = 'origem LIKE ?';
    $params[] = "%$origem%";
    $types .= 's';
}

if ($destino) {
    $cond[] = 'destino LIKE ?';
    $params[] = "%$destino%";
    $types .= 's';
}

if (count($cond) > 0) {
    $query .= ' WHERE ' . implode(' AND ', $cond);
}

$query .= ' ORDER BY data_hora ASC';

$stmt = $conexao->prepare($query);
if ($stmt === false) {
    echo json_encode(['status' => 'nok', 'mensagem' => 'Erro na query', 'data' => []]);
    exit;
}

if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();
$cargas = [];
while ($linha = $resultado->fetch_assoc()) {
    $cargas[] = $linha;
}

if (count($cargas) > 0) {
    $retorno = ['status' => 'ok', 'mensagem' => 'OK', 'data' => $cargas];
} else {
    $retorno = ['status' => 'nok', 'mensagem' => 'Nenhuma carona encontrada', 'data' => []];
}

$stmt->close();
$conexao->close();
header('Content-type:application/json;charset:utf-8');
echo json_encode($retorno);
