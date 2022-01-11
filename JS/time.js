"use strict";

// Les variables constantes___________________________________________________________________________________________

// Months = [MOIS DE L'ANNEE type string]
const MONTHS = [
    "Janvier",
    "Février",
    "Mars",
    "Avril",
    "Mai",
    "Juin",
    "Juillet",
    "Aout",
    "Septembre",
    "Octobre",
    "Novembre",
    "Décembre"
];

// Days = [JOUR DE LA SEMAINE type string]
const DAYS = [
    "Dimanche",
    "Lundi",
    "Mardi",
    "Mercredi",
    "Jeudi",
    "Vendredi", 
    "Samedi"
];

// Les variables non statiques___________________________________________________________________________________________

var date = new Date();

// Close = [NOM DU JOUR FERIE][NUMERO DU MOIS][NUMERO DU JOUR]
// Il reste paque ascension pentecote 
// AJOUTER 3 FONCTION POUR CALCULER CES DERNIERE date 
//  CAR ELLE DEPEND DE L'ANNEE ET DU MOIS
// UNE FONCTION POUR PUSH
let close = [
    ["Jour de l'an", MONTHS.indexOf("Janvier"), 1],
    ["Fête du travail", MONTHS.indexOf("Mai"), 1],
    ["Fête de la Victoire de 1945", MONTHS.indexOf("Mai"), 8],
    ["Fête nationale", MONTHS.indexOf("Juillet"), 14],
    ["Assomption", MONTHS.indexOf("Aout"), 15],
    ["Toussaint", MONTHS.indexOf("Novembre"), 1],
    ["Armistice", MONTHS.indexOf("Novembre"), 11],
    ["Noel", MONTHS.indexOf("Décembre"), 25]
]

// Les jours organisés
// organize va s'organiser comme cela
// [NOM DE LEVENEMENT]
//[JOUR DE LEVENEMENT][MOIS DE LEVENEMENT][ANNEE DE LEVENEMENT]
//[DEBUT HORAIRE][FIN HORAIRE]
//[NUMERO DISTINCT DE LEVENEMENT]
// DELAI
let organize = [];

// Les dates courantes 
let currentYear = date.getFullYear();
let currentMonth = date.getMonth();
let currentDays = getAllDays(currentMonth, currentYear);
var currentClose = closeDays();
// var currentOrganize = organizeDays();

var lastClick =  null;
var isOnline;
var color;


// Les fonctions______________________________________________________________________________________________________

// Affichage de l'heure courante
function displayHour() {
    // ajouter le timer
    date = new Date();
    let hours = date.getHours() + ' : ' + date.getMinutes() + ' : ' + date.getSeconds();
    document.querySelector('#hour p').innerHTML = "";
    document.querySelector('#hour p').innerHTML = hours;
    // isSummerHour();

    if (isSummerHour()) {
        document.querySelector("#hour p").innerHTML += " (heure d'été)";
    } else {
        document.querySelector('#hour p').innerHTML += " (heure d'hiver)";
    }

}

// Affichage de la date courante
function displayDate() {
    // Récupération de la date souhaitée
    var d = new Date(currentYear, currentMonth);
    var current = MONTHS[d.getMonth()] + ' ' + d.getFullYear();

    // Affichage sur la page
    document.querySelector('.date h1').innerHTML = current;

    if (currentMonth == date.getMonth() && currentYear == date.getFullYear()) {
        var dating = DAYS[d.getDay()] + ' ' + date.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + date.getFullYear();
        document.querySelector('.date #day').innerHTML = dating;
    } else {
        document.querySelector('.date #day').innerHTML = '';
    }
}

