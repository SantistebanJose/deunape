<?php
$ip     = '192.168.1.X'; // la IP que apareció en el scan
$puerto = 9100;
$s = @fsockopen($ip, $puerto, $errno, $errstr, 3);
echo $s ? "CONECTADO OK" : "Error {$errno}: {$errstr}";
if ($s) fclose($s);