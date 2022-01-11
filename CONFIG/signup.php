<?php 
    include "databases.php";

    if (isset($_SESSION['ID'])) {
        header('Location: ../index.php?id='.$_SESSION["id"]);
        exit();
    }

    // Récupération des données du formulaire pour l'inscription sur CALENDAR
    if (isset($_POST["signup"])) {
        if (!empty($_POST["fam"]) && !empty($_POST["name"]) && !empty($_POST["mail"]) && !empty($_POST["id"]) && !empty($_POST["pass"])) {
            if (filter_var(htmlspecialchars($_POST["mail"]), FILTER_VALIDATE_EMAIL)) {
                $fam = htmlspecialchars($_POST["fam"]);
                $name = htmlspecialchars($_POST["name"]);
                $country = htmlspecialchars($_POST["country"]);
                $email = htmlspecialchars($_POST["mail"]);
                $pseudo = htmlspecialchars($_POST["id"]);
                $password = sha1($_POST["pass"]);

                // CE COMPTE EXISTE IL DEJA ? soit par le pseudo soit par l'adresse email
                $req = $pdo->prepare("SELECT * FROM consumer WHERE CONSPSEUDO = ? OR CONSEMAIL = ?");
                $req->execute(array($pseudo, $email));
                $existing = $req->rowCount();

                if ($existing == 0) {
                    // INSERTION
                    $query = $pdo->prepare("INSERT INTO consumer (CONSNAME, CONSFAM, CONSEMAIL, CONSPSEUDO, CONSPASSWORD, CONSCOUNTRY) VALUES (?, ?, ?, ?, ?, ?)");
                    $query->execute(array($name, $fam, $email, $pseudo, $password, $country));

                    // RECHERCHE 
                    $req = $pdo->prepare("SELECT * FROM consumer WHERE CONSPSEUDO = ? AND CONSPASSWORD = ?");
                    $req->execute(array($pseudo, $password));
                    $existing = $req->rowCount();
                    
                    $datas = $req->fetch();
                    $request = $pdo->prepare("INSERT INTO parameter (PARANUM) VALUES (?)");
                    $request->execute(array($datas["CONSNUM"]));

                    // VERIFICATION DE L'INSERTION ET CONNEXION
                    if ($existing == 1) {
                        $_SESSION["ID"] = $datas["CONSNUM"];
                        $_SESSION["NAME"] = $datas["CONSNAME"];
                        $_SESSION["FAMILY"] = $datas["CONSFAM"];
                        $_SESSION["EMAIL"] = $datas["CONSEMAIL"];
                        $_SESSION["PSEUDO"] = $datas["CONSPSEUDO"];
                        $_SESSION["PASSWORD"] = $datas["CONSPASSWORD"];
                        $_SESSION["COUNTRY"] = $datas["CONSCOUNTRY"];
                        header('Location: ../index.php?id='.$_SESSION["id"]);
                        exit();
                    }
                } else {
                    $error_message = "Ce compte existe déjà";
                }
                
            } else {
                $error_message = "L'adresse mail incorrecte";
            }
        } else {
            $error_message = "Minimum une des saisie est vide";
        }
    } 
?>

<!DOCTYPE html>
<!-- Page d'inscription -->
<html lang="fr">

    <!-- Entete de la page -->

    <head>
        <meta charset="utf-8" />
        <title> Calendar - Inscription </title>
        <link rel="icon" type="image/png" sizes="20x20" href="../ASSETS/logo.png">
        <link rel="stylesheet" href="../CSS/forms.css" />
    </head>

    <!-- Corps de la page -->

    <body>
        <div id="outer">
            <div id="register" class="form">
                <form method="POST" action="signup.php">
                    <p>S'inscrire</p>
                    <p> Nom de famille </p>
                    <input type="text" name="fam" required/>
                    <p> Prénom </p>
                    <input type="text" name="name" required/>
                    <!-- Ajout de la sélection de la ville -->
                    <p> Ville </p>
                    <select id="selection" name="country" required>
                        <option disabled selected value=""> -- Sélectionner une ville -- </option>   
                        <option value="Paris"> Paris </option>
                        <option value="Lille"> Lille </option>
                        <option value="Rouen"> Rouen </option>
                        <option value="Amiens"> Amiens </option>
                        <option value="Caen"> Caen </option>
                        <option value="Nice"> Nice </option>
                        <option value="Marseille"> Marseille </option>
                        <option value="Narbonne"> Narbonne </option>
                        <option value="Toulouse"> Toulouse </option>
                        <option value="Bordeau"> Bordeau </option>
                        <option value="Vendée"> Vendée </option>
                        <option value="Lyon"> Lyon </option>
                        <option value="Grenoble"> Grenoble </option>
                        <option value="Strasbourg"> Strasbourg </option>
                        <option value="Nantes"> Nantes </option>
                        <option value="Montpellier"> Montpellier </option>
                        <option value="Rennes"> Rennes </option>
                        <option value="Reims"> Reims </option>
                        <option value="Toulon"> Toulon </option>
                        <option value="Angers"> Angers </option>
                        <option value="Dijon"> Dijon </option>
                        <option value="Brest"> Brest </option>
                        <option value="Tours"> Tours </option>
                        <option value="Limoges"> Limoges </option>
                        <option value="Perpignan"> Perpignan </option>
                        <option value="Nancy"> Nancy </option>
                        <option value="Nanterre"> Nanterre </option>
                        <option value="Avignon"> Avignon </option>
                        <option value="Louviers"> Louviers </option>
                        <option value="Yvetot">Yvetot</option>
                    </select>
                    <p> Adresse mail <p>
                    <input type="text" name="mail" required/>
                    <p> Identifiant </p>
                    <input type="text" name="id" required/>
                    <p> Mot de passe </p>
                    <input type="password" name="pass" autocomplete="off" required/>
                    <input type="submit" value="S'inscrire" name="signup" required/>
                    <p> Vous avez déjà un compte ? <a href="../index.php">Connectez vous</a></p>
                    <p id="error">
                        <?php
                            if (isset($error_message)) {
                                echo $error_message;
                            }
                        ?>
                    </p>
                </form>
            </div>
        </div>
    </body>
</html>
