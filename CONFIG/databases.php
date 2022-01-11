<?php
    session_start();
    $bdd = "mysql:host=localhost;dbname=calendar";
    $pdo = new PDO($bdd, "root", "");
?>