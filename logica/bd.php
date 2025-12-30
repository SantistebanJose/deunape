<?php
//para cuando lo subo a hosting

//$server = "localhost"; 
//$bd = "sistema_libreria_rodri";
//$user = "postgres";
//$pass = "76008509";
//$port = "5432";


$server = "aws-1-us-east-1.pooler.supabase.com";
$bd = "postgres";
$user = "postgres.jsrtcyygjhxnrtgbmwrp";
$pass = "LqBG4VVUrK6_jcy";
$port = "5432";


try {
    // Incluye el puerto en el DSN
    $conectar = new PDO("pgsql:host=$server;port=$port;dbname=$bd", $user, $pass);
    $conectar->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


} catch (PDOException $e) {
    // Muestra el mensaje de error en caso de fallo
    echo "Error de conexión: " . $e->getMessage();
}


//