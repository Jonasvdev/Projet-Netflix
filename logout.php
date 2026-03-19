<?php
session_start(); // initialisation de la session 
session_unset(); // desativer la session
session_destroy();  // detruire la session 
setcookie("auth",'', time()-1); // Detruire les cookies 

header("locatuion : index.php");



?>