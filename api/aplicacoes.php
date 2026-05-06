<?php
include '../../config/db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
           if (isset($_GET['id_passageiro'])) {
            $id_passageiro = $_GET['id_passageiro'];
$result = $conn->query("
    SELECT 
        a.id,
        a.id_passageiro,
        a.id_carona,
        a.status,
        a.mensagem,
        c.origem,
        c.titulo,
        c.destino,
        c.vagas
    FROM aplicacao a
    INNER JOIN carona c ON c.id = a.id_carona
    WHERE a.id_passageiro = $id_passageiro
");
            $aplicacoes = [];
            while ($row = $result->fetch_assoc()) {
                $aplicacoes[] = $row;
            }
            echo json_encode($aplicacoes);
            }
        else {
            $result = $conn->query("SELECT * FROM aplicacao");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

        
    case 'POST':
        $id_carona = $input['id_carona'];
        $id_passageiro = $input['id_passageiro'];

        $queryInicial = "select * from aplicacao where id_passageiro = $id_passageiro and id_carona = $id_carona";
        $result = $conn->query($queryInicial);
        if ($result->num_rows > 0){
            echo json_encode(["message" => "ja aplicou"]);
            break;
        }

        $queryVagas = "select c.id,c.vagas,a.id_carona from carona c
left join aplicacao a on c.id = a.id having c.id=$id_carona";
        $result = $conn->query($queryVagas);
        $carona = $result->fetch_assoc();
        $vagas = $carona['vagas'];
        if ($vagas <= 0){
            echo json_encode(["message" => "carona cheia"]);
            break;
        }
        $mensagem = $input['mensagem'];
        $status = "pendente";
        if ($conn->query("INSERT INTO aplicacao(id_passageiro,id_carona,status,data_aplicacao,mensagem) VALUES ('$id_passageiro', '$id_carona', '$status', NOW(), '$mensagem')")){
            echo json_encode(["message" => "aplicacao ok"]);
        }
        else{
        echo json_encode(["error" => $conn->error]);

        }
        break;

    case 'PUT':  
        $id = $input['id'];      
        $status = $input['status'];
        if ($id) {
        $conn->query("UPDATE aplicacao SET status = '$status' WHERE id=$id");
        echo json_encode(["message" => "aplicacao atualizada"]);
        } 
        else {
        echo json_encode(["error" => "aplicacao não atualizada"]);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("DELETE FROM aplicacao WHERE id=$id");
        echo json_encode(["message" => "del sucesso"]);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

$conn->close();






        // if (isset($_GET['id'])) {
        //     $id = $_GET['id'];
        //     $result = $conn->query("SELECT * FROM aplicacao WHERE id=$id");
        //     $data = $result->fetch_assoc();
        //     echo json_encode($data);
        // }
?>