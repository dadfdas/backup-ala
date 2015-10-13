<?php
/**
*  Docent bewerken
*  Deze pagina bevat de functie: edit_student()
*  Deze pagina gebruikt de functie:
*    quote_smart()
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
  <?php
  $replacer="";
  if(isset($_POST['cancel']))
  {
    echo("<meta http-equiv='REFRESH' content='0;URL=index.php?page=user'> ");	
  }	
  elseif (isset($_POST['submit']))
  {
	// Maak een errorMessage array aan om de fout berichten in op te slaan
	$errorMessage = Array();
	// errorFound standaard op false
	$errorFound   = false;

	if ( empty($_POST['Voornaam']) || empty($_POST['Achternaam']) ) {
		$errorMessage[] = "Voor of achternaam is niet ingevuld.";
		$errorFound = true;
	}
	if ( empty($_POST['Email']) || !isEmail($_POST['Email']) ) {
		$errorMessage[] = "Email adres is niet juist.";
		$errorFound = true;
	}
	if ( empty($_POST['IDStudent']) || !ctype_digit($_POST['IDStudent']) ) {
		$errorMessage[] = "Student nummer is leeg of bestaat niet alleen uit cijfers.";
		$errorFound = true;
	}
	
	// Error gevonden?
	if ( $errorFound ) {
		$size = count($errorMessage);
		$error_mes = '';
		
		for( $i=0; $i < $size; $i++ ) {
			$error_mes .= $errorMessage[$i].'<br />';
		}
		
		$fields = $_POST;
		edit_student('', $fields, $error_mes);
	}
	else {
		if ( isset($_POST['Archief']) ) {
			$archief = 1;
		}
		else {
			$archief = 0;
		}
		
		//Query
		$query="UPDATE tblstudent SET 
				Achternaam 	= '".quote_smart($_POST['Achternaam'])."', 
				Voornaam 	= '".quote_smart($_POST['Voornaam'])."',
				IDStudent	= '".quote_smart($_POST['IDStudent'])."',
				Klas		= '".quote_smart($_POST['Klas'])."',
				Email 		= '".quote_smart($_POST['Email'])."',
				Archief		= '".quote_smart($archief)."',
				Tutor		= '".quote_smart($_POST['Tutor'])."',
				Locatie		= '".quote_smart($_POST['Locatie'])."'
			  WHERE IDStudent = '".quote_smart($_POST['id'])."'";
		//Voer de Query uit
		mysql_query($query) or die ( mysql_error() );
	  
		//Echo de link terug
		echo ' Gegevens zijn opgeslagen<br />
		<meta http-equiv="REFRESH" content="3;URL=index.php?page=user">
		<a href="index.php?page=user" id="main">Terug naar overzicht</a><br />';
	}	
  }
  elseif ( isset($_GET['ID']) )
  {
    $id = $_GET['ID'];
	edit_student($id, '', '');		
  }
	
else { 
	header("Location: user.php");
}
?>
<?php
} ##else[1]

function edit_student($id, $fields, $error) {
	if ( !empty($id) && ctype_digit($id) ) {
		$query="SELECT * FROM tblstudent WHERE IDStudent= '".quote_smart($id)."' ";
		$result = mysql_query($query);
		
		$row = mysql_fetch_array($result);
		
		  $achternaam 	= $row['Achternaam'];
		  $voornaam 	= $row['Voornaam'];
		  $locatie 		= $row['Locatie'];
		  $tutor 		= $row['Tutor'];
		  $klas 		= $row['Klas'];
		  $nummer 		= $row['IDStudent'];
		  $email 		= $row['Email'];
		  $id			= $row['IDStudent'];
		  if ( isset($fields['Archief']) ) {
			  $archief = 'checked="checked"';
		  }
		  else {
			  $archief = '';
		  }

	}
	elseif ( !empty($fields) ) {
		  $achternaam 	= $fields['Achternaam'];
		  $voornaam 	= $fields['Voornaam'];
		  $locatie 		= $fields['Locatie'];
		  $tutor 		= $fields['Tutor'];
		  $klas 		= $fields['Klas'];
		  $nummer 		= $fields['IDStudent'];
		  $email 		= $fields['Email'];
		  $id			= $fields['id'];
		  
		  if ( isset($fields['Archief']) ) {
			  $archief = 'checked="checked"';
		  }
		  else {
			  $archief = '';
		  }
	}
	else {
		$error = 'Geen id en geen andere gegevens.';	
	}
	
	  if ( isset($error) ) {
			echo '<div id="fout">'.$error.'</div>';
	  }
	  echo '
			<form method="POST">
			<font color="red">* Verplicht.</font><br />
			<table id="tab">
			  <tr>
				<td>Achternaam:</td>
				<td><input type="text" value="'.$achternaam.'" name="Achternaam" size="50"></td>
				<td><font color="red">*</font></td>
			  </tr>
				<td>Voornaam:</td>
				<td><input type="text" value="'.$voornaam.'" name="Voornaam" size="50"></td>
				<td><font color="red">*</font></td>
			  </tr>
			  <tr>
				<td>Locatie:</td>
				<td><input type="text" value="'.$locatie.'" name="Locatie" size="50"></td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td>Klas:</td>
				<td><input type="text" value="'.$klas.'" name="Klas" size="50"></td>
				<td><font color="red">*</font></td>
			  </tr>
			  <tr>
				<td>Studentnummer:</td>
				<td><input type="text" value="'.$nummer.'" name="IDStudent" size="50" maxlength="5"></td>
				<td><font color="red">*</font></td>
			  </tr>
			  <tr>
			 	<td>Email:</td> 
				<td><input type="text" value="'.$email.'" name="Email" size="50"></td>
				<td><font color="red">*</font></td>
			  </tr>
			  <tr>
			 	<td>Tutor:</td> 
				<td><input type="text" value="'.$tutor.'" name="Tutor" size="50"></td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td>Archief:</td>
				<td><input type="checkbox" '.$archief.' value="1" name="Archief"></td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td valign="top" colspan="3"><center><input type="submit" value="Aanpassen" name="submit">
				<input type="submit" name="cancel" value="Annuleren"></center>
			  </tr>
			</table>
			<input type="hidden" value="'.$id.'" name="id">
			</form>
			';
}
?>