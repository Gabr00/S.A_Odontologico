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

// Obter o id_servico da URL
$id_servico = isset($_GET['id_servico']) ? $_GET['id_servico'] : 0;

// Função para verificar os horários disponíveis
function getAvailableTimes($conn, $id_servico) {
    $times = [];
    $startTimes = [
        "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", // Manhã
        "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30" // Tarde
    ];

    // Verificar os horários já ocupados
    $sql = "SELECT horario FROM agendamentos WHERE id_servico = ? AND DATE(horario) = CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_servico);
    $stmt->execute();
    $result = $stmt->get_result();

    $bookedTimes = [];
    while ($row = $result->fetch_assoc()) {
        $bookedTimes[] = date("H:i", strtotime($row['horario']));
    }

    // Remover horários ocupados da lista
    foreach ($startTimes as $time) {
        if (!in_array($time, $bookedTimes)) {
            $times[] = $time;
        }
    }

    return $times;
}

// Se o formulário for enviado, realizar o agendamento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['horario'])) {
    $horario = $_POST['horario'];
    
    // Verifica se o horário já foi agendado
    $sql = "SELECT * FROM agendamentos WHERE horario = ? AND id_servico = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $horario, $id_servico);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Este horário já foi agendado. Por favor, escolha outro horário.";
    } else {
        // Realizar o agendamento
        $sql = "INSERT INTO agendamentos (id_servico, horario) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $id_servico, $horario);
        $stmt->execute();
        $message = "Agendamento realizado com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Serviço</title>
    <link rel="stylesheet" href="css/agendamento.css">
</head>
<body>
    <header>
        <h1>Agendar Serviço</h1>
    </header>

    <section>
        <h2>Selecione o horário</h2>
        <form method="POST" action="">
            <label for="horario">Horário:</label>
            <select name="horario" id="horario" required>
                <?php
                // Exibir os horários disponíveis
                $availableTimes = getAvailableTimes($conn, $id_servico);
                foreach ($availableTimes as $time) {
                    echo "<option value='$time'>$time</option>";
                }
                ?>
            </select>
            <button type="submit">Agendar</button>
        </form>

        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    </section>

    <footer>
        <a href="index.html">Voltar</a>
    </footer>
</body>
</html>

<?php
// Fechar a conexão
$conn->close();
?>