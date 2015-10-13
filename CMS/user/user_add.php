<?php
/**
*  Leerling toevoegen
*  Deze pagina bevat de functie: form()
*  Deze pagina gebruikt de functie:
*    -
*/

//Controleer eerst of er al een session is (!)
if (!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{	## Else[1] (deze wordt onderin afgesloten)
  echo '<p><b>Student toevoegen</b></p>';

  if (isset($_POST['submit']))
  {
	  
  $IDStudent	= $_POST["IDStudent"];
  $achternaam	= $_POST["Achternaam"];
  $voornaam	    = $_POST["Voornaam"];
  $klas	        = $_POST["Klas"];
  $email		= $IDStudent . "@ict-idcollege.nl";
  $locatie		= $_POST['Locatie'];
  $tutor		= $_POST['Tutor'];
 
    if (empty($IDStudent) || !ctype_digit($IDStudent) ) 
	{
	  echo '<div id="fout">Er is geen studentnummer in gegeven of deze bestaat niet aleen uit cijfers.</div>';
	  $fields = $_POST;
	  form( $fields);
	} 
	elseif (empty($achternaam)) 
	{
	  echo '<div id="fout">Er is geen achternaam in gegeven!</div>';
	  $fields = $_POST;
	  form( $fields);
	} 
	elseif (empty($voornaam)) 
	{
	  echo '<div id="fout">Er is geen voornaam in gegeven!</div>';
	  $fields = $_POST;
	  form( $fields);
	} 
	elseif (empty($klas)) 
	{
	  echo '<div id="fout">Er is geen klas ingevoerd!</div>';
	  $fields = $_POST;
	  form( $fields);
	} 
	else 
	{
	  $query = "INSERT INTO `tblstudent` ( `IDStudent`,`Email`,`Achternaam`,`Voornaam`,`Klas`,`Archief`,`Locatie`,`Tutor`)
	            VALUES ('".quote_smart($IDStudent)."', '".quote_smart($email)."', 
						'".quote_smart($achternaam)."', '". quote_smart($voornaam)."', 
						'". quote_smart($klas)."', '0', '".quote_smart($locatie)."',
						'".quote_smart($tutor)."');";
      
	  mysql_query($query) or die ("De gegevens die u probeert in te voeren, bestaan waarschijnlijk al. \n <br>
	  Probeert u eens andere gegevens.");
	  
	  echo 'De volgende gegevens zijn toegevoegd aan de database:<br />
	  Studentnummer: '.$IDStudent.'<br />
	  Achternaam: '.$achternaam.'<br />
	  Voornaam: '.$voornaam.'<br />
	  Klas: '.$klas.'<br />
	  Email: '.$email.'<br />
	  Locatie: '.$locatie.'<br />
	  Tutor: '.$tutor.'
	  <meta http-equiv="REFRESH" content="4;URL=index.php?page=user">';	
	}
  }
  else
  {
    form('');
  }
} ##else[1]
?>
<?php
function form($fields) 
{
	if ( !empty($fields) ) {
	  $IDStudent	= $fields["IDStudent"];
	  $achternaam	= $fields["Achternaam"];
	  $voornaam	    = $fields["Voornaam"];
	  $klas	        = $fields["Klas"];
	  $tutor		= $fields['Tutor'];
	  $locatie		= $fields['Locatie'];
	}
	else {
	  $IDStudent	= '';
	  $achternaam	= '';
	  $voornaam	    = '';
	  $klas	        = '';
	  $tutor		= '';
	  $locatie		= '';
	}
 
  echo '<form action="index.php?page=user_add" method="post" name="Add">
		<font color="red">* Verplicht.</font><br />
           <table id="tab">
             <tr>
			   <td>Studentnummer:</td>
			   <td>
			     <input type="text" maxlength="5" name="IDStudent" value="' . $IDStudent . '" />
			   </td>
			   <td><font color="red">*</font></td>
			 </tr>
             <tr>
			   <td>Achternaam:</td>
			   <td>
			     <input type="text" name="Achternaam" value="' . $achternaam . '" />
			   </td>
			   <td><font color="red">*</font></td>
			 </tr>
             <tr>
			   <td>Voornaam:</td>
			   <td>
			     <input type="text" name="Voornaam" value="' . $voornaam . '" />
			   </td>
			   <td><font color="red">*</font></td>
			 </tr>
			  <tr>
			 	<td>Tutor:</td> 
				<td><input type="text" value="'.$tutor.'" name="Tutor" /></td>
				<td>&nbsp;</td>
			  </tr>
             <tr>
			   <td>Klas:</td>
			   <td>
			     <input type="text" name="Klas" value="' . $klas . '" />
			   </td>
			   <td><font color="red">*</font></td>
			 </tr>
			  <tr>
				<td>Locatie:</td>
				<td><input type="text" value="'.$locatie.'" name="Locatie" /></td>
				<td>&nbsp;</td>
			  </tr>
             <tr>
			   <td colspan="3">
			     <center>
				   <input type="submit" value="Toevoegen" name="submit" />
				 </center>
			   </td>
			 </tr>
           </table>
         </form>';
} 
?>