<?
	include_once("../include.php");		
	header('Content-type: text/calendar');
	header('Content-Disposition: inline; filename=calendar.ics');

	echo "BEGIN:VCALENDAR\r\n";
	echo "PRODID:-//Ateliers Instant Z//OMO 0.59//FR\r\n";
    echo "VERSION:2.0\r\n";

    echo "CALSCALE:GREGORIAN\r\n";
    echo "METHOD:PUBLISH\r\n";
    
	if (isset($_GET["circle"]) ) {	

		$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
	$meetings=$circle->getMeetings();	
	
	} else 
	if (isset($_GET["org"]) ) {	
		$org=$_SESSION["currentManager"]->loadOrganisation($_GET["org"]);
		$meetings=$org->getMeetingList();
	} else {
		exit;
	}
		foreach ($meetings as $meeting) {
		    echo "BEGIN:VEVENT\r\n";
		    echo "UID:" . md5(uniqid(mt_rand(), true)) . "@openmyorganization.com\r\n";
		    //echo "DTSTAMP;TZID=Europe/Zurich:" . gmdate('Ymd').'T'. gmdate('His') . "\r\n";
		    echo "DTSTART:".$meeting->getDate()->setTimezone(new DateTimeZone('GMT'))->format("Ymd")."T".str_replace(":","",$meeting->getStartTime())."\r\n";
		    echo "DTEND:".$meeting->getDate()->setTimezone(new DateTimeZone('GMT'))->format("Ymd")."T".str_replace(":","",$meeting->getEndTime())."\r\n";
		    echo "SUMMARY: [".$meeting->getCircle()->getName()."] Reunion de ".$meeting->getMeetingType()."\r\n";
		    echo "LOCATION: ".$meeting->getLocation()."\r\n";
		    echo "END:VEVENT\r\n";
		}
	echo "END:VCALENDAR";
	
?>
