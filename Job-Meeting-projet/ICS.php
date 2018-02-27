<?php
header("Content-Type: text/Calendar");
header("Content-Disposition: inline; filename=calendar.ics");

// Passer un tableau de propriétés et dont les mêmes variables changeront à chaque tour de boucle

if(get_magic_quotes_gpc())
{
    $tabPropsGeneral = unserialize(stripslashes($_GET['tabGeneral']));
}
else
{
    $tabPropsGeneral = unserialize($_GET['tabGeneral']);
}

// L'ensemble des properties est dans un tableau contenant plusieurs tableau de properties


$nbEvent = count($tabPropsGeneral); // Longueur du tableau de tableau


for ($i=0; $i < $nbEvent ; $i++) {
  if($i == 0){
    echo "BEGIN:VCALENDAR\n";
    echo "VERSION:2.0\n";
    echo "PRODID:-//bobbin v0.1//NONSGML iCal Writer//EN\n";
    echo "CALSCALE:GREGORIAN\n";
    echo "METHOD:PUBLISH\n";
    echo "X-WR-TIMEZONE:Europe/Paris\n";
  }

  $SUMMARY = $tabPropsGeneral[$i][4];
  $DTSTART = $tabPropsGeneral[$i][0]."T".$tabPropsGeneral[$i][1];
  $DTEND = $tabPropsGeneral[$i][2]."T".$tabPropsGeneral[$i][3];

  echo "BEGIN:VEVENT\n";
  echo "DTSTART:".$DTSTART."\n";
  echo "DTEND:".$DTEND."\n";
  echo "DTSTAMP:20091130T213238\n";
  echo "UID:1285935469767a7c7c1a9b3f0df8003".$i."@JobMeeting.com\n";
  echo "CREATED:20091130T213238\n";
  echo "LOCATION:3 rue Maréchal Joffre 44100 Nantes\n";
  echo "LAST-MODIFIED:20091130T213238\n";
  echo "SEQUENCE:0\n";
  echo "DESCRIPTION:\n";
  echo "STATUS:CONFIRMED\n";
  echo "SUMMARY:"."Entretien avec l'entreprise ".$SUMMARY."\n";
  echo "TRANSP:OPAQUE\n";
  echo "END:VEVENT\n";

  if($i == $nbEvent-1){
    echo "END:VCALENDAR";
  }
}
 ?>
