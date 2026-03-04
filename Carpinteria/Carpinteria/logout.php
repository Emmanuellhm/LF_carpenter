<?php
session_start();
session_unset();
session_destroy();

header("Location: iniciar-seccion.php");
exit;
?>
