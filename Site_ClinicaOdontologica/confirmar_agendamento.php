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

// Verificar se os dados necessários foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_servico'], $_POST['data_agendamento'], $_POST['horario'])) {
    $id_servico = $_POST['id_servico'];
    $data_agendamento = $_POST['data_agendamento'];
    $horario = $_POST['horario'];

    // Verificar se o horário já está ocupado
    $sql = "SELECT * FROM agendamentos WHERE data_agendamento = ? AND horario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $data_agendamento, $horario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Se o horário já foi agendado
        echo "Desculpe, esse horário já foi agendado. Por favor, escolha outro horário.";
    } else {
        // Se o horário está disponível, fazer o agendamento
        $sql = "INSERT INTO agendamentos (id_servico, data_agendamento, horario) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $id_servico, $data_agendamento, $horario);

        if ($stmt->execute()) {
            // Sucesso
            echo "<h2>Agendamento confirmado!</h2>";
            echo "<p>Seu agendamento foi realizado para o serviço no dia " . date('d/m/Y', strtotime($data_agendamento)) . " às $horario.</p>";
            echo "<a href='Servicos.html'>Voltar para os serviços</a>";
        } else {
            // Caso haja erro
            echo "Erro ao agendar o serviço. Tente novamente.";
        }
    }

    // Fechar o statement
    $stmt->close();
} else {
    echo "Dados de agendamento inválidos.";
}

// Fechar a conexão
$conn->close();
?>