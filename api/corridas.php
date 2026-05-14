<?php
include '../../config/db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
           if (isset($_GET['id_motorista'])) {
            $id_motorista = $_GET['id_motorista'];
$result = $conn->query("SELECT 
    co.id,
    co.origem,
    co.destino,
    co.status,
    co.data_inicio,
    co.data_fim,
    c.titulo,
    c.id as id_carona,
    GROUP_CONCAT(u.nome SEPARATOR ', ') AS passageiros
FROM corrida co
INNER JOIN carona c ON c.id = co.id_carona
INNER JOIN usuario u ON u.id = co.id_passageiro
WHERE co.id_motorista = $id_motorista
GROUP BY c.id
");
            $aplicacoes = [];
            while ($row = $result->fetch_assoc()) {
                $aplicacoes[] = $row;
            }
            echo json_encode($aplicacoes);
            }

         else  if (isset($_GET['id_passageiro'])){
            $id_passageiro = $_GET['id_passageiro'];

            $result = $conn->query("SELECT 
    co.id,
    co.origem,
    co.destino,
    co.status,
    co.data_inicio,
    co.data_fim,
    c.titulo,
    GROUP_CONCAT(u.nome SEPARATOR ', ') AS passageiros
FROM corrida co
INNER JOIN carona c ON c.id = co.id_carona
INNER JOIN usuario u ON u.id = co.id_passageiro
WHERE co.id_passageiro = $id_passageiro
GROUP BY c.id");
 $aplicacoes = [];
            while ($row = $result->fetch_assoc()) {
                $aplicacoes[] = $row;
            }
            echo json_encode($aplicacoes);
            
        
         }
        else {
            $result = $conn->query("SELECT * FROM corridas");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

        
    case 'POST':
        
    $id_carona   = $_GET['id_carona'];
    $id_motorista = $_GET['id_motorista'];
    $status      = $input['status'];

    if ($status === 'em_andamento') {
        $conn->query("UPDATE corrida 
                      SET status='em_andamento', data_inicio=NOW()
                      WHERE id_carona=$id_carona 
                      AND id_motorista=$id_motorista
                      AND status='pendente'");
                    echo json_encode(["message" => "corrida atualizada"]);

    } else if ($status === 'finalizada') {
        $conn->query("UPDATE corrida 
                      SET status='finalizada', data_fim=NOW()
                      WHERE id_carona=$id_carona 
                      AND id_motorista=$id_motorista
                      AND status='em_andamento'");
    echo json_encode(["message" => "corrida atualizada"]);
    } else if ($status === 'cancelada') {
        $conn->query("UPDATE corrida 
                      SET status='cancelada'
                      WHERE id_carona=$id_carona 
                      AND id_motorista=$id_motorista
                      AND status IN ('pendente','em_andamento')");
                    echo json_encode(["message" => "corrida atualizada"]);
    }
    
    
        else{
        echo json_encode(["error" => $conn->error]);

        }
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
        $id = $_GET['id_corrida'];
        $conn->query("CALL deletar_carona($id)");
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