// Implémente le calendrier 
function displayCalendar() { 
    // console.log(organize)
    // isSummerHour();
    var connected;
    if (isOnline) {
        connected = 'online';
    } else {
        connected = 'offline';
    }
    
    document.querySelector("#daysInMonth").classList.add(connected);
    document.querySelector("#daysInMonth").innerHTML = "";
    // Implémentation du calendrier
    var c = document.querySelector("#daysInMonth");

    var i = 0;
    var week = 1; 
    while (i < currentDays.length) {
        if (week != currentDays[i][1]) {
            c.innerHTML += "<div class='empty'></div>";
        } else {
            var id = currentDays[i][0].toString();
            c.innerHTML += "<div onclick='updateForm("
                        + id + ")' id='" + currentDays[i][0] 
                        + "'>" + currentDays[i][0] + "</div>";
            if (currentMonth == date.getMonth() && currentYear == date.getFullYear() && currentDays[i][0] == date.getDate()) {
                // ajout de la classe dday   
                document.getElementById(""+ currentDays[i][0] +"").classList.add("dday");
            } if (isClose(currentDays[i][0])) {
                // ajout de la classe close
                document.getElementById(""+ currentDays[i][0] +"").setAttribute("title", titleClose(currentClose, currentDays[i][0]));
                document.getElementById(""+ currentDays[i][0] +"").classList.add("close");
            } if (isSat(currentDays[i][0])) {
                // ajout de la classe saturday
                document.getElementById(""+ currentDays[i][0] +"").classList.add("saturday");
            } if (isSun(currentDays[i][0])) {
                // ajout de la classe sunday
                document.getElementById(""+ currentDays[i][0] +"").classList.add("sunday");
            } if (isDeck(currentDays[i][0])) {
                // ajout de la classe deck
                document.getElementById(""+ currentDays[i][0] +"").classList.add("deck");
            } if (isOrganize(currentDays[i][0], currentMonth, currentYear) && isOnline) {
                // ajout de la classe organize
                document.getElementById(""+ currentDays[i][0] +"").classList.add("organize");
            }
            i += 1;
        } 

        // Mise a jour du jour de la semaine
        if (week == 6) {
            week = 0;
        } else {
            week += 1;
        }
    }
}

/*
function test() {
    alert("c'est genial ca");
}
*/

// Apparition d'un div et changement de forme 
function updateForm(id) {
    if (isOnline) {
        // Vide l'affichage au lieu de le saturer d'éléments inutile
        document.querySelector("#addevents").innerHTML = "";
        document.querySelector("#displayEvent").innerHTML = "";

        // console.log(id);
        // console.log(lastClick);

        // Remet à jour l'affichage du dernier jour cliqué
        if (lastClick != null) {
            document.getElementById(lastClick[0]).style.backgroundColor = lastClick[1];
        } 
        // On récupère les valeur du jour cliqué à l'identifiant id
        lastClick = [id, document.getElementById(id).style.backgroundColor];
        if (isOnline) {
            document.getElementById(id).style.backgroundColor = color;
        } else {
            document.getElementById(id).style.backgroundColor = "#FF9406";
        }

        // AJOUTE UNE LISTE D'EVENEMENT CORRESPONDANT AU JOUR CLIQUEE 
        var events = document.querySelector("#displayEvent");
        events.innerHTML += "<h1>Evenement du " + id + "/" + (currentMonth + 1) + "/" + currentYear + "</h1>";
        var eventsOrganizeOfDay = daysOrganizeOfTheDay(id);
        if (eventsOrganizeOfDay.length != 0) { 
            events.innerHTML += '<ul>';
            for (var i = 0; i < eventsOrganizeOfDay.length; i++) {
                if (eventsOrganizeOfDay[i][4] == 0) {
                    document.querySelector("#displayEvent ul").innerHTML += '<li>'
                                                                         +  '<section> <p class="displayPrevision">' 
                                                                         +  '<span>' + eventsOrganizeOfDay[i][0] + '</span>' + " de "
                                                                         +  '<span>' + eventsOrganizeOfDay[i][2][0] + '</span>' + " à "
                                                                         +  '<span>' + eventsOrganizeOfDay[i][2][1] + '</span>' + '. ';
                } else {
                    var to = new Date(currentYear, currentMonth, id);
                    to.setDate(to.getDate() + eventsOrganizeOfDay[i][4]);
                    document.querySelector("#displayEvent ul").innerHTML += '<li>'
                                                                         +  '<section> <p class="displayPrevision">' 
                                                                         +  '<span>' + eventsOrganizeOfDay[i][0] + '</span>' + " à "
                                                                         +  '<span>' + eventsOrganizeOfDay[i][2][0] + '</span>' + " jusqu'au "
                                                                         +  '<span>' + to.getDate() + '/' + to.getMonth() + '/' + to.getFullYear() + '</span>' + " à "
                                                                         +  '<span>' + eventsOrganizeOfDay[i][2][1] + '</span>' + '. ';
                }
                document.querySelector("#displayEvent ul").innerHTML += '<button type="submit" onclick="modifiedEvent('+ eventsOrganizeOfDay[i][3] + ')">Modifier</button>'
                                                                     + '<button type="submit" onclick="messageSupprEvent(' + eventsOrganizeOfDay[i][3] + ',\'' + eventsOrganizeOfDay[i][0] + '\')">Supprimer</button>';
                events.innerHTML += '</p> </section>';
            }
        } else {
            events.innerHTML += "<p>Aucun évènement n'est prévu à ce jour</p>";
        }


        // <button type="submit" onclick="modifiedEvent(<?php echo $r['EVENUM']; ?>)">Modifier</button>
        // <button type="submit" onclick="messageSupprEvent(<?php echo $r['EVENUM']; ?>, '<?php echo $r['EVETITLE']; ?>')">Supprimer</button>
        
        
        // Ajoute un div qui nous servira a ajouter des évènements sur le jour cliqué (form)
        // insérer une liste d'évènement a ce jour
        // insérer un formulaire
        var box = document.querySelector("#addevents");
        
        var strMonth;
        var strDay;
        
        // int to str pour le mois 
        if (currentMonth < 10) {
            var m = (currentMonth + 1);
            strMonth = '0' + m.toString();
        } else {
            strMonth = (currentMonth + 1).toString();
        }

        // int to str pour le jour 
        if (id <= 9) {
            strDay = '0' + id.toString();
        } else {
            strDay = id.toString();
        }

        var d = currentYear.toString() + '-' + strMonth + '-' + strDay;
        // console.log(d);
        box.innerHTML += '<div><h2>Ajouter un évènement</h2>';
        
        document.querySelector('#addevents div').innerHTML += "<form action='CONFIG/addevent.php' method='POST' class='form' id='addevent'>"
                                                            + "<p>Titre :</p>"
                                                            + "<input type='text' name='title' required /> </br>" 
                                                            + "<p>Description :</p>"
                                                            + "<input type='text' name='description' /> </br>"
                                                            + "<p>Lieu :</p>"
                                                            + "<input type='text' name='location' /> </br>"
                                                            + "<p>Du </p>"
                                                            + "<input type='date' name='start' value="+ d +"  required />"
                                                            + "<p> à </p>"
                                                            + "<input type='time' name='hourstart' value='09:00' required/> </br>"
                                                            + "<p>Au </p>" 
                                                            + "<input type='date' name='end' value="+ d +" required />"
                                                            + "<p> à </p> "
                                                            + "<input type='time' name='hourend' value='09:30' required/> </br>"
                                                            + "<input type='submit' name='events' value='Enregistrer'/> </div>";
                                                            // + "</br>";
    }
}

