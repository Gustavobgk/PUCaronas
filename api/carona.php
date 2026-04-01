<?php
include '../../config/db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM carona WHERE id=$id");
            $data = $result->fetch_assoc();
            echo json_encode($data);
        } else {
            $result = $conn->query("SELECT * FROM carona");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

    case 'POST':
        $id_motorista = $input['id_motorista'];
        $id_veiculo = $input['id_veiculo'];
        $titulo = $input['id_veiculo'];
        $descricao = $input['descricao'];
        $mensagem = $input['mensagem'];
        $vagas = $input['vagas'];
        $status = $input['status'];
        $origem = $input['origem'];
        $destino = $input['destino'];
        $conn->query("INSERT INTO carona(id_motorista,id_veiculo,titulo,descricao,mensagem,vagas,status,origem,destino) VALUES ('$id_motorista', '$id_veiculo', '$titulo', '$descricao', '$mensagem', '$vagas', '$status', '$origem', '$destino')");
        echo json_encode(["message" => "carona sucesso"]);
        break;

    case 'PUT':
        $id = $_GET['id'];
        $id_motorista = $input['id_motorista'];
        $id_veiculo = $input['id_veiculo'];
        $titulo = $input['id_veiculo'];
        $descricao = $input['descricao'];
        $mensagem = $input['mensagem'];
        $vagas = $input['vagas'];
        $status = $input['status'];
        $origem = $input['origem'];
        $destino = $input['destino'];
        $conn->query("UPDATE carona SET id_motorista='$id_motorista', id_veiculo='$id_veiculo', titulo='$titulo', descricao='$descricao', mensagem='$mensagem', vagas='$vagas', status='$status', origem='$origem', destino='$destino' WHERE id=$id");
        echo json_encode(["message" => "carona atualizada"]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("DELETE FROM carona WHERE id=$id");
        echo json_encode(["message" => "del sucesso"]);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

$conn->close();
?>