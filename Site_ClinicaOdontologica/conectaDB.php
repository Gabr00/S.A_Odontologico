<?php
mysqli_report(MYSQLI_REPORT_ALL^MYSQLI_REPORT_INDEX);
function conectadb() {
    $endereco = "localhost";
    $usuario = "root";
    $senha = "";
    $bancodedados = "clinica_odontologica";
    try{
        $con = new mysqli($endereco, $usuario, $senha, $bancodedados);   
        return $con;
    }
    catch(mysqli_sql_exception $e) {
        echo "Erro na conexão" . $e->getMessage();
        exit();
    }
}
// Exemplo de uso
$conexao = conectadb();
//  .... seu codigo ....
$conexao->close();


?>