// Remet à jour l'affichage
function updateDisplay() {
    if (document.querySelector("#addevents") != null) {
        document.querySelector("#addevents").innerHTML = "";
        document.querySelector("#displayEvent").innerHTML = "";
    }
    displayHour();
    displayDate();
    displayCalendar();
}

function updateDateOfMonth(isChangeYear) {
    currentDays = getAllDays(currentMonth, currentYear);

    // Ajout des trois jours fériés mobiles dans notre variable globale
    removeDaysClose(isChangeYear);
    addDaysClose(isChangeYear);
    currentClose = closeDays();

    // console.log("date a lannee ");
    // console.log(close);
    // console.log("date au mois ");
    // console.log(currentClose);
    updateDisplay();
}

// Initialise les jours des évènements
function initEvent(init) {
    // console.log(init);
    // console.log(organize);
    organize = [];
    for (var i = 0; i < init.length; i++) {
        organize.push(init[i]);
    }
    // console.log("ma variable organize");
    // console.log(organize);
    // updateDisplay();
}

// Supprime les jours qui seront donc plus à jour si la valeur booléenne newYear vaut true
// Ce qui veut dire que l'on change d'année et donc de dates pour les jours fériés mobiles
function removeDaysClose(newYear) {
    var title = [
        "Lundi de Pâques",
        "Jeudi de l'Ascension", 
        "Lundi de Pentecôte"
    ];

    if (newYear) {
        var newClose = [];
        for (var i = 0; i < close.length; i++) {
            if (!title.includes(close[i][0])) {
                newClose.push(close[i]);
            }
        }
        close = newClose;
    }
}
    

// Ajoute les jours fériée correspondantes à l'année
function addDaysClose(newYear) {
    if (newYear) {
        close.push(getDateEaster());
        close.push(getDateAscension());
        close.push(getDatePentecost());
    }
}

