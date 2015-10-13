<?php
/**
*  Leerlingen overzicht, verwijderen en zoeken.
*  Deze pagina bevat de functie: tabel()
*  Deze pagina gebruikt de functie:
*    staan in: inc_functions.php
*/

//Controleer eerst of er al een session is (!)
if (!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{	## Else[1] (deze wordt onderin afgesloten)
  ?>
  <div id='main'>	
    <p><b>Studenten</b></p>
	Zoek een student:<br />
	<form method="POST" action="">
	  <input type="text" name="zoek" size="37"> 
	  <input type="submit" name="search" value="Zoek student">
	</form>
  <?
  if(isset($_GET['verwijderen'])) 
  { 
    if ( $_GET['verwijderen'] == "y" && ctype_digit($_GET['id']) ) 
    { 
	
	  	$q_student = "DELETE FROM tblstudent WHERE IDStudent = '". quote_smart($_GET['id'])."'";
		//Voer de Query uit
	  	mysql_query($q_student) or die("Student kon niet worden verwijderd: <BR> 
		<div id='fout'>".mysql_error()."</div>");
		
	  	$q_insch = "DELETE FROM tblinschrijvingen WHERE IDStudent = '". quote_smart($_GET['id'])."'";
		//Voer de Query uit
	  	mysql_query($q_insch) or die("Student kon niet worden verwijderd: <BR> 
		<div id='fout'>".mysql_error()."</div>");		
		
		echo '<div id="goed">Verwijderen van de student met daarbij zijn inschrijvingen bij activiteiten is gelukt.</div>';
		
    } 
  }  
  
  if(isset($_POST['search'])) 
  {
    $search=$_POST['zoek'];
	$query = "SELECT * FROM tblstudent WHERE 
						 	( Achternaam LIKE '%".quote_smart($search)."%'  )
						 OR (Voornaam LIKE '%".quote_smart($search)."%')
						 OR ( IDStudent LIKE '%".quote_smart($search)."%' )
	                      ORDER BY Archief,Achternaam,Voornaam,IDStudent";
	$result = mysql_query($query) or die( mysql_error() ); 
	
	if ( !mysql_num_rows($result) )
	{
	  echo '<center><p id="fout">Er zijn geen resultaten gevonden met het trefwood: '.$search.', probeer opnieuw</p></center>';
	  $query = "SELECT * FROM tblstudent  ORDER BY Archief, Achternaam";
	  tabel($query);
	}
	else
	{
	  echo '<center><p id="goed">U zocht op "<i>'.$search.'"</i>,
			en deze resultaten zijn gevonden:</p></center>
			<div id="margin">';			
	  tabel($query);
	}	
  }
  else
  {
	$query = "SELECT * FROM tblstudent ORDER BY Archief, Achternaam, Voornaam, IDStudent";
	tabel($query);
  } 
  ?>
  </div>
  <?php
} ##Else[1]

function tabel($query)
{
  echo '
		<table id="tab">
		  <thead>
			<tr>
			  <td>Edit</td>
			  <td>Delete</td>
			  <td>Student</td>
			  <td> Achternaam</td>
			  <td> Voornaam</td>
			  <td> Locatie</td>
			  <td> Klas</td>
			  <td> Tutor</td>
			  <td> E-mail</td>
			  <td> Archief</td>
			</tr>
		  </thead>
		  <tbody>';
  
	$result = mysql_query($query) or die( mysql_error() ); 
	
  while( $row = mysql_fetch_assoc($result) ) 
  {
    $id = $row['IDStudent'];
	$achternaam = $row['Achternaam'];
	$voornaam = $row['Voornaam'];
	$nummer = $row['IDStudent'];
	if (empty($row['Klas']) ) { 
		$klas = '&nbsp;';
	} else {
		$klas = $row['Klas'];	
	}
	if (empty($row['Locatie']) ) { 
		$locatie = '&nbsp;';
	} else {
		$locatie = $row['Locatie'];	
	}
	if (empty($row['Tutor']) ) { 
		$tutor = '&nbsp;';
	} else {
		$tutor = $row['Tutor'];	
	}
	
	$email = $row['Email'];
	$email = '<a href="mailto:'.$email.'?subject=kennisbus.nl">'.$email.'</a>';

	switch ($row['Archief'])
	{
	  case 1 : $archief = "Ja";break;
	  case 0 : $archief = "Nee";break;
	}
	
	echo '<tr>
		    <td>
			  <a href="index.php?page=user_edit&ID='.$row['IDStudent'].'"> 
			    <img src="images/edit_icon.gif" border="0">
			  </a>
			</td>
		    <td>
			 <center>
			  <a href="index.php?page=user&verwijderen=y&id='.$id.'" onCLick="return confirm(';
																	echo "'Weet u het zeker?')";
					echo '" title="Verwijderen?">
				<img src="images/delete_icon.gif" alt="Verwijderen?" border="0">
			  </a>
			 </center>
		    </td>
			<td>'.$nummer.'</td>
			<td>'.$achternaam.'</td>
			<td>'.$voornaam.'</td>
			<td>'.$locatie.'</td>
			<td>'.$klas.'</td>
			<td>'.$tutor.'</td>
			<td>'.$email.'</td>
			<td>'.$archief.'</td>
		  </tr>';
  }
  echo ' <tbody>
 		</table>';
}
?>