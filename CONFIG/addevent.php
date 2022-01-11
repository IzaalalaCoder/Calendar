<?php
    include 'databases.php';
    
    if (!isset($_SESSION['ID'])) {
        header('Location: ../index.php');
    }
    // test date logique
    if (isset($_POST['events'])) {
        $title = htmlspecialchars($_POST['title']);
        $desc = htmlspecialchars($_POST['description']);
        $loc = htmlspecialchars($_POST['location']);
        $startdate = ($_POST['start']);
        $enddate = ($_POST['end']);
        $starthour = ($_POST['hourstart']);
        $endhour = ($_POST['hourend']);

        $checks = $pdo->prepare("SELECT * 
								 FROM events 
								 WHERE EVETITLE = ?
                                 AND EVESTART = ? 
                                 AND EVEEND = ? 
                                 AND EVECONS = ? 
                                 AND EVEHOURSTART = ? 
                                 AND EVEHOUREND = ?");
        
        $checks->execute(array($title, $startdate, $enddate, $_SESSION["ID"], $starthour, $enddate));
        $ifexist = $checks->rowCount();
        // echo $checks->rowCount();
        
        if (($enddate == $startdate && $starthour < $endhour) || ($enddate > $startdate)) {
			if ($ifexist == 0) {
				if (!empty($loc) && !empty($desc)) {
					$insert = $pdo->prepare("INSERT INTO events (EVETITLE, EVEDESC, EVELOC, EVESTART, EVEEND, EVEHOURSTART, EVEHOUREND, EVECONS) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
					$insert->execute(array($title, $desc, $loc, $startdate, $enddate, $starthour, $endhour, $_SESSION["ID"]));
				} else if (!empty($loc) && empty($desc)) {
					$insert = $pdo->prepare("INSERT INTO events (EVETITLE, EVELOC, EVESTART, EVEEND, EVEHOURSTART, EVEHOUREND, EVECONS) VALUES (?, ?, ?, ?, ?, ?, ?)");
					$insert->execute(array($title, $loc, $startdate, $enddate, $starthour, $endhour, $_SESSION["ID"]));
				} else if (empty($loc) && !empty($desc)) {
					$insert = $pdo->prepare("INSERT INTO events (EVETITLE, EVEDESC, EVESTART, EVEEND, EVEHOURSTART, EVEHOUREND, EVECONS) VALUES (?, ?, ?, ?, ?, ?, ?)");
					$insert->execute(array($title, $desc, $startdate, $enddate, $starthour, $endhour, $_SESSION["ID"]));
				} else {
					$insert = $pdo->prepare("INSERT INTO events (EVETITLE, EVESTART, EVEEND, EVEHOURSTART, EVEHOUREND, EVECONS) VALUES (?, ?, ?, ?, ?, ?)");
					$insert->execute(array($title, $startdate, $enddate, $starthour, $endhour, $_SESSION["ID"]));
				}

				// Recherche de la valeur unique de l'évènement
				$search = $pdo->prepare("SELECT EVENUM FROM events WHERE EVECONS = ? AND EVENUM = (SELECT MAX(EVENUM) FROM events WHERE EVECONS = ?)");
				$search->execute(array($_SESSION["ID"], $_SESSION["ID"]));
				$search_number = $search->fetch();
				$number = $search_number["EVENUM"];

				// header('Location: index.php?IDEVENT='.$number.
				//                             '&DAY='.$day.
				//                             '&MONTH='.$month.
				//                             '&YEAR='.$year.
				//                             '&START='.$start.
				//                             '&END='.$end.
				//                             '&DIFF='.$diff.
				//                             '&NAME='.$name);
				header('Location: ../index.php');
				exit();
			}
		} else {
			header('Location: ../index.php?CHECK=false');
			exit();
		}
    }   
?>  
