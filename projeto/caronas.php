<?php
include '../../config/db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';


switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM caronas WHERE id=$id");
            $data = $result->fetch_assoc();
            echo json_encode($data);
        }
        elseif (isset($_GET['origem']) || isset($_GET['destino']) || isset($_GET['data']) || isset($_GET['preco_max']) || isset($_GET['horario'])) {
            $query = "SELECT * FROM caronas WHERE 1=1";
            
            if (!empty($_GET['origem'])) {
                $origem = $conn->real_escape_string($_GET['origem']);
                $query .= " AND origem LIKE '%$origem%'";
            }
   
            if (!empty($_GET['destino'])) {
                $destino = $conn->real_escape_string($_GET['destino']);
                $query .= " AND destino LIKE '%$destino%'";
            }
        
            if (!empty($_GET['data'])) {
                $data = $_GET['data'];
                $query .= " AND data_saida = '$data'";
            }
            
            if (!empty($_GET['preco_max'])) {
                $preco_max = floatval($_GET['preco_max']);
                $query .= " AND preco <= $preco_max";
            }
            
            if (!empty($_GET['horario'])) {
                $horario = $_GET['horario'];
                $query .= " AND horario_saida = '$horario'";
            }
            
            $result = $conn->query($query);
            $caronas = [];
            while ($row = $result->fetch_assoc()) {
                $caronas[] = $row;
            }
            echo json_encode($caronas);
        }
        else {
            $result = $conn->query("SELECT * FROM caronas");
            $caronas = [];
            while ($row = $result->fetch_assoc()) {
                $caronas[] = $row;
            }
            echo json_encode($caronas);
        }
        break;

    case 'POST':
        switch ($action){
            case 'criar_carona':
                $usuario_id = $input['usuario_id'];
                $origem = $input['origem'];
                $destino = $input['destino'];
                $data_saida = $input['data_saida'];
                $horario_saida = $input['horario_saida'];
                $assentos = $input['assentos'];
                $preco = $input['preco'];
                $descricao = $input['descricao'];
                $status = "ativa";
                
                if ($conn->query("INSERT INTO caronas (usuario_id, origem, destino, data_saida, horario_saida, assentos, preco, descricao, status) VALUES ('$usuario_id', '$origem', '$destino', '$data_saida', '$horario_saida', '$assentos', '$preco', '$descricao', '$status')")) {
                    echo json_encode(["message" => "Carona criada com sucesso."]);
                } else {
                    echo json_encode(["error" => $conn->error]);
                }
                break;
        }
        break;

    case 'PUT':
        $id = $_GET['id'];
        $status = $input['status'] ?? null;
        
        if ($status) {
            if ($conn->query("UPDATE caronas SET status='$status' WHERE id=$id")) {
                echo json_encode(["message" => "Carona atualizada com sucesso"]);
            } else {
                echo json_encode(["error" => $conn->error]);
            }
        }
        break;

    case 'DELETE':
        $id = $_GET['id'];
        if ($conn->query("DELETE FROM caronas WHERE id=$id")) {
            echo json_encode(["message" => "Carona deletada com sucesso"]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
        break;

    default:
        echo json_encode(["message" => "Método Inválido"]);
        break;
}

$conn->close();
?>
