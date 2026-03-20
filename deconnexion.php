<?php
    session_start();
    if (isset($_POST["OUT"])){
        session_destroy();
        header("Location: Portail_Connexion.php");
        exit();
    }
    ?>