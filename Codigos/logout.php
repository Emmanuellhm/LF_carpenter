<?php
session_start();
session_destroy();
header("Location: iniciar-seccion.html");
exit;
?>
