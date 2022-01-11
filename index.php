<script>
    var tabEvent = [];
</script>
<?php 
    include "CONFIG/databases.php";
    // Récupération des données pour la connexion sur le compte CALENDAR
    if (isset($_POST["login"])) {
        if (!empty($_POST["pseudo"]) && !empty($_POST["pass"])) {
            $id = htmlspecialchars($_POST["pseudo"]);
            $password = sha1($_POST["pass"]);
            
            // TEST DE CONNEXION
            $req = $pdo->prepare("SELECT * FROM consumer WHERE CONSPSEUDO = ? AND CONSPASSWORD = ?");
            $req->execute(array($id, $password));
            $existing = $req->rowCount();

            if ($existing == 1) {
                $datas = $req->fetch();
                $_SESSION["ID"] = $datas["CONSNUM"];
                $_SESSION["NAME"] = $datas["CONSNAME"];
                $_SESSION["FAMILY"] = $datas["CONSFAM"];
                $_SESSION["EMAIL"] = $datas["CONSEMAIL"];
                $_SESSION["PSEUDO"] = $datas["CONSPSEUDO"];
                $_SESSION["PASSWORD"] = $datas["CONSPASSWORD"];
                $_SESSION["COUNTRY"] = $datas["CONSCOUNTRY"];
                // $_SESSION["ORGANIZE"] = array();
                header('Location: index.php?ID='.$_SESSION['ID']);
                exit();
            } else {
                $error_message =  "Erreur de saisie";
            }
        } else {
            $error_message = "Aucune saisie";
        }
    }

    // Requetes des évènements
    if (isset($_SESSION['ID'])) {
        $event = array();
        $request = $pdo->query('SELECT DATE_FORMAT(EVESTART, "%d") AS JOUR,
                                DATE_FORMAT(EVESTART, "%m") AS MOIS,
                                DATE_FORMAT(EVESTART, "%Y") AS ANNEE,
                                DATE_FORMAT(EVEHOURSTART, "%H:%i") AS DEBUT,
                                DATE_FORMAT(EVEHOUREND, "%H:%i") AS FIN,
                                DATEDIFF(EVEEND, EVESTART) AS DIFF,
                                EVETITLE,
                                EVENUM
                                FROM events
                                WHERE EVECONS = '. $_SESSION['ID']);
        if ($request->rowCount() > 0) {
            while ($r = $request->fetch()) {
                $day = intval($r['JOUR']);
                $month = intval($r['MOIS']) - 1;
                $year = intval($r['ANNEE']);
                $start = $r['DEBUT'];
                $end = $r['FIN'];
                $delay = intval($r['DIFF']);
                $title = $r['EVETITLE'];
                $number = intval($r['EVENUM']);
                $temp = [$title, [$day, $month, $year], [$start, $end], $number, $delay];
                $event[] = $temp;
            }
        }
        // print_r($event);
        // echo var_dump($event);
?>
        <script>
            // console.log('les');
            tabEvent = <?php echo json_encode($event); ?>;
            // console.log("euu " + tabEvent);
        </script>
<?php
    }
?>


<!DOCTYPE html>
<!-- Page d'acceuil -->
<html lang="fr">
    <!-- Entete de la page -->
    <head>
        <meta charset="utf-8" />
        <title> Calendar - <?php if (!isset($_SESSION["ID"])){ echo "Se connecter"; } else { echo $_SESSION["PSEUDO"];}?></title>
        <link rel="icon" type="image/png" sizes="20x20" href="ASSETS/logo.png">
        <link rel="stylesheet" href="CSS/weather.css" />
        <link rel="stylesheet" href="CSS/calendar.css" />
        <link rel="stylesheet" href="CSS/forms.css" />
        <?php   
        if (isset($_SESSION["ID"])) { 
            $req = $pdo->prepare("SELECT * FROM parameter WHERE PARANUM = ?");
            $req->execute(array($_SESSION["ID"]));

            // Pas besoin de faire le test car si le client est déjà connecté
            // il existe forcément une ligne unique dans notre table parameter qui porte 
            // le même numéro que notre identifiant pour notre client
            $datas = $req->fetch();
        ?>
        <style>
            /* Le online */
            #daysInMonth.online div {
                cursor: pointer;
            }
            
            #daysInMonth.online .saturday {
                background-color: <?php echo $datas["PARASAT"]; ?>;
            }

            #daysInMonth.online .sunday {
                background-color: <?php echo $datas["PARASUN"]; ?>;
            }

            #daysInMonth.online .deck {
                background-color: <?php echo $datas["PARADECK"]; ?>;
            }

            #daysInMonth.online .close {
                background-color :  <?php echo $datas["PARACLOSE"]; ?>;
            }

            #daysInMonth.online .organize {
                text-decoration: underline;
                font-weight: bolder;
                background-color: <?php echo $datas["PARAEVEN"]; ?>;
            }

        </style>
        <script>
            var color = '<?php echo $datas["PARACLICK"]; ?>';
        </script>    
        <?php } ?>
    </head>

    <!-- Corps de la page -->
    <body>
        <script>
            var isOnline;
        </script>

        <?php           
        if (!isset($_SESSION['ID'])) {
        ?>
            <script>
                isOnline = false;
            </script>
            <div id="connexion" class="form">
                <!-- Formulaire de connexion -->
                <form method="POST" action="index.php">
                    <p>Connexion</p>
                    <p> Identifiant :</p>
                    <input type="text" name="pseudo" required />
                    <p> Mot de passe :</p>
                    <input type="password" name="pass" autocomplete="off" required/>
                    <input type="submit" value="Se connecter" name="login" required/>
                    <p> Vous n'avez pas de compte ? <a href="CONFIG/signup.php">Inscrivez-vous</a></p>
                    <p id="error">
                        <?php
                            if (isset($error_message)) {
                                echo $error_message;
                            }
                        ?>
                    </p>
                </form>
            </div>
        <?php
        } else {
        ?>
            <script>
                isOnline = true;
            </script>
            <div id="connected">
                <p>Vous êtes connecté en tant que <span id="pseudo"> <?php echo $_SESSION['PSEUDO']; ?></span>.</p>
                <!-- Les boutons d'envoi
                <form action="parameter.php" method="post">
                    <input type="submit" value="Préférences" />
                </form> -->
                <!-- <form action="#" method="post">
                    <input type="submit" value="Déconnexion" onclick="messageYesNo()"/>
                </form> -->
                <button type="submit" onclick="directPrefer()">Préférences</button>
                <button type="submit" onclick="messageYesNo()">Déconnexion</button>
            </div> 
        <?php } ?>

        <?php // Récupération des données de la ville V
            if (isset($_SESSION["COUNTRY"])) {
                $v = $_SESSION["COUNTRY"];
                $url = "http://api.openweathermap.org/data/2.5/weather?q=". $v ."&lang=fr&units=metric&appid=32639a19c61861dd2eda4d22b0021c2c";
                $raw = file_get_contents($url);

                $json = json_decode($raw);
                // var_dump($json);

                // récupération de la ville
                $name = $json->name;

                // récupération de la météo
                $weather = $json->weather[0]->main;
                $desc_weather = $json->weather[0]->description;

                // récupération de la température
                $temp = $json->main->temp;
                $feel_like = $json->main->feels_like;

                // récupération du vent
                $speed = $json->wind->speed;
                $deg = $json->wind->deg;
                // echo $speed; ?>
            <div id="weather">
                <div>
                    <h1>Météo du jour à <strong><?php echo $name; ?></strong></h1>
                    <!-- Affichage de la météo en fonction de l'état de ce dernier -->
                    <?php switch($weather) {
                            // Soleil
                            case "Clear": ?>
                                <img src="ASSETS/soleil.png" alt="soleil"/>
                    <?php       break;
                            // Venteux
                            case "Drizzle": ?>
                                <img src="ASSETS/vent.png" alt="vent" />
                    <?php       break;
                            // Brumeux
                            case "Mist": ?>
                                <img src="ASSETS/vent.png" alt="vent" />
                    <?php       break;
                            // Pluie
                            case "Rain": ?>
                                <img src="ASSETS/pluie.png" alt="pluie"/>
                    <?php   break;
                            // Nuage
                            case "Clouds": ?>
                                <img src="ASSETS/nuage.png" alt="nuageux" />
                    <?php       break;
                            // Neige
                            case "Snow": ?>
                                <img src="ASSETS/neige.png" alt="neige" />
                    <?php       break;
                            // Foudre
                            case "Thunderstorm": ?>
                                <img src="ASSETS/foudre.png" alt="orage" />
                    <?php       break;
                            // Météo inconnue
                            default: ?>
                                <img src="ASSETS/unknow.png" alt="orage" />
                    <?php break; } ?>
                </div>
                <div id="infoweather">
                    <h2>
                        Température - <?php echo $temp; ?> °C </br>
                        Ressentis - <?php echo $feel_like; ?> °C </br>
                        Vent - <?php echo $speed; ?> Km/h </br>
                        Direction du vent - <?php echo $deg; ?>° </br>
                        Temps - <?php echo $desc_weather; ?>
                    </h2>
                </div>
            </div>
        <?php } ?>
        
        <!-- Heure -->
        <div id="hour">
            <p></p>
        </div>

        <!-- Calendrier -->
        <div class="container">
            <div class="calendar">
                <div class="month">
                    <!-- Information du mois courant 
                            - Le mois
                            - Le jour en chiffre et en chaîne de caractère
                            - L'année
                    -->
                    <!-- Flèche allant vers la gauche -->
                    <div class="dir">
                        <!-- <button onclick="getPrevMonth()">◄</button> -->
                        <img src="ASSETS/left.png" alt="Flèche gauche" onclick="getPrevMonth()"/>
                    </div>
                    <!-- La date  -->
                    <div class="date"> 
                        <h1></h1>
                        <p id="day"></p>
                    </div>
                    <!-- Flèche allant vers la droite -->
                    <div class="dir">
                        <!-- <button onclick="getNextMonth()" title="droite">►</button> -->
                        <img src="ASSETS/right.png" alt="Flèche droite" onclick="getNextMonth()"/>
                    </div>
                </div>

                <div id="choose" class="form">
                    <p>Afficher un autre mois :</p>
                    
                    <form action="index.php" method="POST">
                        <!-- LE CHOIX ENTRE LES 12 MOIS -->
                        <select id="selection" name="month" required>
                            <option value="Janvier"> Janvier </option>
                            <option value="Février"> Février </option>
                            <option value="Mars"> Mars </option>
                            <option value="Avril"> Avril </option>
                            <option value="Mai"> Mai </option>
                            <option value="Juin"> Juin </option>
                            <option value="Juillet"> Juillet </option>
                            <option value="Aout"> Aout </option>
                            <option value="Septembre"> Septembre </option>
                            <option value="Octobre"> Octobre </option>
                            <option value="Novembre"> Novembre </option>
                            <option value="Décembre"> Décembre </option>
                        </select>
                        <!-- LE CHOIX DE L'ANNEE -->
                        <input type="text" name="year" pattern="[0-9]{4}" required/>
                        <input type="submit" value="Confirmer" name="Confirmer"/>
                    </form>     
                </div>  

                <div class="week">
                    <!-- Information de la semaine liste de tout les jours de la semaine -->
                    <div>Lun</div>
                    <div>Mar</div>
                    <div>Mer</div>
                    <div>Jeu</div>
                    <div>Ven</div>
                    <div>Sam</div>
                    <div>Dim</div>
                </div>
                <!-- Les jours chiffrés -->
                <div id="daysInMonth">               
                </div>
            </div>
        </div>
        

        <?php 
        if (isset($_SESSION["ID"])) {
        ?>
            <div id="events">
                <h1>Mes prochains évènements</h1>
                <!-- Liste des evenement sur PARAEVEDAY jours -->
                <?php
					if (isset($_GET['CHECK'])) {
				?>
					<script>
						alert("Une erreur de saisie des dates ou des heures");
					</script>
				<?php
					}
					
                    // Récupération de la valeur PARADAYEVE
                    $limit = $pdo->prepare("SELECT PARADAYEVE 
                                            FROM parameter 
                                            WHERE PARANUM = ?");
                    $limit->execute(array($_SESSION["ID"]));
                    $datas = $limit->fetch();   
                    $prevision = $datas["PARADAYEVE"];
                
                    // AFFICHAGE DES EVENEMENTS SUR PARADAYEVE JOURS
                    $request = $pdo->prepare('SELECT EVENUM, EVETITLE, 
                                        DATE_FORMAT(EVESTART, "%d/%m/%Y") AS START_D,
                                        DATE_FORMAT(EVEEND, "%d/%m/%Y") AS END_D, 
                                        DATE_FORMAT(EVEHOURSTART, "%H:%i") AS START_H,
                                        DATE_FORMAT(EVEHOUREND, "%H:%i") AS END_H,
                                        DATEDIFF(EVESTART, NOW()) AS TIMER, 
                                        DATEDIFF(EVEEND, EVESTART) AS DIFF
                                        FROM events 
                                        WHERE EVECONS = '. $_SESSION["ID"] .' AND DATEDIFF(EVESTART, NOW()) <= '. $prevision .  
                                        ' AND DATEDIFF(EVESTART, NOW()) >= 0 ORDER BY EVESTART ASC');

                    if ($request->rowCount() > 0) {
                        ?>
                        <ul>
                        <?php
                        while($r = $request->fetch()) { ?>
                                    <li>
                                        <section>
                                            <p class="displayPrevision">
                                                <?php
                                                    if ($r['DIFF'] == 0) {
                                                ?>
                                                        <span> <?php echo $r['EVETITLE'] ?> </span> le 
                                                        <span> <?php echo $r['START_D'] ?> </span> de
                                                        <span> <?php echo $r['START_H'] ?> </span> à 
                                                        <span> <?php echo $r['END_H'] ?> </span>.
                                                <?php
                                                    } else {
                                                ?>
                                                        <span> <?php echo $r['EVETITLE'] ?> </span> de 
                                                        <span> <?php echo $r['START_D'] ?> </span> à 
                                                        <span> <?php echo $r['START_H'] ?> </span> au
                                                        <span> <?php echo $r['END_D'] ?> </span> à 
                                                        <span> <?php echo $r['END_H'] ?> </span>.
                                                <?php
                                                    }
                                                ?>
                                            </p>
                                            <p class="timer"><?php echo "TEMPS RESTANT - " . $r['TIMER'] . " JOURS."; ?></p>
                                            <button type="submit" onclick="modifiedEvent(<?php echo $r['EVENUM']; ?>)">Modifier</button>
                                            <button type="submit" onclick="messageSupprEvent(<?php echo $r['EVENUM']; ?>, '<?php echo $r['EVETITLE']; ?>')">Supprimer</button>
                                        </section>
                                    </li>                        
                    <?php } echo '</ul>'; }  else { ?>
                    
                    <p>Rien n'a été encore programmé pour les <?php echo $prevision; ?> prochains jours</p>
                    <?php } ?>
            <div>
                <div id="displayEvent">
                <!-- liste des évènement par rapport au jour clique -->
                </div>
                <div id="addevents">
                <!-- ajouter un evenement et de presenter les evenement du jour clique -->
                </div>
            </div>
            
        <?php } ?> 
        <footer>
            <p>Calendar - 2021</p>
            <p>&copy - Tout droits réservés</p>
            <div>
                Icônes conçues par 
                <a href="https://www.flaticon.com/fr/auteurs/flexsolution" title="FlexSolution">FlexSolution</a> 
                depuis 
                <a href="https://www.flaticon.com/fr/" title="Flaticon">www.flaticon.com</a>
            </div>
        </footer>
        
    </body>
</html>

<!-- Insertion du script -->
<script src="JS/time.js"></script>  

<!-- INITIALISATION -->


<script>
    // console.log(isOnline);
    function directPrefer() {
        window.location.replace("CONFIG/parameter.php");
    }

    function messageYesNo() {
        var message = "Etes-vous sur de vouloir se déconnecter ? ";
        // Test du résultat de la fenêter de confirmation
        if (confirm(message)) {
            window.location.replace("CONFIG/logout.php");
        }
    }

    function modifiedEvent(idevent) {
        window.location.replace("CONFIG/modified.php?IDEVENT=" + idevent);
    }

    function messageSupprEvent(idevent, nameevent, number) {
        var message = "Etes-vous sur de vouloir supprimer " + nameevent;
        if (confirm(message)) {
            // removeEvent(nameevent, number);
            window.location.replace("CONFIG/deleteevent.php?IDEVENT=" + idevent);
        }
    }

    // function initEvents() {
    //     // DATE DE DEBUT et de FIN [JOUR, MOIS, ANNEE]
    //     console.log("tableau" + tab);
    //     initEvent(tab);
    // }
    
    setOnline(isOnline);
    setColor(color);
    initEvent(tabEvent);
    addDaysClose(true);
    displayHour();
    displayDate();
    displayCalendar();
</script>

<?php
    if (isset($_POST["Confirmer"])) {   
        $month = $_POST['month'];
        $year = $_POST['year'];
?>
        <script>
            var month = "<?php echo $month; ?>";
            var year = <?php echo $year; ?>;
            getMonthDetail(month, year);
        </script>
<?php } ?> 
