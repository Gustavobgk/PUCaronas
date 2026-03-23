<?php
include_once('conexao.php');

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$email_academico = $_POST['email_academico'] ?? '';
$idade = (int)($_POST['idade'] ?? 0);
$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';
$tipo = $_POST['tipo'] ?? 'passageiro';

if (!$nome || !$email || !$email_academico || !$idade || !$usuario || !$senha) {
    $retorno = ['status' => 'nok', 'mensagem' => 'Todos os campos são obrigatórios', 'data' => []];
    header('Content-type:application/json;charset:utf-8');
    echo json_encode($retorno);
    exit;
}

// Validação da idade
if ($idade < 16 || $idade > 100) {
    $retorno = ['status' => 'nok', 'mensagem' => 'Idade deve ser entre 16 e 100 anos', 'data' => []];
    header('Content-type:application/json;charset:utf-8');
    echo json_encode($retorno);
    exit;
}

// Validação do email acadêmico
if (!preg_match('/@puc/i', $email_academico)) {
    $retorno = ['status' => 'nok', 'mensagem' => 'Email acadêmico deve ser da PUC', 'data' => []];
    header('Content-type:application/json;charset:utf-8');
    echo json_encode($retorno);
    exit;
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Campos específicos do motorista
$foto_perfil = null;
$carro = null;
$modelo = null;
$placa = null;

if ($tipo === 'motorista') {
    $carro = $_POST['carro'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $placa = $_POST['placa'] ?? '';

    if (!$carro || !$modelo || !$placa) {
        $retorno = ['status' => 'nok', 'mensagem' => 'Informações do veículo são obrigatórias para motoristas', 'data' => []];
        header('Content-type:application/json;charset:utf-8');
        echo json_encode($retorno);
        exit;
    }

    // Upload da foto de perfil
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $file_name = uniqid() . '_' . basename($_FILES['foto_perfil']['name']);
        $upload_file = $upload_dir . $file_name;

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['foto_perfil']['type'], $allowed_types)) {
            $retorno = ['status' => 'nok', 'mensagem' => 'Tipo de arquivo não permitido. Use JPG, PNG ou GIF', 'data' => []];
            header('Content-type:application/json;charset:utf-8');
            echo json_encode($retorno);
            exit;
        }

        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $upload_file)) {
            $foto_perfil = $file_name;
        } else {
            $retorno = ['status' => 'nok', 'mensagem' => 'Erro ao fazer upload da foto', 'data' => []];
            header('Content-type:application/json;charset:utf-8');
            echo json_encode($retorno);
            exit;
        }
    }
}

$stmt = $conexao->prepare('INSERT INTO cliente (nome, email, email_academico, idade, usuario, senha, instagram, ativo, tipo, foto_perfil, carro, modelo, placa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$instagram = '';
$ativo = 1;
$stmt->bind_param('sssisssssssss', $nome, $email, $email_academico, $idade, $usuario, $senha_hash, $instagram, $ativo, $tipo, $foto_perfil, $carro, $modelo, $placa);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $retorno = ['status' => 'ok', 'mensagem' => 'Cadastro efetuado com sucesso', 'data' => []];
} else {
    $retorno = ['status' => 'nok', 'mensagem' => 'Falha no cadastro', 'data' => []];
}

$stmt->close();
$conexao->close();

header('Content-type:application/json;charset:utf-8');
echo json_encode($retorno);
