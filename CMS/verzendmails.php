<?php
/**
*   De mensen die zich aangemeld hebben voor een cursus 1 dag van tevoren een email versturen.
*   Deze pagina is gemaakt om in een cron job te gebruiken.
*/
include_once('../includes/inc_data.php');
// Controle melingen laten zien.
$DEBUG  = false;

	$q_activiteiten = mysql_query("SELECT
					IDActiviteit, Titel, Tijd, Lokaal, Plaats, Week, Jaar, Dag, Info
				FROM	tblactiviteiten
				WHERE Jaar = ".date('Y')."
				AND Week = ".date('W')."
				AND Dag = ".( date('w')+1 )."
				");
	if ( $DEBUG ) {
		echo 'Aantal acticiteiten: '.mysql_num_rows($q_activiteiten);	
	}
	
if ( mysql_num_rows($q_activiteiten) ) {
		while( $f_act = mysql_fetch_assoc($q_activiteiten) ) {
			switch ($row['Plaats'])
		  {
			case 0: $plaats = "Gouda";break;
			case 1: $plaats = "Zoetermeer";break;
			case 2: $plaats = "Extern";break;
		  }
			
			// Alleen inschrijvingen ophalen die geactiveerd zijn.
			$q_users = mysql_query("SELECT
							IDActiviteit, IDStudent, Activated
						FROM	tblinschrijvingen
						WHERE IDActiviteit = '".$f_act['IDActiviteit']."'
						AND Activated = 1");
			if ( $DEBUG ) {
				echo 'Aantal aanmeldingen: '.mysql_num_rows($q_users);	
			}
			
			while($f_users = mysql_fetch_assoc($q_users)) {
				// Email adres en naam bij IDStudent ophalen
				$q_email = mysql_query("SELECT
								IDStudent, Email, Voornaam, Achternaam
							FROM	tblstudent
							WHERE IDStudent = '".$f_users['IDStudent']."'
						");
				
				$f_email = mysql_fetch_assoc($q_email);
				
			  if ( $f_email['Email'] != '' ) {
				$sendMail = new email();
				$sendMail->set_ontvanger($f_email['Voornaam'].' '.$f_email['Achternaam'], $f_email['Email']);
				$sendMail->set_onderwerp("Kennisbus herinering activiteit: ".$f_act['Titel']);
				$bericht = 'Dit is een herinering voor de cursus <strong>'.$f_act['Titel'].'</strong><br />';
				$bericht.= 'http://www.kennisbus.nl/index.php?page=infoact&actid='.$f_act['IDActiviteit'].'<br /><br />';
				$bericht.= $f_act['Info'].'<br />';
				$bericht.= 'Deze cursus vind plaats aanstaande '.switch_day_full($f_act['Dag']).' om '.$f_act['Tijd'].'<br />';
				$bericht.= 'In lokaal'.$f_act['Lokaal'].' in '.$plaats.'<br />';
				
				$sendMail->set_bericht($bericht);
				
				if ( $sendMail->send() ) {
					if ( $DEBUG ) {
						echo '<br />Gelukt';	
					}
				}
				else {
					if ( $DEBUG ) {
						echo '<br />Mislukt';	
					}
				} 
			  }
			}
		}	
}
else {
	
}
?> 