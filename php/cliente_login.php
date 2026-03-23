<?php
    include_once('conexao.php');
    // Configurando o padrão de retorno em todas
    // as situações
    $retorno = [
        'status'    => '', // ok - nok
        'mensagem'  => '', // mensagem que envio para o front
        'data'      => []
    ];

    $stmt = $conexao->prepare("SELECT * FROM cliente WHERE usuario = ?");
    $stmt->bind_param("s", $_POST['usuario']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $tabela = [];
    if ($resultado->num_rows > 0) {
        $linha = $resultado->fetch_assoc();
        if (password_verify($_POST['senha'], $linha['senha'])) {
            $tabela[] = $linha;
            session_start();
            $_SESSION['usuario'] = $tabela;

            $retorno = [
                'status'    => 'ok',
                'mensagem'  => 'Login efetuado com sucesso.',
                'data'      => $tabela
            ];
        } else {
            $retorno = [
                'status'    => 'nok',
                'mensagem'  => 'Credenciais inválidas.',
                'data'      => []
            ];
        }
    } else {
        $retorno = [
            'status'    => 'nok',
            'mensagem'  => 'Usuário não encontrado.',
            'data'      => []
        ];
    }

    // Fechamento do estado e conexão.
    $stmt->close();
    $conexao->close();

    // Estou enviando para o FRONT o array RETORNO
    // mas no formato JSON
    header("Content-type:application/json;charset:utf-8");
    echo json_encode($retorno);