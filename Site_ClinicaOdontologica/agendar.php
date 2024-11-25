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

// Recuperar o ID do serviço da URL
$id_servico = isset($_GET['id_servico']) ? $_GET['id_servico'] : null;

if ($id_servico) {
    // Consultar o nome do serviço no banco de dados
    $sql = "SELECT nome FROM servicos WHERE id_servico = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_servico);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nome_servico = $row['nome'];
    } else {
        $nome_servico = "Serviço não encontrado";
    }
} else {
    $nome_servico = "Serviço não especificado";
}

$conn->close();

// Verificar se a data foi enviada pelo formulário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['data'])) {
    $data_escolhida = $_POST['data'];

    // Verificar se a data não é um fim de semana
    $dia_da_semana = date('N', strtotime($data_escolhida)); // 1 = segunda-feira, 7 = domingo
    if ($dia_da_semana == 6 || $dia_da_semana == 7) {
        echo "Por favor, escolha um dia útil (segunda a sexta-feira).";
        exit;
    }

    // Consultar horários ocupados no mesmo dia
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT horario FROM agendamentos WHERE data_agendamento = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $data_escolhida);
    $stmt->execute();
    $result = $stmt->get_result();

    $horarios_ocupados = [];
    while ($row = $result->fetch_assoc()) {
        $horarios_ocupados[] = $row['horario'];
    }

    // Definir os horários disponíveis
    $horarios_disponiveis = [];
    $horarios = [
        '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
        '11:00', '11:30', '13:00', '13:30', '14:00', '14:30',
        '15:00', '15:30', '16:00', '16:30', '17:00', '17:30'
    ];

    // Remover horários ocupados apenas para o mesmo dia
    foreach ($horarios as $hora) {
        if (!in_array($hora, $horarios_ocupados)) {
            $horarios_disponiveis[] = $hora;
        }
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Serviço</title>
    <link rel="stylesheet" href="css/agendar.css">
</head>
<body>
<header>
        <h1>Transforme seu sorriso</h1>
        <nav>
            <a href="index.html" class="active">Home</a>
            <a href="Servicos.php">Serviços</a>
            <a href="SobreNos.html">Quem somos</a>
            <a href="Contato.html">Contato</a>
        </nav>
    </header>
<h1>Agendar Serviço</h1>


<!-- Exibir o nome do serviço que está sendo agendado -->
<p>Você está agendando o serviço: <strong><?php echo htmlspecialchars($nome_servico); ?></strong></p>

<!-- Formulário de Escolha de Data -->
<form method="POST" action="">
    <label for="data">Escolha a data para o agendamento (somente dias úteis):</label><br>
    <input type="date" id="data" name="data" min="<?php echo date('Y-m-d'); ?>" required>
    <button type="submit">Verificar horários disponíveis</button>
</form>

<?php
// Se a data for válida e o formulário for enviado
if (isset($data_escolhida) && !empty($horarios_disponiveis)) {
    echo "<h2>Horários disponíveis para o dia " . date('d/m/Y', strtotime($data_escolhida)) . ":</h2>";
    
    // Exibir os horários disponíveis
    echo "<form method='POST' action='confirmar_agendamento.php'>";
    echo "<input type='hidden' name='id_servico' value='$id_servico'>";
    echo "<input type='hidden' name='data_agendamento' value='$data_escolhida'>";

    foreach ($horarios_disponiveis as $horario) {
        echo "<input type='radio' name='horario' value='$horario' id='$horario' required>";
        echo "<label for='$horario'>$horario</label><br>";
    }

    echo "<br><button type='submit'>Confirmar Agendamento</button>";
    echo "</form>";
}
?>



</body>
</html>