<div id='main'>
<?php
//Controleer eerst of er wel of geen zoekopdracht wordt gegeven
if (isset($_POST['Submit']))
{
  //Zet de zoekvelden standaard op 0, vul ze later.. of niet..

  //Controleer welke velden zijn ingevoerd
  // -------------------------------------------- //
  if (!empty($_POST['trefwoord']))
  {
    $trefwoord=$_POST['trefwoord'];
	$q_trefwoord = "(`Trefwoord` LIKE '%".quote_smart($trefwoord)."%') OR ( Titel LIKE '%".quote_smart($trefwoord)."%') OR ( Info LIKE '%".quote_smart($trefwoord)."%')";
	//echo ("$trefwoord");
	//$Trefwoord = "Trefwoord LIKE '%$trefwoord%'";
  }
  else
  {
    $q_trefwoord = "";
  }
  // -------------------------------------------- //
  if (!empty($_POST['IDOpleiding']))
  {
    $opleiding=$_POST['IDOpleiding'];
	$q_opleiding = '';
	if (!empty($q_trefwoord) ) {
		$q_opleiding .= 'AND ';	
	}
	$q_opleiding .= "`Opleiding` LIKE '%".$opleiding."%'";
  }
  else {
	$q_opleiding = '';  
  }
  // -------------------------------------------- //
  if (!empty($_POST['IDSoort']))
  {
    $type=$_POST['IDSoort'];
	$q_soort = '';
	if (!empty($q_trefwoord) || !empty($q_opleiding)) {
		$q_soort .= 'AND ';
	}
	$q_soort .="tblactiviteiten.IDSoort = ".$type."";
  }
  else {
	$q_soort = '';  
  }
  // -------------------------------------------- //
  if (!empty($_POST['week']))
  {
	  $weeknr_een = $_POST['week'];
	  $weeknr_twee = $_POST['week2'];
	  $week = '';
	if (!empty($q_trefwoord) || !empty($q_opleiding)) {
		$week .= 'AND ';
	}
	if (ctype_digit($weeknr_twee)) 
	{
	  if(ctype_digit($weeknr_twee) && ( $weeknr_twee >= $weeknr_een))
	  {
	    $week .= " Week BETWEEN ".quote_smart($weeknr_een)." AND ".quote_smart($weeknr_twee);
	  }
	  else
	  {
	    $week .= " Week = ".quote_smart($weeknr_een);
	  }
	}
	else
	{
	  $week .= "  Week LIKE '%'";
	}
  }
  else {
	 $week  = ""; 
  }
  
  // -------------------------------------------- //
  echo '<div id="head"> Zoeken</div>';
  // genereer daar een SQL statement van
  $q_test  = "SELECT * FROM tblactiviteiten
		      LEFT JOIN tblsoort on tblactiviteiten.IDSoort = tblsoort.IDSoort
	  	      LEFT JOIN tblopleiding on tblactiviteiten.IDOpleiding = tblopleiding.IDOpleiding 
		      WHERE AArchief = 0 AND ".$q_trefwoord." ".$q_opleiding." ".$q_soort."
                       ".$week." ORDER BY Jaar DESC, Week DESC,Dag ASC";

  $SQL = mysql_query($q_test);

  if(is_resource($SQL))
  {
    $i = mysql_num_rows($SQL);
	if ($i >=1)
	{
	  echo '<div id="main">De volgende resultaten voldeden aan uw zoekopdracht:<br><br>';
	  echo '<table id="tab" style="border:1px;">';
	  while ($row= mysql_fetch_array($SQL))
	  {
		if ( $row['AArchief'] == 1 ) {
			// Achief niet weergeven. in query verwerken lukte niet :s	
		}
		else {
			$ID = $row['IDActiviteit'];
			$Titel = $row['Titel'];
			$Info = $row['Info'];
			$Type = $row['Soort'];
			$Week = $row['Week'];
			$Jaar = $row['Jaar'];
				$Tijd = $row['Tijd'];
			$dag = switch_day($row['Dag']);
			//Limiteer de infotekst
			if($Info =="<br>" || ($Info =="<BR>") || (empty($Info)))
			{
			  $Info="Geen informatie opgegeven";
			}
			else
			{
			  if(strlen($Info) >= 50)
			  {
				$Info = $Info." ";
				$Info = substr($Info,0,50);
				$Info = substr($Info,0,strrpos($Info,' '));
				$Info = $Info."...";
			  }
			}
				$blank = date('w') ; 
				$uur = date("H");
				$actuur = substr("$Tijd",0,2);
	
			if ($actuur -2 <= $uur && $row['Dag'] <= $blank && $row['Week'] <= date('W') && $row['Jaar'] 
						<= date('Y') || $row['Aanmelden'] == 0)
			{
			  $URI = "<B><a href='index.php?page=infoact&actid=".$ID."' style=color:red;>".$Titel."</a></B>";
			  $OI = "<font color='red'>Type: ".$Type."</Font></TD>
				 <TD><font color='red'>Jaar: ".$Jaar."</Font></TD>
					 <TD><font color='red'>Week: ".$Week."</Font></TD>
					 <TD><font color='red'>Dag: ".$dag."</Font>";
			}
			elseif ($actuur -2 <= $uur && $row['Dag'] <= $blank && $row['Week'] <= date('W')
					&& $row['Jaar'] <= date('Y'))
			{
			  $URI = "<B><a href='index.php?page=infoact&actid=".$ID."' style=color:red;>".$Titel."</a></B>";
			  $OI = "<font color='red'>Type: ".$Type."</Font></TD>
				 <TD><font color='red'>Jaar: ".$Jaar."</Font></TD>
					 <TD><font color='red'>Week: ".$Week."</Font></TD>
					 <TD><font color='red'>Dag: ".$dag."</Font>";
			}
			elseif ($actuur -2 <= $uur && $row['Dag'] >= $blank && $row['Week'] <= date('W') 
							&& $row['Jaar'] <= date('Y'))
					{
			  $URI = "<B><a href='index.php?page=infoact&actid=".$ID."' style=color:red;>".$Titel."</a></B>";
			  $OI = "<font color='red'>Type: ".$Type."</Font></TD>
				 <TD><font color='red'>Jaar: ".$Jaar."</Font></TD>
					 <TD><font color='red'>Week: ".$Week."</Font></TD>
					 <TD><font color='red'>Dag: ".$dag."</Font>";
			}
			else
			{
			  $URI = "<B><a href='index.php?page=infoact&actid=".$ID."'>".$Titel."</a></B>";
			  $OI = "Type: ".$Type."</TD><TD>Jaar: ".$Jaar."</TD><TD>Week: ".$Week."</TD><TD>Dag: ".$dag;
			}
			echo '<tr>
					<td colspan="2">Activiteit: '.$URI.'</td> 
					<td colspan="1"> '.$OI.' </td>
				  </tr>
				  <tr>
					<td colspan="6">'.$Info.'</td>
				  </tr>
				';
		}
	  }
		  echo '</tr></table>';
    }
    else
    {
      echo 'Er zijn bij deze zoekopdracht geen resultaten gevonden.';
    }
  }
  else
  {
  	echo 'Er zijn bij deze zoekopdracht geen resultaten gevonden.';
  }
}
else
{
  // Als er geen zoekopdracht wordt uitgevoerd
  // Toon zoekformulier.
?>
<div id="head">Geadvanceerd Zoeken</div>
<div id="main">
<BR />
<form method="POST" action="">
  <FIELDSET  style="width: 350px;">
    <table cellpadding="3" id="main">
	  <tr>
		<td>
		  Trefwoord:
		</td>
		<td>
		  <input type="text" name="trefwoord" style="width: 200px;">
		</td>
	  </tr>
	  <tr>
	    <td>
		  Type:
		</td>
		<td>
		  <select name='IDSoort' style='width: 200px;'>
		    <option VALUE='' selected="selected">Alle</OPTION>
			<option	VALUE='1'>Excursie</option>									
			<option	VALUE='2'>Hoorcollege</option>
			<option	VALUE='3'>Presentatie</option>					
			<option	VALUE='4'>Toets</option>			
			<option	VALUE='5'>Workshop</option>	
			<option	VALUE='6'>Overig</option>							
		  </select>
		</td>
	  </tr>
	  <tr>
	  	<td>
		  Opleiding:
		</td>
		<td>
		  <select name='IDOpleiding'  style='width: 200px;'>
		    <option VALUE='' selected="selected">Alle type opleidingen</OPTION>
			<option	VALUE='1'>Applicatie Ontwikkeling</option>									
			<option	VALUE='2'>Netwerk Beheerder</option>
			<option	VALUE='3'>ICT Beheerder</option>					
			<option	VALUE='4'>Digitaal Resercheur</option>			
			<option	VALUE='5'>Medewerker ICT</option>				
			<option	VALUE='6'>Medewerker Beheer ICT</option>								
		  </select>
		</td>
	  </tr>
	  <tr>
	  	<td>
		  Tussen week:
		</td>
		<td>
		  <select name='week'  style='width: 60px;'>
		   	<?php 
			//Genereer weeknummers
			$current_week = date('W');
			for ($i=1;$i<=52;$i++)
			{
			  if ($i == $current_week)
			  {
			    echo '<option value="" selected="selected"> Alle </option>';
				echo '<option value="'.$i.'"><b>'.$i.'</b></option>';
			  }
			  else
			  {
			    echo '<option value="'.$i.'">'.$i.'</option>';
			  }
			}
			?>
		  </select>
		  en week:
		  <select name="week2"  style="width: 70px;">
		    <?php 
			//Genereer weeknummers
			$current_week = date('W');
			for ($i=1;$i<=52;$i++)
			{
			  if ($i == $current_week)
			  {
			    echo '<option value="" selected="selected"> Geen </option>';
				echo '<option value="'.$i.'"><b>'.$i.'</b></option>';
			  }
			  else
			  {
			    echo '<option value="'.$i.'">'.$i.'</option>';
			  }
			}
			?>
		  </select>
		</td>
	  </tr>
	  <tr>
	    <td colspan="2">
		  <div align="right"><input type="submit" value="Zoeken" name="Submit"></div>
		</td>
	  </tr>
    </table>
  </fieldset>
</form>
<?php
#<? werkt niet, moet <?php zijn
// Sluit de else-statement af
}
?>