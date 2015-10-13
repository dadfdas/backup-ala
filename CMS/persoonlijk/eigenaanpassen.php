<?php
/**
*  Wachtwoord aanpassen.
*  Deze pagina bevat de functie: show_form_edit()
*  Deze pagina gebruikt de functie:
*    -
*/
if (!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{ ##Deze Else[1] wordt onderaan afgesloten!
  ?>
  <p><b>Persoonlijke Gegevens Aanpassen</b></p>
  <div id='main'>
    <?php
	if(!isset($_POST['submit']))
	{ ## controleer of er wat wordt aangepast
	  show_form_edit("<B>Wachtwoord aanpassen:</B>");
	}
	else {
		if ( empty($_POST['pwd_old']) || empty($_POST['pwd_new']) || empty($_POST['pwd_new_sec']) ) {
			show_form_edit("Niet alle velden zijn ingevuld.");  
		}
	  	else {
			  $pwd_old = md5($_POST['pwd_old']);
			  $pwd_new = md5($_POST['pwd_new']);
			  $pwd_new_sec = md5($_POST['pwd_new_sec']);
				
			  $SQL=mysql_query("SELECT * FROM tblgever WHERE IDgever=".$_SESSION['IDGever']."");		
			  $row = mysql_fetch_array($SQL);
			  $saved_pwd = $row['Wachtwoord'];
			  
			  if ($pwd_old != $saved_pwd)
			  {
				show_form_edit("<B>Het ingevoerde wachtwoord komt niet overeen met het opgeslagen wachtwoord!");
			  }
			  else
			  {
				if ($pwd_new_sec != $pwd_new)
				{
				  show_form_edit("<B> Het bevestigingsveld komt niet overeen met het veld 'nieuw wachtwoord'!</B>");
				}
				else
				{
				  $SQL = ("UPDATE tblgever SET Wachtwoord = '$pwd_new' WHERE IDGever=".$_SESSION['IDGever']."");
				  mysql_query($SQL) or DIE("Er is een fout opgetreden: <BR> " .mysql_error()."");
				  echo("Het wachtwoord is succesvol aangepast!");
				}
			  }
	  	}
	}
    ?>
    <?php
  } ##Else[1]

  function show_form_edit($message)	{
    echo '<div id="fout">'.$message.'</div>';
	echo'
	     <form method="POST" action="">
	       <table id="tabel">
		     <tr>
			   <td>Wachtwoord:</td>
			   <td>
			     <input type="password" name="pwd_old">
			   </td>
		     </tr>
			 <tr>
			   <td>Nieuw Wachtwoord:</td>
			   <td>
			     <input type="password" name="pwd_new">
			   </td>
		     </tr>
			 <tr>
			   <td>Bevestig Wachtwoord:</td>
			   <td>
			     <input type="password" name="pwd_new_sec">
			   </td>
		     </tr>
			 <tr>
			   <td colspan="2">
			     <input type="submit" value="Aanpassen" name="submit"> 
			   </td>
		     </tr>
	       </table>
	     </form>
	    ';
  }