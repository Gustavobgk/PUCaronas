<?php
include_once('conexao.php');
session_start();

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario']) || count($_SESSION['usuario']) === 0) {
    $retorno = ['status' => 'nok', 'mensagem' => 'Usuário não logado', 'data' => []];
    header('Content-type:application/json;charset:utf-8');
    echo json_encode($retorno);
    exit;
}

$usuarioSess = $_SESSION['usuario'][0];
if (($usuarioSess['tipo'] ?? '') !== 'motorista') {
    $retorno = ['status' => 'nok', 'mensagem' => 'Apenas motoristas podem postar caronas', 'data' => []];
    header('Content-type:application/json;charset:utf-8');
    echo json_encode($retorno);
    exit;
}

$origem = trim($_POST['origem'] ?? '');
$destino = trim($_POST['destino'] ?? '');
$data_hora = trim($_POST['data_hora'] ?? '');
$vagas = (int)($_POST['vagas'] ?? 0);
$valor = (float)($_POST['valor'] ?? 0);
$descricao = trim($_POST['descricao'] ?? '');

if (!$origem || !$destino || !$data_hora || !$vagas || !$valor) {
    $retorno = ['status' => 'nok', 'mensagem' => 'Dados incompletos', 'data' => []];
    header('Content-type:application/json;charset:utf-8');
    echo json_encode($retorno);
    exit;
}

$stmt = $conexao->prepare('INSERT INTO caronas (motorista_id, motorista_nome, origem, destino, data_hora, vagas, valor, descricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->bind_param('isssiiss', $usuarioSess['id'], $usuarioSess['nome'], $origem, $destino, $data_hora, $vagas, $valor, $descricao);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $retorno = ['status' => 'ok', 'mensagem' => 'Carona publicada', 'data' => []];
} else {
    $retorno = ['status' => 'nok', 'mensagem' => 'Falha ao publicar carona', 'data' => []];
}
$stmt->close();
$conexao->close();

header('Content-type:application/json;charset:utf-8');
echo json_encode($retorno);
