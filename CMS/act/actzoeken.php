<?php
/**
*  Activiteit zoeken.
*  Deze pagina bevat de functie: show_results($SQL,$msg)
*/
//Controleer eerst of er al een session is (!)
if ( !isset ($_SESSION['user']) )  {
  die("Hack attempt");
}
else
{	## Else[1] (deze wordt onderin afgesloten)

  if(isset($_POST['subsearch']))
  {
    $search = $_POST['search'];
	
	if (!empty($_POST['week']))
	{
	  $week = $_POST['week'];
	  $week2 = $_POST['week2'];
	  
	  if (is_numeric($week)) 
	  {
        if(is_numeric($week2) && ($week2 >= $week))
		{
		  $Week = " Week BETWEEN ".$week." AND ".$week2;
		}
		else
		{
		  $Week = " Week = ".$week;
		}
	  }
      else
	  {
	    $Week = " Week LIKE '%'";
	  }
	}
	switch($_SESSION['usertype'])
	{
	  case "Admin":
	    $SQL = "SELECT * FROM tblactiviteiten 
			RIGHT JOIN tblgever on tblactiviteiten.IDgever = tblgever.IDgever
			LEFT JOIN tblsoort on tblactiviteiten.IDSoort = tblsoort.IDSoort
	  		LEFT JOIN tblniveau on tblactiviteiten.IDNiveau = tblniveau.IDNiveau
	  		LEFT JOIN tblopleiding on tblactiviteiten.IDOpleiding = tblopleiding.IDOpleiding
			WHERE (`Trefwoord` LIKE '%".quote_smart($search)."%') 
			OR ( Titel LIKE '%".quote_smart($search)."%') 
			OR ( Info LIKE '%".quote_smart($search)."%') 
			AND ".$Week." 
			ORDER BY Week DESC, Dag ASC, Tijd";
	  break;
	  case "Intern":
		$SQL = "SELECT * FROM tblactiviteiten 
			LEFT JOIN tblgever on tblactiviteiten.IDgever = tblgever.IDgever
			LEFT JOIN tblsoort on tblactiviteiten.IDSoort = tblsoort.IDSoort
	  		LEFT JOIN tblniveau on tblactiviteiten.IDNiveau = tblniveau.IDNiveau
	  		LEFT JOIN tblopleiding on tblactiviteiten.IDOpleiding = tblopleiding.IDOpleiding
			WHERE (`Trefwoord` LIKE '%".quote_smart($search)."%') 
			OR ( Titel LIKE '%".quote_smart($search)."%') 
			OR ( Info LIKE '%".quote_smart($search)."%') 
			AND tblactiviteiten.IDgever = '".quote_smart($_SESSION['IDGever'])."' 
			AND ".$Week."
			ORDER BY Week DESC, Dag ASC, Tijd";
	  break;
	}

	show_results($SQL, "U zocht op: '".$search."' en deze resultaten zijn gevonden: <BR />\n");
  } 
  else
  { 
  ?>
  <b> Zoek een activiteit op titel </b>
  <br><br>
  <form method="POST" action="" name="search_form">
    <fieldset  style="width:320px;">
	  <table cellpadding="3" id="main">
	    <tr>
		  <td>Trefwoord:</td>
		  <td>
		    <input type="text" name="search" style="width: 200px;" />
		  </td>
		</tr>
		<tr>
		  <td>Tussen week:</td>
		  <td>
		    <select name="week"  style="width: 60px;">
			  <?php 
			  //Genereer weeknummers
			  $current_week = date('W');
			  
			  for ($i=1;$i<=52;$i++)
			  {
			    if ($i == $current_week)
				{
				  echo '<option value="%" selected="selected">Alle</option>';
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
				  echo '<option value="%" selected="selected"> Geen </option>';
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
		    <div align="right">
			  <input type="submit" name="subsearch" value="Zoek">
			</div>
		  </td>
		</tr>
	  </table>
	</fieldset>
  </form>
  <?php
  }
}

function show_results($SQL,$msg)
{
  echo '<div id="main">
  	    <center><div id="goed">'.$msg.'</div><br />';
	$query = mysql_query($SQL) or die(mysql_error() );
	
if ( mysql_num_rows($query) )
  {
    echo '
		<center>
	    <table id="tab">
	      <th>Week</th>
		  <th>Dag</th>
		  <th>Titel</th>
		  <th>Type</th>
		  <th>Niveau</th>
		  <th>Tijd</th>
		  <th>Lokaal</th>
		  <th>Docent</th>
		  <th>Limiet</th>
		  </tr>
		  ';	
	while ($row= mysql_fetch_assoc($query))
	{
	  $id = $row['IDActiviteit'];
	  $Week = $row['Week'];
	  $Jaar = $row['Jaar'];
	  $Titel = $row['Titel'];
	  $Type = $row['Soort'];
	  $Niveau = $row['Niveau'];
	  $Tijd = $row['Tijd'];
	  $Lokaal = $row['Lokaal'];
	  $voornaam = substr($row['gVoornaam'],0,1);
	  $Gever = $row['Naam'];
	  $Limiet = $row['Limiet'];
	  $Archief = $row['AArchief'];
	  $Docent = $row['IDGever'];
	  
	  $InlogID = $_SESSION['IDGever'];

	  // -- Genereer de dag naam -- //
	  $dag = switch_day($row['Dag']);

	  if (empty($Lokaal))
	  {
	  	$Lokaal = "&nbsp;";
	  }									
	  if (empty($Limiet))
	  {
	  	$Limiet = "&nbsp;";
	  }
	  switch ($row['Plaats'])
	  {
	    case 0: $plaats = "G";break;
		case 1: $plaats = "Z";break;
		case 2: $plaats = "E";break;
	  }
	  if ($Archief == "0") 
	  {
	    echo '
			 <tr>
			   <td><center>'.$Week.'</center></td>
			   <td>'.$dag.'</td>
			   <td>
			     <u>
			      ';
			       if ($Docent == $InlogID || $_SESSION['IDType'] == 1)
			       {
				     echo '<a href="index.php?page=actaanpassen&actid='.$id.'&week='.$Week.'&jaar='.$Jaar.'">'.$Titel.'</a>';
				   }
				   else
				   {
					 echo $Titel;
				   }
				   echo '
				 </u>
			   </td>
			   <td>'.$Type.'</td>
			   <td><center>'.$Niveau.'<center></td>
			   <td>'.$Tijd.'</td>
			   <td>'.$plaats.' '.$Lokaal.'</td>
			   <td>'.$voornaam.', '.$Gever.'</td>
			   <td><center>'.$Limiet.'</center></td>
			 </tr>
			 ';
	  } 
	  elseif ($Archief == "1") 
	  {
	    echo '
		     <tr>
			   <td><center>'.$Week.'</center></td>
			   <td>'.$dag.'</td>
			   <td>
			     <u>';
				 if ($Archief == "1") {
				   echo '<font style="font-style:italic;">';
				 }
			         
			          if ($Docent == $InlogID || $_SESSION['IDType'] == 1)
			          {
				        echo '<a href="index.php?page=actaanpassen&actid='.$id.'&week='.$Week.'&jaar='.$Jaar.'">'.$Titel.'</a>';
				      }
				      else
				      {
					    echo $Titel;
				      }
				 if ($Archief == "1") {
				   echo '</font>';
				 }
				      echo '
				 </u>
			   </td>
			   <td>'.$Type.'</td>
			   <td><center>'.$Niveau.'<center></td>
			   <td>'.$Tijd.'</td>
			   <td>'.$Lokaal.'</td>
			   <td>'.$voornaam.' '.$Gever.'</td>
			   <td><center>'.$Limiet.'</center></td>
			 </tr>
			 ';
	  }
	}
	echo '</table>';
  }
  else
  {
    echo 'Er zijn geen resultaten die voldoen aan uw zoekopdracht.';
  }
}
?>