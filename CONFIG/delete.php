<?php
    include "databases.php";

    if (isset($_SESSION["ID"])) {
        $consumer = $_SESSION['ID'];
        $request_delete = $pdo->prepare('DELETE FROM consumer WHERE CONSNUM = ?');
        $request_delete->execute(array($consumer));
        session_destroy();
        header('Location: CONFIG/logout.php');
        exit();
    } else {
        header('Location: ../index.php');
        exit();
    }
?>
