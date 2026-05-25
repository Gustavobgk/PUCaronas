<?php
include '../../config/db.php';

$result = $conn->query("SELECT id, senha_hash FROM usuario");
while ($row = $result->fetch_assoc()) {
    $novo_hash = password_hash($row['senha_hash'], PASSWORD_DEFAULT);
    $conn->query("UPDATE usuario SET senha_hash='$novo_hash' WHERE id={$row['id']}");
}
echo "Feito!";