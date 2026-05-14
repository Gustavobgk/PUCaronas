<?php
include '../../config/db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';


switch ($method) {
    case 'GET':
        if ($action === 'listar_espera') {
            $result = $conn->query("SELECT * FROM usuario WHERE status='espera'");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        elseif (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM usuario WHERE id=$id");
            $data = $result->fetch_assoc();
            echo json_encode($data);
        }
        elseif (isset($_GET['email'])) {
        $email = $_GET['email'];
        $result = $conn->query("SELECT * FROM usuario WHERE email='$email'");
        $data = $result->fetch_assoc();
        echo json_encode($data);}
        
        else {
            $result = $conn->query("SELECT * FROM usuario");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

    case 'POST':
        switch ($action){
        case 'cadastro':
        $nome = $input['nome'];
        $email = $input['email'];
        $senha_hash = $input['senha_hash'];
        $data_nasc = $input['data_nasc'];
        $status = "espera";
        $cargo = $input['cargo'];
        $doc = $input['doc'];
        if ($conn->query("INSERT INTO usuario (nome, email,senha_hash,data_nasc,status,cargo,doc) VALUES ('$nome', '$email', '$senha_hash', '$data_nasc', '$status', '$cargo','$doc')")){
            echo json_encode(["message" => "Usuário adicionado com sucesso."]);
        }
        else{
             echo json_encode(["error" => $conn->error]);
        }
            break;
        
        case 'aprovar':
            $id_usuario = $input['id_usuario'];
            if ($conn->query("UPDATE usuario SET status='aprovado' WHERE id=$id_usuario")) {
                echo json_encode(["message" => "sucesso"]);
            } else {
                echo json_encode(["error" => $conn->error]);
            }
            break;
        
        case 'rejeitar':
            $id_usuario = $input['id_usuario'];
            if ($conn->query("UPDATE usuario SET status='reprovado' WHERE id=$id_usuario")) {
                echo json_encode(["message" => "sucesso"]);
            } else {
                echo json_encode(["error" => $conn->error]);
            }
            break;

        case 'login':

                $email = $input['email'];
                $senha = $input['senha_hash'];
                $result = $conn->query("SELECT * FROM usuario WHERE email='$email'");
                $user = $result->fetch_assoc();
                if (!$user) {
                    echo json_encode(["error" => "Email ou senha inválidos"]);
                }
                else if ($user['senha_hash'] !== $senha) {
                    echo json_encode(["error" => "Email ou senha inválidos"]);
                }
                else if ($user['cargo'] == 'admin') {
                    echo json_encode(["message" => "adm login ok", "nome" => $user['nome']]);
                }

                else if ($user['status'] == 'espera') {
                    echo json_encode(["message" => "login espera", "nome" => $user['nome']]);
                }
                else if ($user['status'] !== 'aprovado') {
                    echo json_encode(["message" => "login n ok", "nome" => $user['nome']]);
                }
               
                else {
                    echo json_encode(["message" => "Login ok", "nome" => $user['nome']]);
                }
                break;

        
        
        }
        break;

    case 'PUT':
        $id = $_GET['id'];
        $nome = $input['nome'];
        $email = $input['email'];
        $senha_hash = $input['senha_hash'];
        $data_nasc = $input['data_nasc'];
        $status = $input['status'];
        $cargo = $input['cargo'];
        $doc = $input['doc'];
        if  (  $conn->query("UPDATE usuario SET nome='$nome' ,email='$email',senha_hash='$senha_hash' ,data_nasc = '$data_nasc', status ='$status' ,cargo = '$cargo' ,doc = '$doc' WHERE id=$id")
       ) {echo json_encode(["message" => "Usuário atualizado com sucesso"]);}
       else{
             echo json_encode(["error" => $conn->error]);
       } 
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("CALL deletar_usuario($id)");
        echo json_encode(["message" => "Usuário deletado com sucesso"]);
        break;

    default:
        echo json_encode(["message" => "Método Inválido"]);
        break;
}

$conn->close();
?>