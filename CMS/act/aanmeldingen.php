<?php
/**
*  Aanmeldingen / Presentielijst maken.
*  Functies: show_aanmeldingen() staat in: inc_functions_beheer.php
*            switch_day_full()   staat in: inc_functions.php
*/
session_start();
include ("../../includes/inc_data.php");
//Controleer eerst of er al een session is (!)
if (!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{	## Else[1] (deze wordt onderin afgesloten)
?>
<html>
<title> Presentielijst </title>
<link href="../../includes/default.css" type="text/css" rel="stylesheet" />
<body style="background-color:#FFF;">
<div id="main" style="background-color:#FFF;">
<div id="head" style="background-color:#FFF;"> Presentielijst </div>
<br />

<?php
	$actid = $_GET['actid'];
	$q_activiteit = mysql_query("
								SELECT
									act.IDActiviteit,
									act.Week,
									act.Jaar,
									act.Dag,
									act.Tijd,
									act.Titel,
									act.IDGever,
									gev.Naam,
									gev.gVoornaam
								FROM
									tblactiviteiten AS act,
									tblgever AS gev
								WHERE
									act.IDActiviteit = '".quote_smart($actid)."'
								AND
									act.IDGever = gev.IDGever
								");

	$f_act = mysql_fetch_assoc($q_activiteit);
	
	$dag = switch_day_full($f_act['Dag']);

		
  echo '<strong>
  			Activiteit:&nbsp;'.$f_act['Titel'].'</br>
  		    Week:&nbsp;'. $f_act['Week'].'</br> 
			Dag:&nbsp;'. $dag.'</br> 
			Tijd:&nbsp;'.$f_act['Tijd'].'</br>
		    Gever:&nbsp;'.$f_act['gVoornaam'].' '.$f_act['Naam'].'
		</strong>';
  
  $q_wel_geact = mysql_query("
					 SELECT 
					 	ins.IDActiviteit,
						ins.IDStudent,
						ins.Commentaar,
						stu.Achternaam,
						stu.Voornaam,
						stu.Klas
					 FROM 
					 	tblinschrijvingen AS ins, 
						tblstudent AS stu
					 WHERE
					 	ins.IDActiviteit = '".quote_smart($actid)."'
					 AND
					 	ins.Activated = 1
					 AND
					 	ins.IDStudent = stu.IDStudent
					ORDER BY 
						stu.Achternaam ASC
					");
  $q_niet_geact = mysql_query("
					 SELECT 
					 	ins.IDActiviteit,
						ins.IDStudent,
						ins.Commentaar,
						stu.Achternaam,
						stu.Voornaam,
						stu.Klas
					 FROM 
					 	tblinschrijvingen AS ins, 
						tblstudent AS stu
					 WHERE
					 	ins.IDActiviteit = '".quote_smart($actid)."'
					 AND
					 	ins.Activated = 0
					 AND
					 	ins.IDStudent = stu.IDStudent
					ORDER BY 
						stu.Achternaam ASC
							  ");
	
  $num = mysql_num_rows($q_wel_geact);
  $num2 = mysql_num_rows($q_niet_geact);
		
  if ($num == 0 && $num2 == 0)
  {
    echo '<p> Er zijn geen aanmeldingen...</p>';
  }
  elseif($num == 1)
  {
    show_aanmeldingen($q_wel_geact,$q_niet_geact,$actid,$num2);
	echo '<br>Er is <b> '.$num.' </b>  aanmelding<br /> <br />';
  }
  elseif ($num >1)
  {
    show_aanmeldingen($q_wel_geact,$q_niet_geact,$actid,$num2);
	echo '<br>Er zijn <b> '.$num.' </b>  aanmeldingen<br /> <br />';
  }
  elseif ($num <=0 && $num2 ==1)
  {
    show_aanmeldingen($q_wel_geact,$q_niet_geact,$actid,$num2);
	echo '<br>Er is <b> '.$num2.' </b>  aanmelding<br /> <br />';	
  }
  elseif ($num <=0 && $num2 >1)
  {
    show_aanmeldingen($q_wel_geact,$q_niet_geact,$actid,$num2);
	echo '<br>Er zijn <b> '.$num2.' </b>  aanmelding<br /> <br />';	
  }    

?>
</tr>
</table>
<a href="javascript:window.print()" style="background-color:#FFFFFF;color:#000000;">Afdrukken</a>
<?php
} ##Else[1]
?>
</body>
</html>