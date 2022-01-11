<?php
    include "databases.php";
    if (!isset($_SESSION["ID"])) {
        header('Location: ../index.php');
        exit();
    }
    
    if (isset($_GET['IDEVENT'])) {
        echo $_GET['IDEVENT'];
        $request = $pdo->prepare("DELETE FROM events WHERE EVENUM = ?");
        $request->execute(array($_GET['IDEVENT']));
        header('Location: ../index.php');
        exit();
    }
?>
