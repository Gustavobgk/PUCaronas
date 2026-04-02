<?php
include '../../config/db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM veiculo WHERE id=$id");
            $data = $result->fetch_assoc();
            echo json_encode($data);
        } else {
            $result = $conn->query("SELECT * FROM veiculo");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

        
    case 'POST':
        $id_motorista = $input['id_motorista'];
        $modelo = $input['modelo'];
        $placa = $input['placa'];
        $n_assentos = $input['n_assentos'];
        $conn->query("INSERT INTO veiculo(id_motorista,modelo,placa,n_assentos) VALUES ('$id_motorista', '$modelo', '$placa', '$n_assentos')");
        echo json_encode(["message" => "veiculo sucesso"]);
        break;

    case 'PUT':  
        $id = $_GET['id'] ?? null;      
        $id_motorista = $input['id_motorista'];
        $modelo = $input['modelo'];
        $placa = $input['placa'];
        $n_assentos = $input['n_assentos'];
        if ($id) {
        $conn->query("UPDATE veiculo SET modelo='$modelo', placa='$placa', n_assentos='$n_assentos' WHERE id=$id");
        echo json_encode(["message" => "veiculo atualizado"]);
        } 
        else {
        echo json_encode(["error" => "veiculo não atualizado"]);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("DELETE FROM veiculo WHERE id=$id");
        echo json_encode(["message" => "del sucesso"]);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

$conn->close();
?>git 