// Récupère les dates des trois derniers jours fériées.
// Calcule la date du Lundi de Paques
function getDateEaster() {
    // Calculs
    var a = Number(currentYear % 19);
    var b = Number(currentYear % 4);
    var c = Number(currentYear % 7);
    var d = Number(((a * 19) + 24) % 30);
    var expression = (2 * b) + (4 * c) + (6 * d) + 5
    var e = Number(expression % 7);
    
    // Variables que l'on a besoin pour l'ajouter à notre tableau close
    var monthEaster;
    var dayEaster;
    
    // somme
    var somme = d + e;

    // Test de nos conditions
    if (e == 6 && d == 29) {
        monthEaster = MONTHS.indexOf("Avril");
        dayEaster = 19; 
    } else if (e == 6 && d == 28) {
        dayEaster = 18;
        monthEaster = MONTHS.indexOf("Avril");
    } else if (somme < 10) {
        monthEaster = MONTHS.indexOf("Mars");
        dayEaster = d + e + 22;
    } else {
        monthEaster = MONTHS.indexOf("Avril");
        dayEaster = d + e - 9;
    } 

    return ["Lundi de Pâques", monthEaster, dayEaster + 1];
}

// Retourne la date du Jeudi de l'Ascension
function getDateAscension() {
    // On récupère la date du lundi de Pâques car on sait que le 
    // Jeudi de l'Ascension arrive 40 jours après le lundi de Pâques
    const easter = getDateEaster();

    // Création de la date initialement égale à celle du 
    // Lundi de Pâques à laquelle on ajoutera 40 jours
    var dateAscension = new Date(currentYear, easter[1], easter[2]);
    dateAscension.setDate(dateAscension.getDate() + 40);

    return ["Jeudi de l'Ascension", dateAscension.getMonth(), dateAscension.getDate() - 2];
}

// Retourne la date du Lundi de Pentecôte
function getDatePentecost() {
    // On récupère la date du lundi de Pâques car on sait que le 
    // Jeudi de l'Ascension arrive 50 jours après le lundi de Pâques
    const easter = getDateEaster();

    // Création de la date initialement égale à celle du 
    // Lundi de Pâques à laquelle on ajoutera 50 jours
    var dateAscension = new Date(currentYear, easter[1], easter[2]);
    dateAscension.setDate(dateAscension.getDate() + 50);

    return ["Lundi de Pentecôte", dateAscension.getMonth(), dateAscension.getDate() - 2];
}

// Retourne le mois suivant 
function getNextMonth() {
    var changeYear;
    if (currentMonth == 11) {
        currentMonth = 0;
        currentYear += 1;
        changeYear = true;
    } else {
        currentMonth += 1;
        changeYear = false;
    }
    updateDateOfMonth(changeYear);
}

// Retourne le mois précédent
function getPrevMonth() {
    var changeYear;
    if (currentMonth == 0) {
        currentMonth = 11;
        currentYear -= 1;
        changeYear = true;
    } else {
        currentMonth -= 1;
        changeYear = false;
    }
    updateDateOfMonth(changeYear);
}

// Retourne le mois associé au mois m et à l'année y
function getMonthDetail(month, year) {
    var changeYear = (currentYear == year); 
    currentYear = year;
    currentMonth = MONTHS.indexOf(month);
    // alert(currentYear);
    // alert(currentMonth);
    updateDateOfMonth(changeYear);
}

// Retourne le nombre de jour associé au mois selon l'année
function getNumberDayInMonth(month, year) {
    var start = new Date(year, month, 1);
    var end = new Date(year, month + 1, 1);
    return (end - start) / (1000 * 60 * 60 * 24);
    // mai 1919
}

// Retourne le numéro du jour dans le mois associé à son jour de la semaine
function getAllDays(month, year) {
    // DayWeek = [JOUR][JOUR DE LA SEMAINE]  -> tous de type entier
    var dayWeek = [];
    var numberDay = getNumberDayInMonth(month, year)
    for (var i = 1; i <= numberDay; i++) {
        var day = new Date(year, month, i);
        var week = day.getDay();
        dayWeek.push([i, week]);
    }
    return dayWeek;
}

// Retourne les différents jours des samedis du mois
function satDays(dayOfMonth) {
    // satDay = [JOUR ASSOCIE AU JOUR SAMEDI] -> valeur entiere
    var satDay = [];
    for (var i = 0; i < dayOfMonth.length; i++) {
        if (dayOfMonth[i][1] == DAYS.indexOf("Samedi")) {
            satDay.push(dayOfMonth[i][0]);
        }
    }
    return satDay;
}

