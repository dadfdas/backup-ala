<?php
/**
*  Docenten overzicht
*  Deze pagina bevat de functie: show_overzicht($SQL,$msg)
*  Deze pagina gebruikt de functie:
*    verwijderdocent()   staat in: inc_functions_beheer.php
*     quote_smart()                staat in: inc_functions.php
*/
//Controleer eerst of er al een session is (!)
if (!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{	## Else[1] (deze wordt onderin afgesloten)
?>
  <p><b>Docent overzicht</b></p>
  Zoek een docent:<br>
  <form method="POST" action="">
    <input type="text" name="name" size="37"> 
	<input type="submit" name="zoek" value="Zoek docent">
  </form>
  <?php
  if(isset($_GET['verwijderen'])) 
  { 
    if ( $_GET['verwijderen'] == "y" ) 
    { 
      verwijderdocent($id); 
    } 
  }  
  
  if (!isset ($_POST['zoek']))
  {
    //Als dit niet het geval is: toon overzicht
	$SQL=mysql_query("SELECT * FROM tblgever ORDER BY Naam ASC,gVoornaam ASC, IDType");
	show_overzicht($SQL,'');
  }
  else
  {
    $Name = $_POST['name'];
	$search=mysql_query("SELECT * FROM tblgever WHERE Naam LIKE '%".quote_smart($Name)."%' OR gVoornaam LIKE '%".quote_smart($Name)."%' ORDER BY IDType, Naam, gVoornaam ASC");
	show_overzicht($search,'<center>
	                          <div id="goed">U zocht: '.$Name.' en de volgende resultaten zijn gevonden:</div>
							</center><br />');
  }
} ##Sluit Else[1]

function show_overzicht($SQL,$msg)
{
  if (!mysql_num_rows($SQL))
  {
    echo "Er zijn geen gegevens gevonden.";
  }
  else
  {
    echo $msg.'
		 <table id="tab">
		   <tr>
		     <th>Edit</th>
		     <th>Delete</th>
			 <th>Naam</th>
			 <th>Voornaam</th>
			 <th>Email</th>
			 <th>Type</th>
			 <th>Bedrijf</th>
			 <th>Straat</th>
			 <th>Postcode</th>
			 <th>Woonplaats</th>
			 <th>Telefoonnummer</th>
		   </tr>
		 ';
    while($row = mysql_fetch_array($SQL)) 
	{
	    $id = $row['IDGever'];
		$naam = $row['Naam'];
		$voornaam = $row['gVoornaam'];
		$email = "<a href='mailto:".$row['Email']."'>".$row['Email']."</a>";
		$wachtwoord = $row['Wachtwoord'];
		$type = $row['IDType'];
		$bedrijf = $row['Bedrijf'];
		$straat = $row['Adres'];
		$pcode = $row['Postcode'];
		$wplaats = $row['Woonplaats'];
		$Tel = $row['Telefoon'];
		$Fax = $row['Fax'];
			
		// Hier komen de IFstatements die een spatie neerzetten als een StrING leeg is.
		if(empty($naam))
		{
		  $naam="&nbsp;";
		}
		if(empty($bedrijf))
		{	
		  $bedrijf="&nbsp;";
		}
		if(empty($straat))
		{ 	
		  $straat="&nbsp;";
		}
		if(empty($pcode))
		{ 		
		  $pcode="&nbsp;";
		}
		if(empty($wplaats))
		{ 	
		  $wplaats="&nbsp;";
		}
		if(empty($Tel))
		{ 		
		  $Tel="&nbsp;";
		}
		
		switch ($type)
		{
		  case 1: $title='Admin';break;
		  case 2: $title='Intern';break;
		  case 3: $title='Extern';break;
		  default:$title='Overig';break;
		}
		
		echo '
			  <tr>
			    <td>
				  <center>
				    <a href="index.php?page=geveraanpassen&GID='.$id.'">
					  <img src="images/edit_icon.gif" border="0" />
					</a>
			 	  </center>
				</td>
			    <td>
		          <center>
					<a href="index.php?page=geveroverzicht&verwijderen=y&id='.$id.'"onCLick="return confirm(';
																												echo "'Weet u het zeker?')";
					echo ' title="Verwijderen?">
					  <img src="images/delete_icon.gif" alt="Verwijderen?" border="0">
					</a>
				  </center>
				</td>
			 ';
		
		if($row['Archief'] !=0)
		{
		  echo ' 
		        <td>
				  <font color="red">'.$naam.'&nbsp;</font>
				</td>
				<td>
				  <font color="red">'.$voornaam.'&nbsp;</font>
				</td>
			 ';
		}
		else
		{
		  echo '
			    <td>'.$naam.'&nbsp;</td>
			    <td>'.$voornaam.'&nbsp;</td>
			 ';
		}
		
		echo '
				<td>'.$email.'</td>
			    <td>
				  <a title="'.$title.'">'.$type.'</a>
				</td>
			    <td>'.$bedrijf.'</td>
			    <td>'.$straat.'</td>
			    <td>'.$pcode.'</td>
			    <td>'.$wplaats.'</td>
			    <td>'.$Tel.'</td>
			  </tr>';
    }
	echo '</table>';
  }
}
?>