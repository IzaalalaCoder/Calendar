<?php
    include "databases.php";
    if (!isset($_SESSION["ID"])) {
        header('Location: ../index.php');
        exit();
    }
// vérification date logique
    if (isset($_GET['IDEVENT'])) {
        // echo $_GET['IDEVENT'];
        $request = $pdo->prepare("SELECT * FROM events WHERE EVENUM = ?");
        $request->execute(array($_GET["IDEVENT"]));
        $exists = $request->rowCount();
        if ($exists == 1) {
            $datas = $request->fetch();

            if (isset($_POST['Modifier'])) {
                // MODIFICATION DU TITRE
                if (isset($_POST['title']) && !empty($_POST['title']) && ($_POST['title'] != $datas['EVETITLE'])) {
                    $insert = $pdo->prepare('UPDATE events SET EVETITLE = ? WHERE EVENUM = ?');
                    $insert->execute(array(htmlspecialchars($_POST['title']), $_GET['IDEVENT']));
                    header("Location: ../index.php?id=".$_SESSION['ID']);
                }

                // MODIFICATION DE LA DESCRIPTION
                if (isset($_POST['description']) && !empty($_POST['description']) && ($_POST['description'] != $datas['EVEDESC'])) {
                    $insert = $pdo->prepare('UPDATE events SET EVEDESC = ? WHERE EVENUM = ?');
                    $insert->execute(array(htmlspecialchars($_POST['description']), $_GET['IDEVENT']));
                    header("Location: ../index.php?id=".$_SESSION['ID']);
                }

                // MODIFICATION DU LIEU
                if (isset($_POST['location']) && !empty($_POST['location']) && ($_POST['location'] != $datas['EVELOC'])) {
                    $insert = $pdo->prepare('UPDATE events SET EVELOC = ? WHERE EVENUM = ?');
                    $insert->execute(array(htmlspecialchars($_POST['location']), $_GET['IDEVENT']));
                    header("Location: ../index.php?id=".$_SESSION['ID']);
                }

                // MODIFICATION DE LA DATE DU DEBUT DE L'EVENEMENT
                if (isset($_POST['start']) && !empty($_POST['start']) && ($_POST['start'] != $datas['EVESTART'])) {
                    $insert = $pdo->prepare('UPDATE events SET EVESTART = ? WHERE EVENUM = ?');
                    $insert->execute(array(htmlspecialchars($_POST['start']), $_GET['IDEVENT']));
                    header("Location: ../index.php?id=".$_SESSION['ID']);
                }

                // MODIFICATION DE L'HEURE DU DEBUT DE L'EVENEMENT
                if (isset($_POST['hourstart']) && !empty($_POST['hourstart']) && ($_POST['hourstart'] !=  $datas['EVEHOURSTART'])) {
                    $insert = $pdo->prepare('UPDATE events SET EVEHOURSTART = ? WHERE EVENUM = ?');
                    $insert->execute(array(htmlspecialchars($_POST['hourstart']), $_GET['IDEVENT']));
                    header("Location: ../index.php?id=".$_SESSION['ID']);
                }

                // MODIFICATION DE LA DATE DE FIN DE L'EVENEMENT
                if (isset($_POST['end']) && !empty($_POST['end']) && ($_POST['end'] != $datas['EVEEND'])) {
                    $insert = $pdo->prepare('UPDATE events SET EVEEND = ? WHERE EVENUM = ?');
                    $insert->execute(array(htmlspecialchars($_POST['end']), $_GET['IDEVENT']));
                    header("Location: ../index.php?id=".$_SESSION['ID']);
                }
                
                // MODIFICATION DE L'HEURE DE FIN DE L'EVENEMENT
                if (isset($_POST['hourend']) && !empty($_POST['hourend']) && ($_POST['hourend'] !=  $datas['EVEHOUREND'])) {
                    $insert = $pdo->prepare('UPDATE events SET EVEHOUREND = ? WHERE EVENUM = ?');
                    $insert->execute(array(htmlspecialchars($_POST['hourend']), $_GET['IDEVENT']));
                    header("Location: ../index.php?id=".$_SESSION['ID']);
                }
            } 
?>
<html>
    <head>
        <title>Modifier <?php echo $datas['EVETITLE']; ?></title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" href="../CSS/forms.css" />
        <link rel="icon" type="image/png" sizes="20x20" href="../ASSETS/logo.png">
    </head>
    <body>
        <div id="outer" class="form modified">
            <h2>Modifier l'évènement <?php echo $datas['EVETITLE']; ?> </h2>
            <form action="<?php echo 'modified.php?IDEVENT='.$_GET['IDEVENT']; ?>" method="POST">
                <p>Titre : </p>
                <input type="text" name="title" value="<?php echo $datas['EVETITLE'];?>"/> </br>
                <p>Description : </p>
                <input type="text" name="description" value="<?php if ($datas['EVEDESC'] == "INCONNU") { echo ""; } else { echo $datas['EVEDESC']; }?>"/> </br>
                <p>Lieu : </p>
                <input type="text" name="location" value="<?php if ($datas['EVELOC'] == "INCONNU") { echo ""; } else { echo $datas['EVELOC']; }?>"/> </br>
                <p>Du</p>
                <input type="date" name="start" value="<?php echo $datas['EVESTART'];?>"/>
                <p>à</p>
                <input type="time" name="hourstart" value="<?php echo  $datas['EVEHOURSTART'];?>"/> </br>
                <p>Au</p>
                <input type="date" name="end" value="<?php echo $datas['EVEEND'];?>"/>
                <p>à</p>
                <input type="time" name="hourend" value="<?php echo  $datas['EVEHOUREND'];?>"/> </br>
                <input type="submit" name="Modifier" value="Modifier l'évènement"/>
            </form>
            <a href="../index.php">Retour au calendrier</a>
        </div>
    </body>
</html>
<?php
        }
    }
?>
