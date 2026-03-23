<?php
include_once('conexao.php');
session_start();

if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario']) || count($_SESSION['usuario']) === 0) {
    echo json_encode(['status' => 'nok', 'mensagem' => 'Usuário não logado']);
    exit;
}

$usuario = $_SESSION['usuario'][0];
$id = $usuario['id'];

$stmt = $conexao->prepare('DELETE FROM cliente WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    session_unset();
    session_destroy();
    echo json_encode(['status' => 'ok', 'mensagem' => 'Conta excluída com sucesso']);
} else {
    echo json_encode(['status' => 'nok', 'mensagem' => 'Falha ao excluir conta']);
}

$stmt->close();
$conexao->close();
header('Content-type:application/json;charset:utf-8');
