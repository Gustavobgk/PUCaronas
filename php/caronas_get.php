<?php
include_once('conexao.php');
$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

if (isset($_GET['id'])) {
    $stmt = $conexao->prepare('SELECT * FROM caronas WHERE id = ?');
    $stmt->bind_param('i', $_GET['id']);
} else {
    $stmt = $conexao->prepare('SELECT * FROM caronas ORDER BY data_hora ASC');
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
