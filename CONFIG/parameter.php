<!-- 
    Changement de paramètre
        - Utilisateur
        - Couleurs associé au calendrier
    Si l'utilisateur le souhaite 
        - Supprimer son compte
 -->
<?php
    include "databases.php";

    if (!isset($_SESSION["ID"])) {
        header("Location: ../index.php");
        exit(); 
    } else {
    
        // LES REQUETES DE CHAQUE DONNEE QUE L'ON VEUT MODIFIER DANS NOS PARAMETRES
        $request_consumer = $pdo->prepare("SELECT * FROM consumer WHERE CONSNUM = ?");
        $request_consumer->execute(array($_SESSION['ID']));
        $exists_consumer = $request_consumer->rowCount();
        $consumer = $request_consumer->fetch();

        $request_parameter = $pdo->prepare("SELECT * FROM parameter WHERE PARANUM = ?");
        $request_parameter->execute(array($_SESSION["ID"]));
        $exists_parameter = $request_parameter->rowCount();
        $parameter = $request_parameter->fetch();

        if (isset($_POST["Valider"])) {
            // PARTIE PARAMETRE DU MEMBRE

            // Le prénom du membre
            if (isset($_POST['name']) && !empty($_POST['name']) && ($_POST['name'] != $consumer['CONSNAME'])) {
                $name = htmlspecialchars($_POST['name']);
                $insert = $pdo->prepare('UPDATE consumer SET CONSNAME = ? WHERE CONSNUM = ?');
                $insert->execute(array($name, $_SESSION['ID']));
                $_SESSION['NAME'] = $name;
                header("Location: ../index.php?id=".$_SESSION['ID']);
            }

            // Le nom de famille du membre 
            if (isset($_POST['family']) && !empty($_POST['family']) && ($_POST['family'] != $consumer['CONSFAM'])) {
                $family = htmlspecialchars($_POST['family']);
                $insert = $pdo->prepare('UPDATE consumer SET CONSFAM = ? WHERE CONSNUM = ?');
                $insert->execute(array($family, $_SESSION['ID']));
                $_SESSION['FAMILY'] = $family;
                header("Location: ../index.php?id=".$_SESSION['ID']);
            }

            // La ville du membre
            if (isset($_POST['country']) && !empty($_POST['country']) && $_POST['country'] != $consumer['CONSCOUNTRY']) {
                $country = htmlspecialchars($_POST["country"]);
                $insert = $pdo->prepare('UPDATE consumer SET CONSCOUNTRY = ? WHERE CONSNUM = ?');
                $insert->execute(array($country, $_SESSION['ID']));
                $_SESSION['COUNTRY'] = $country;
                header("Location: ../index.php?id=".$_SESSION['ID']);
            }

            // L'adresse email du membres
            if (isset($_POST['email']) && !empty($_POST['email']) && ($_POST['email'] != $consumer['CONSEMAIL'])) {
                $email = htmlspecialchars($_POST['email']);
                $if_exist = $pdo->prepare("SELECT * FROM consumer WHERE CONSEMAIL = ?");
                $if_exist->execute(array($email));
                if (filter_var($email, FILTER_VALIDATE_EMAIL) && $if_exist->rowCount() == 0) {
                    $insert = $pdo->prepare('UPDATE consumer SET CONSEMAIL = ? WHERE CONSNUM = ?');
                    $insert->execute(array($email, $_SESSION['ID']));
                    $_SESSION['EMAIL'] = $email;
                    header("Location: ../index.php?id=".$_SESSION['ID']);
                } else {
                    $error_message = "Votre adresse email n'est pas conforme";
                }
            }

            // Le pseudo du membre
            if (isset($_POST['pseudo']) && !empty($_POST['pseudo']) && ($_POST['pseudo'] != $consumer['CONSPSEUDO'])) {
                $pseudo = htmlspecialchars($_POST['pseudo']);
                $if_exist = $pdo->prepare("SELECT * FROM consumer WHERE CONSPSEUDO = ?");
                $if_exist->execute(array($pseudo));
                if ($if_exist->rowCount() == 0) {
                    $insert = $pdo->prepare('UPDATE consumer SET CONSPSEUDO = ? WHERE CONSNUM = ?');
                    $insert->execute(array($pseudo, $_SESSION['ID']));
                    $_SESSION['PSEUDO'] = $pseudo;
                    header("Location: ../index.php?id=".$_SESSION['ID']); 
                }
            }

            // Le mot de passe du membre
            if (isset($_POST['pass']) && isset($_POST['confirmpassword']) && !empty($_POST['pass']) && !empty($_POST['confirmpassword'])) {
                $pass = sha1($_POST['pass']);
                $confirmpass = sha1($_POST['confirmpassword']);
                if ($pass == $confirmpass) {
                    $insert = $pdo->prepare('UPDATE consumer SET CONSPASSWORD = ? WHERE CONSNUM = ?');
                    $insert->execute(array($pass, $_SESSION['ID']));
                    $_SESSION['PASSWORD'] = $pass;
                    header("Location: ../index.php?id=".$_SESSION['ID']);
                } else {
                    $error_message = "Les mots de passes doivent être identiques";
                }
            }

            // PARTIE PARAMETRE DU CALENDRIER

            // Le délai d'affichage
            if (isset($_POST['limits']) && !empty($_POST['limits']) && ($_POST['limits'] != $parameter['PARADAYEVE'])) {
                $limits = $_POST['limits'];
                if (is_numeric($limits)) {
                    $insert = $pdo->prepare('UPDATE parameter SET PARADAYEVE = ? WHERE PARANUM = ?');
                    $insert->execute(array(intval($limits), $_SESSION['ID']));
                    header("Location: ../index.php?id=".$_SESSION['ID']);
                } else {
                    $error_message = "Le délai d'affichage n'est pas dans le bon format (entre 0 et 99)";
                }
            }

            // La couleur pour samedi
            if (isset($_POST['saturday']) && !empty($_POST['saturday']) && ($_POST['saturday'] != $parameter['PARASAT'])) {
                $saturday = htmlspecialchars($_POST['saturday']);
                $insert = $pdo->prepare('UPDATE parameter SET PARASAT = ? WHERE PARANUM = ?');
                $insert->execute(array($saturday, $_SESSION['ID']));
                header("Location: ../index.php?id=".$_SESSION['ID']);
            }

            // La couleur pour dimanche
            if (isset($_POST['sunday']) && !empty($_POST['sunday']) && ($_POST['sunday'] != $parameter['PARASUN'])) {
                $sunday = htmlspecialchars($_POST['sunday']);
                $insert = $pdo->prepare('UPDATE parameter SET PARASUN = ? WHERE PARANUM = ?');
                $insert->execute(array($sunday, $_SESSION['ID']));
                header("Location: ../index.php?id=".$_SESSION['ID']);
            }

            // La couleur pour les jours fériés
            if (isset($_POST['close']) && !empty($_POST['close']) && ($_POST['close'] != $parameter['PARACLOSE'])) {
                $close = htmlspecialchars($_POST['close']);
                $insert = $pdo->prepare('UPDATE parameter SET PARACLOSE = ? WHERE PARANUM = ?');
                $insert->execute(array($close, $_SESSION['ID']));
                header("Location: ../index.php?id=".$_SESSION['ID']);
            }

            // La couleur pour ou un évènement se présente
            if (isset($_POST['even']) && !empty($_POST['even']) && ($_POST['even'] != $parameter['PARAEVEN'])) {
                $even = htmlspecialchars($_POST['even']);
                $insert = $pdo->prepare('UPDATE parameter SET PARAEVEN = ? WHERE PARANUM = ?');
                $insert->execute(array($even, $_SESSION['ID']));
                header("Location: ../index.php?id=".$_SESSION['ID']);
            }

            // La couleur pour de pont entre férié et week-end
            if (isset($_POST['deck']) && !empty($_POST['deck']) && ($_POST['deck'] != $parameter['PARADECK'])) {
                $deck = htmlspecialchars($_POST['deck']);
                $insert = $pdo->prepare('UPDATE parameter SET PARADECK = ? WHERE PARANUM = ?');
                $insert->execute(array($deck, $_SESSION['ID']));
                header("Location: ../index.php?id=".$_SESSION['ID']);
            }

            // La couleur pour jour cliqué
            if (isset($_POST['click']) && !empty($_POST['click']) && ($_POST['click'] != $parameter['PARACLICK'])) {
                $click = htmlspecialchars($_POST['click']);
                $insert = $pdo->prepare('UPDATE parameter SET PARACLICK = ? WHERE PARANUM = ?');
                $insert->execute(array($click, $_SESSION['ID']));
                header("Location: ../index.php?id=".$_SESSION['ID']);
            }
        }
?>
    
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8"/>
        <title>Paramètre de <?php echo $_SESSION["PSEUDO"]; ?></title>
        <link rel="icon" type="image/png" sizes="20x20" href="../ASSETS/logo.png">
        <link href="../CSS/forms.css" rel="stylesheet"/>
        <script>
            // Retourne un message et réagira en fonction de la réponse donnée
            function questionDelete() {
                var msg = "Etes-vous sur de vouloir supprimer votre compte ?";
                // Test du résultat de la fenêter de confirmation
                if (confirm(msg)) {
                    window.location.replace("delete.php");
                }
            }
        </script>
    </head>

    <body>
        <?php if ($exists_consumer == 1 && $exists_parameter == 1) { ?>
            <div id="outer" class="setting">
                <form action="parameter.php" method="POST" class="form"> 
                    <!-- Partie informations du membre -->
                    <h1>Mes informations confidentielles </h1>
                    <div>
                        <p>Prénom : </p>
                        <input type="text" value="<?php echo $consumer["CONSNAME"]; ?>" name="name" />
                        <p>Nom de famille : </p>
                        <input type="text" value="<?php echo $consumer["CONSFAM"]; ?>" name="family" />
                        <p> Ville : </p>
                        <select  name="country">
                            <option value="Paris" <?php if ("Paris" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Paris </option>
                            <option value="Lille" <?php if ("Lille" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Lille </option>
                            <option value="Rouen" <?php if ("Rouen" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Rouen </option>
                            <option value="Amiens" <?php if ("Amiens" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Amiens </option>
                            <option value="Caen" <?php if ("Caen" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Caen </option>
                            <option value="Nice" <?php if ("Nice" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Nice </option>
                            <option value="Marseille" <?php if ("Marseille" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Marseille </option>
                            <option value="Narbonne" <?php if ("Narbonne" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Narbonne </option>
                            <option value="Toulouse" <?php if ("Toulouse" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Toulouse </option>
                            <option value="Bordeaux" <?php if ("Bordeaux" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Bordeaux </option>
                            <option value="Vendée" <?php if ("Vendée" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Vendée </option>
                            <option value="Lyon" <?php if ("Lyon" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Lyon </option>
                            <option value="Grenoble" <?php if ("Grenoble" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Grenoble </option>
                            <option value="Strasbourg" <?php if ("Strasbourg" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Strasbourg </option>
                            <option value="Nantes" <?php if ("Nantes" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Nantes </option>
                            <option value="Montpellier" <?php if ("Montpellier" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Montpellier </option>
                            <option value="Rennes" <?php if ("Rennes" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Rennes </option>
                            <option value="Reims" <?php if ("Reims" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Reims </option>
                            <option value="Toulon" <?php if ("Toulon" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Toulon </option>
                            <option value="Angers" <?php if ("Angers" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Angers </option>
                            <option value="Dijon" <?php if ("Dijon" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Dijon </option>
                            <option value="Brest" <?php if ("Brest" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Brest </option>
                            <option value="Tours" <?php if ("Tours" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Tours </option>
                            <option value="Limoges" <?php if ("Limoges" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Limoges </option>
                            <option value="Perpignan" <?php if ("Perpignan" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Perpignan </option>
                            <option value="Nancy" <?php if ("Nancy" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Nancy </option>
                            <option value="Nanterre" <?php if ("Nanterre" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Nanterre </option>
                            <option value="Avignon" <?php if ("Avignon" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Avignon </option>
                            <option value="Louviers" <?php if ("Louviers" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>> Louviers </option>
                            <!-- ajout -->
                            <option value="Yvetot" <?php if ("Louviers" == $consumer["CONSCOUNTRY"]) {?> selected <?php } ?>>Yvetot</option>
                        </select>
                        <p>Adresse email : </p>
                        <input type="text" value="<?php echo $consumer["CONSEMAIL"]; ?>" name="email" />
                        <p>Pseudo : </p>
                        <input type="text" value="<?php echo $consumer["CONSPSEUDO"]; ?>" name="pseudo" />
                        <p>Mot de passe : </p>
                        <input type="password" name="pass" autocomplete="off" />
                        <p>Confirmer le mot de passe : </p>
                        <input type="password" name="confirmpassword" autocomplete="off" />
                    </div>

                    <!-- Partie paramètre visuel -->
                    <h1>Informations visuelles </h1>
                    <div>
                        <p>Limitation du nombre de jours : </p>
                        <input type="text" pattern="[0-9]{1,2}" name="limits" value="<?php echo $parameter["PARADAYEVE"];?>" />
                        <p>La couleur pour les Samedis : </p>
                        <input type="color" name="saturday" value="<?php echo $parameter["PARASAT"];?>" />
                        <p>La couleur pour les Dimanches : </p>
                        <input type="color" name="sunday" value="<?php echo $parameter["PARASUN"];?>" />
                        <p>La couleur pour les jours fériés : </p>
                        <input type="color" name="close" value="<?php echo $parameter["PARACLOSE"];?>" />
                        <p>La couleur pour les jours ou vous organisez des évènements : </p>
                        <input type="color" name="even" value="<?php echo $parameter["PARAEVEN"];?>" />
                        <p>La couleur pour les ponts entre les jours fériés et le premier jour du Week-End : </p>
                        <input type="color" name="deck" value="<?php echo $parameter["PARADECK"];?>" />
                        <p>La couleur pour les jours qui sont actives : </p>
                        <input type="color" name="click" value="<?php echo $parameter["PARACLICK"];?>" />
                    </div>   

                    <h1>Valider</h1>
                    <div>
                        <input type="submit" name="Valider" value="Modifier les informations" />
                        <p id="error">
                            <?php 
                            if (isset($error_message)) {
                                echo $error_message;
                            }
                            ?>
                        </p>
                        
                    </div>
                    
                    <h1>Autres</h1>
                    <div>
                        <button type="submit" onclick="questionDelete()">Supprimer le compte</button>
                        <a href="../index.php">Retour au calendrier</a>
                    </div>
                </form>              
            <?php } } ?>
        </div>
    </body>
</html>
