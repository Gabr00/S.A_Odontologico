<!-- ARQUIVO RESPONSAVEL POR REALIZAR A CONEXAO COM O BANCO DE DADOS -->
<?php
$nomeservidor = "localhost";
$usuario = "root";
$senha = "";
$bancodedados = "clinica_odontologica";

// O CODIGO ABAIXO CRIA A CONEXAO 
$conn = mysqli_connect($nomeservidor, $usuario, $senha, $bancodedados);

// O CODIGO ABAIXO FAZ VERIFICAÇÃO DE CONEXAO COM O B.D
// RETIRE O COMENTARIO DO CODIGO ABAIXO PARA TESTAR A CONEXAO
if(!$conn){
    die ("Conexão Falhou!" . mysqli_connect_error());
} else {

    echo "Conectado com sucesso!" . "<br/>" ;
    die("Erro na consulta SQL: " . $conn->error); // Mostrar erro de SQL
    
}


?>