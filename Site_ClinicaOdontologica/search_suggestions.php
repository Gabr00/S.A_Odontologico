<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clinica_odontologica";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = isset($_GET['q']) ? $_GET['q'] : '';

// Se a consulta não for vazia, buscamos as sugestões
if (!empty($query)) {
    $sql = "SELECT id_servico, nome FROM servicos WHERE nome LIKE ? LIMIT 5";
    $stmt = $conn->prepare($sql);
    $searchQuery = "%$query%";
    $stmt->bind_param("s", $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = [];
    
    // Buscar os resultados e retornar como um array
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row; // Adiciona tanto id_servico quanto nome
    }

    // Retornar as sugestões como JSON
    echo json_encode($suggestions);
} else {
    // Se a consulta estiver vazia, retornar um array vazio
    echo json_encode([]);
}

// Fechar a conexão
$conn->close();
?>