<?php
include_once('../includes/inc_data.php');

	/**
	*   Hier worden alle activiteiten van een week nummer terug in het achief gezet.
	*   aanmelden op '0' en archief op '0'
	*   Deze pagina is gemaakt om in een cron job te draaien. Beste is dit elke maandag ochtend 01:00 ongeveer te starten.
	*/
	
	$curweek = date("W");
	$curdag = date("w");
	$curjaar = date("Y");
	
	/*	Test resultaten.
	
	echo "<p> W:".$curweek." J:".$curjaar." D:".$curdag."</p>";
	
	$q_test = mysql_query("SELECT * FROM tblactiviteiten WHERE 
						Week = '". $curweek. "' AND Jaar = '".$curjaar."'");
	
	while ( $f = mysql_fetch_assoc($q_test) ) {
		echo $f['IDActiviteit']." ".$f['Titel']." W:".$f['Week']." J:".$f['Jaar']." D:".$f['Dag']."<br />";	
	}*/
	
	mysql_query("UPDATE 
						tblactiviteiten 
					SET 
						Aanmelden='0',
						AArchief = '1'
					WHERE 
						Week = '". $curweek. "' AND Jaar = '".$curjaar."'
				 " )or die (mysql_error());
	

?>