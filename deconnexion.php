<?php
session_start();
session_destroy();
header('Location: /bibliotheque/index.php');
exit;
?>