// Retourne les évènement de ce jour --> id
function daysOrganizeOfTheDay(id) {
    var allEvents = [];
    if (organize.length != 0) {
        for (var i = 0; i < organize.length; i++) {
            if (organize[i][1][2] == currentYear && organize[i][1][1] == currentMonth && organize[i][1][0] == id) {
                allEvents.push(organize[i]);
            }
        }
    }
    return allEvents;
}

// Retourne les différents jours des dimanches du mois
function sunDays(dayOfMonth) {
    // sunDay = [JOUR ASSOCIE AU JOUR DIMANCHE] -> valeur entiere
    var sunDay = [];
    for (var i = 0; i < dayOfMonth.length; i++) {
        if (dayOfMonth[i][1] == DAYS.indexOf("Dimanche")) {
            sunDay.push(dayOfMonth[i][0]);
        }
    }
    return sunDay;
}

// Retourne les différents jours fériées du mois
function closeDays() {
    
    // Déclaration des variables
    // holiDay = [NOM DU JOUR FERIE][NUMERO DU JOUR]  -> string et valeur entiere
    var closeDay = [];

    // Parcours du tableau close
    for (var i = 0; i < close.length; i++) {
        if (close[i][1] == currentMonth) {
            var x = [close[i][0], close[i][2]];
            closeDay.push(x);
        }
    }

    // Retourne les jours férié en fonction du mois
    return closeDay;
}

// Retourne les différents jours ponts entre fériées et samedi du mois
function deckDays(dayOfMonth) {
    // deckDay = [JOUR] -> valeur entiere
    var deckDay = [];
    var searchClose = true;
    var searchSat = false;

    // Parcours du mois
    for (var i = 0; i < dayOfMonth.length; i++) {
        if (searchClose && !(isSat(dayOfMonth[i][0]) || isSun(dayOfMonth[i][0]))) {
            if (isClose(dayOfMonth[i][0])) {
                searchSat = true;
                searchClose = false;
            }
        }
        if (searchSat) {
            if (isSat(dayOfMonth[i][0])) {
                searchClose = true;
                searchSat = false;
            } else {
                deckDay.push(dayOfMonth[i][0]);
            }
        } 
    }

    // Retourne les jours pont du mois courant
    return deckDay;
}

// titleClose(day) -> retourne l'intitulé du jour ferie
function titleClose(closes, day) {
    for (var i = 0; i < closes.length; i++) {
        if (closes[i][1] == day) {
            return closes[i][0];
        }
    }
    return "";
}

// Retourne un bouléen si day est un jour férié dans le mois courant
function isClose(day) {
    for (var i = 0; i < close.length; i++) {
        if (close[i][1] == currentMonth && close[i][2] == day) {
            return true;
        } 
    }   
    return false;
}

// Retourne un booléen si day est un samedi dans le mois courant
function isSat(day) {
    var sat = satDays(currentDays);
    for (var i = 0; i < sat.length; i++) {
        if (sat[i] == day) {
            return true;
        } 
    }   
    return false;
}

// Retourne un booléen si day est un dimanche dans le mois courant
function isSun(day) {
    var sun = sunDays(currentDays);
    for (var i = 0; i < sun.length; i++) {
        if (sun[i] == day) {
            return true;
        } 
    }   
    return false;
}

// Retourne un bouléen si day est un jour de pont entre férié et le samedi
function isDeck(day) {
    var deck = deckDays(currentDays);
    for (var i = 0; i < deck.length; i++) {
        if (deck[i] == day) {
            return true;
        } 
    }   
    return false;
}
 
// Retourne un booléen si day est un jour organisé
function isOrganize(day, month, year) {
    for (var i = 0; i < organize.length; i++) {
        if (month == organize[i][1][1] && day == organize[i][1][0] && year == organize[i][1][2]) {
            return true;
        }
    } 
    return false;
}

// Retourne un booléen si l'heure d'été est de vigueur
function isSummerHour() {
    // console.log(date.toString());
    const text = date.toString();
    const i = text.indexOf('normale');
    if (i != -1) {
        return false;
    }
    return true;
}

// Met la valeur booléenne a isOnline
function setOnline(connected) {
    isOnline = connected;
}

// Met la couleur a color
function setColor(color) {
    color = color;
}
