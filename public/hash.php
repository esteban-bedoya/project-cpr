<?php 
$contraseñaSegura = password_hash('123456789', PASSWORD_BCRYPT);
echo 'contraseña: ' . $contraseñaSegura;
?>