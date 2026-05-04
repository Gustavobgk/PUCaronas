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
            $query = "SELECT * FROM carona WHERE 1=1";
            if (isset($_GET['origem']) && !empty($_GET['origem'])) {
                $origem = $conn->real_escape_string($_GET['origem']);
                $query .= " AND origem LIKE '%$origem%'";
            }
            if (isset($_GET['destino']) && !empty($_GET['destino'])) {
                $destino = $conn->real_escape_string($_GET['destino']);
                $query .= " AND destino LIKE '%$destino%'";
            }
            // Você pode adicionar mais filtros aqui se necessário, como por status ou id_motorista

            $result = $conn->query($query);
            $caronas = [];
            while ($row = $result->fetch_assoc()) {
                $caronas[] = $row;
            }
            echo json_encode($caronas);
        }
        break;

        
    case 'POST':
        $id_motorista = $input['id_motorista'];
        $id_veiculo = $input['id_veiculo'];
        $titulo = $input['titulo'];
        $descricao = $input['descricao'];
        $mensagem = $input['mensagem'];
        $vagas = $input['vagas'];
        $status = "aberta";
        $origem = $input['origem'];
        $destino = $input['destino'];
        $sql = "INSERT INTO carona(id_motorista,id_veiculo,titulo,descricao,mensagem,vagas,status,origem,destino) VALUES ('$id_motorista', '$id_veiculo', '$titulo', '$descricao', '$mensagem', '$vagas', '$status', '$origem', '$destino')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "carona sucesso"]);
        } else {
            echo json_encode(["message" => "Erro ao criar carona", "error" => $conn->error]);
        }
        break;

    case 'PUT':
        $id = $_GET['id'];
        $id_motorista = $input['id_motorista'];
        $id_veiculo = $input['id_veiculo'];
        $titulo = $input['titulo'];
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