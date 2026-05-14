<?php
include '../../config/db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id_motorista']) && isset($_GET['id_passageiro'])) {
            $id_passageiro = $_GET['id_passageiro'];
            $id_motorista = $_GET['id_motorista'];
            $result = $conn->query("SELECT * from avaliacao where id_motorista = $id_motorista AND id_passageiro = $id_passageiro");
            $aplicacoes = [];
            while ($row = $result->fetch_assoc()) {
                $aplicacoes[] = $row;
            }
            echo json_encode($aplicacoes);
            }   
                 else if (isset($_GET['id_carona'])){
            $id_carona = $_GET['id_carona'];
            $tipo = $_GET['tipo'];
            $id_motorista = $_GET['id_motorista'];
           $result = $conn->query("
    SELECT DISTINCT co.id_passageiro, u.nome
    FROM corrida co
    INNER JOIN usuario u ON u.id = co.id_passageiro
    WHERE co.id_carona = $id_carona
    AND co.id_motorista = $id_motorista
    AND NOT EXISTS (
        SELECT 1 FROM avaliacao a
        WHERE a.id_passageiro = co.id_passageiro
        AND a.id_motorista = $id_motorista
        AND a.tipo = '$tipo'
    )
");
    $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
         }

        
        else if (isset($_GET['id_motorista'])) {
            $id_motorista = $_GET['id_motorista'];
            $result = $conn->query("SELECT * from avaliacao where id_motorista = $id_motorista");
            $aplicacoes = [];
            while ($row = $result->fetch_assoc()) {
                $aplicacoes[] = $row;
            }
            echo json_encode($aplicacoes);
            }
            
         else  if (isset($_GET['id_passageiro'])){
            $id_passageiro = $_GET['id_passageiro'];

            $result = $conn->query("SELECT * from avaliacao where id_passageiro = $id_passageiro");
            $aplicacoes = [];
            while ($row = $result->fetch_assoc()) {
                $aplicacoes[] = $row;
            }
            echo json_encode($aplicacoes);
            
        
         }
                 else {
            $result = $conn->query("SELECT * FROM avaliacao");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
//           por corrida da carona: use pucaronas;
// select co.id_passageiro,co.id_motorista,ca.id_motorista,ca.id from corrida co
// inner join carona ca on co.id_motorista = ca.id_motorista;

        break;

        
    case 'POST':
        
    $id_passageiro   = $_GET['id_passageiro'];
    $id_motorista = $_GET['id_motorista'];
    $tipo      = $input['tipo'];
    $nota      = $input['nota'];

        $result = $conn->query("INSERT INTO avaliacao(id_passageiro,id_motorista,tipo,nota) VALUES ('$id_passageiro', '$id_motorista', '$tipo', '$nota')");
                    echo json_encode(["message" => "avaliacao enviada"]);
                    $aplicacoes = [];
                   
            echo json_encode($aplicacoes);
        
        break;

    case 'PUT':  
        $id = $_GET['id_corrida']; 
        $id_carona = $_GET['id_carona'];        
        $status = $input['status'];
        if ($id) {
        $conn->query("UPDATE corrida SET status = '$status' , data_revisao = NOW() WHERE id=$id AND id_carona=$id_carona");
        echo json_encode(["message" => "aplicacao atualizada"]);
        } 
        else {
        echo json_encode(["error" => "aplicacao não atualizada"]);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("DELETE FROM avaliacao WHERE id=$id");
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