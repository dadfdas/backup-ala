<?php
/**
*   Inloggen van beheerders en het weergeven van de pagina's
*    Bij het inloggen worden alle verlopen activiteiten in het achief gezet (regel 160 en 211) 
*    
*/
// Sessie starten
session_start();
// Configuratie bestanden laden
include_once('../includes/inc_data.php');

if( isset($_GET['uitloggen']) )
{
  // Als de user wilt uitloggen...
  echo "<center><br />U bent nu uitgelogd <br />\n
  U wordt nu automatisch teruggestuurd naar de Index.<br />\n
  <meta http-equiv='REFRESH' content='2;URL=index.php'></center>";
  session_destroy();
  exit();
}
else
{ ## Else[1]
// Sluit de Else statement na alles af!
?>
<html>
  <head>
  
<script language="javascript" type="text/javascript">
	
Date.prototype.getWeek = function() {
	var onejan = new Date(this.getFullYear(),0,1);
	return Math.ceil((((this - onejan) / 86400000) + onejan.getDay())/7);
}	

function getWeekNumber(){
	var d = document.getElementById("dag").value;
	var m = parseInt(document.getElementById("maand").value)-1;
	var y = document.getElementById("jaar").value;
	var today = new Date(y,m,d); 
	var weekno = today.getWeek();
	document.getElementById("WeekNumber").innerHTML = " Week: " + weekno + "";
}
</script>
    <script type="text/javascript">
	  var item ='';
	  function Toggle(item) 
	  {
	    var key;
		var visible;
		var obj;
		obj=document.getElementById(item);
		visible=(obj.style.display!="none")
		key=document.getElementById("x" + item);
		if (visible) 
		{
		  obj.style.display="none";
		  key.innerHTML="[+]";
		} 
		else 
		{
		  obj.style.display="block";
		  key.innerHTML="[-]";
		}
	  }
    </script>
	<title>- CMS - </title>
	<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
	<link href='inc/default.css' type='text/css' rel='stylesheet'>
	<script language="javascript" type="text/javascript">
	<!--
	function popitup(url) 
	{
	  newwindow=window.open(url,'name','height=550,width=1000');
	  if (window.focus) 
	  {
	  	newwindow.focus()
	  }
	  return false;
	}

	// -->
	</script>
  <SCRIPT LANGUAGE="JavaScript">
  <!-- Idea by:  Nic Wolfe (Nic@TimelapseProductions.com) -->
  <!-- Web URL:  http://fineline.xs.mw -->

  <!-- This script and many more are available free online at -->
  <!-- The JavaScript Source!! http://javascript.internet.com -->

  <!-- Begin
  function popUp(URL) 
  {
    day = new Date();
    id = day.getTime();
    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=700,height=500,left = 162,top = 134');");
  }
  // End -->
  </script>
    <!-- Calendar Popup -->
    <script type="text/javascript" src="inc/CalendarPopup.js"></script>
  <script language="JavaScript" type="text/javascript" src="../editor/wysiwyg.js"></script>

</head>
  <body>
    <table border="1" width="100%">
	  <tr>
		<td colspan="2" height="10%" valign="top"> 		  
		  <center>
		    <br />
			<?php
			//Eerst controleren of er niets in de Session staat:
			if (empty ($_SESSION['user']))
			{
			  //Als dat zo is, dan is het goed, anders...
			  //Controleer de POST, misschien dat er ingelogd wordt
			  if (empty ($_POST['username']))
			  {
			    //Als deze ook 'true' oplevert:
				// Echo het form
				show_form("U bent nog niet ingelogd.");
				die();
			  }
			  else
			  { 
			  	//Als de POST niet leeg is ....
				//Controleer naam en wachtwoord:
				$username = $_POST['username'];
				//Controleer of het wachtwoord gevuld is
				if (!empty ($_POST['pwd']))
				{
				  $pwd = $_POST['pwd'];
				  //MD5-HASH het wachtwoord
				  $pwd = md5($pwd);
				  //Maak de connectie naar de Database en controleer de naam
				  $result = mysql_query("SELECT * FROM tblgever WHERE Naam = '".$username."' LIMIT 1");
				  $numrows = mysql_num_rows($result);
				  // Nieuwe gebruikersnaam
				  $result_gebruikersnaam = mysql_query("SELECT * FROM tblgever WHERE gebruikersnaam = '".$username."' LIMIT 1");
				  $numrows_2 = mysql_num_rows($result_gebruikersnaam);
				  
				  // Controleren geen resulataat bij naam en gebruikersnaam
				  if ($numrows == 0 && $numrows_2 == 0)
				  {
				    show_form("De naam die u heeft ingevoerd komt niet voor in de database.");
				  }
				  else
				  {
					// Als er wel een resultaat is bij gebruikersnaam
				  	if ( $numrows_2 != 0 ) {
						$f_gebruiker = mysql_fetch_assoc($result_gebruikersnaam);
						
						if ($pwd != $f_gebruiker['Wachtwoord'])
						{
						  //Het wachtwoord is foutief
						  show_form("Het wachtwoord dat u heeft ingevoerd is niet correct in 
									 combinatie met de gebruikersnaam!");
						  die();
						}
						else
						{
						  $usertype = $f_gebruiker['IDType'];
						  switch ($usertype)
						  {
							case 1: $type= eregi_replace("1","Admin",$usertype);break;
							case 2: $type= eregi_replace("2","Intern",$usertype);break;
							case 3: show_form("U heeft geen inlogrechten!"); die(); break;
						  }									
						  $_SESSION['user'] = $username;
						  $_SESSION['usertype'] = $type;
						  $_SESSION['IDType'] = $usertype;
						  $_SESSION['IDGever'] = $f_gebruiker['IDGever'];
						  
						  echo "<br />U bent ingelogd als: " .$_SESSION['user']. "<br />
								U wordt nu automatisch doorgestuurd naar het CMS.<BR />
								Of druk F5 (refresh) om de pagina opnieuw te laden.
								<meta http-equiv='REFRESH' content='2;URL=index.php'>";
										
						}
					}
					else {
					  	$row = mysql_fetch_assoc($result);
					  	// Controleren of er ook een gebruikersnaam is ingesteld.
					  	if ( !empty($row['gebruikersnaam']) ) {
							show_form("U probeerd in te loggen met de gebruikersnaam ".$row['Naam'].". Er is echter een ander aparte gebruikersnaam voor u ingesteld. Namelijk: '".$row['gebruikersnaam']."'. Hiermee kunt u vanaf nu af aan mee inloggen.");
											   
						}
						else {
				    	
							$Wachtwoord = $row['Wachtwoord'];
							if ($pwd != $Wachtwoord)
							{
							  //Het wachtwoord is foutief
							  show_form("Het wachtwoord dat u heeft ingevoerd is niet correct in 
										 combinatie met de gebruikersnaam!");
							}
							else {
							  $usertype = $row['IDType'];
							  switch ($usertype)
							  {
								case 1: $type= eregi_replace("1","Admin",$usertype);break;
								case 2: $type= eregi_replace("2","Intern",$usertype);break;
								case 3: show_form("U heeft geen inlogrechten!"); die(); break;
							  }									
							  $_SESSION['user'] = $username;
							  $_SESSION['usertype'] = $type;
							  $_SESSION['IDType'] = $usertype;
							  $_SESSION['IDGever'] = $row['IDGever'];
							  
							  echo "<br />U bent ingelogd als: " .$_SESSION['user']. "<br />
									U wordt nu automatisch doorgestuurd naar het CMS.<BR /> \n
									Of druk F5 (refresh) om de pagina opnieuw te laden.
									<meta http-equiv='REFRESH' content='2;URL=index.php'>";
							}
						}
					}
				  }
				}
				else
				{
				  // Er is geen wachtwoord ingevoerd
				  show_form("Er is geen wachtwoord ingevoerd!");
				  die();
				}
			  }
			}
			else
			{ 
			  ## Else[2]
			  // Als de SESSION gevuld is,  Genereer Menu e.d.
			  // Deze ELSE wordt ONDERAAN afgesloten(!)
		    ?>
		    <div id='head'>Control Management System : Versie 3</div>
			Ingelogd als: <B><?PHP echo($_SESSION['user']);?> [<?php echo($_SESSION['usertype']); ?>]</B>
			<?php datum(); ?>
		  </center>
		</td>
	</tr>
	<tr>
	  <td width="15%" valign="top">
	    <div id='menu'>
		  <DIV id='head'>Menu</DIV>
		  <?php
		  switch ($_SESSION['IDType'])	{
		    case 1: generate_menu(1);break;
		    case 2: generate_menu(2);break;
		  }
		  ?>
		  <b>Overig</b><br>
		  -> <a href='index.php'>Help</a><br>
		  -> <a href='index.php?page=eigenaanpassen'>Wachtwoord</a><br>
		  -> <a href='index.php?uitloggen=ok'>Uitloggen</a><br>
		  </div>
	    </div>	
	  </td>
	  <td valign="top"> 
	    <div id="admin">
		  <div ><!--style="height:600px;width:95%; overflow:auto"-->
		  <?php
		  if (isset($_GET['page']))	{
		  
		    $pagina = $_GET['page'];
			switch ($pagina)
			{
			  case 'actoverzicht':
			    include('act/actoverzicht.php');	break;
			  case 'animooverzicht':
			    include('act/animooverzicht.php');	break;			    
			  case 'actanimo':
			    include('act/actanimo.php');		break;
			  case 'animoaanpassen':
			    include('act/animoaanpassen.php');	break;
			  case 'actzoek':
			    include('act/actzoeken.php');		break;
			  case 'acttoevoegen':
			    include('act/acttoevoegen.php');	break;
			  case 'actaanpassen':
			    include('act/actaanpassen.php');	break;
			  case 'aanmeldingen':
			    include('act/aanmeldingen.php');	break;
			  case 'geveroverzicht':
			    include('gever/geveroverzicht.php');break;
			  case 'gevertoevoegen':
			    include('gever/gevertoevoegen.php');break;
			  case 'geveraanpassen':
			    include('gever/geveraanpassen.php');break;
			  case 'user':
			    include('user/user.php');			break;
			  case 'user_add':
			    include('user/user_add.php');		break;
			  case 'user_import':
			    include('user/user_import.php');	break;
			  case 'user_edit':
			    include('user/user_edit.php');		break;
			  case 'tekst_edit':
			    include('tekst/tekst_edit.php');	break;
			  case 'eigenaanpassen':
			    include('persoonlijk/eigenaanpassen.php');break;
			  default:
				echo $pagina." bestaat niet.";		break;
			}
		  }
		  else
		  {	
		    include("help.php");
		  }
	 	  ?>
	    </td>
	  </tr>
    </table>
  </body>
</html>
<?php
  } //Sluit ELSE[2] Af
} // Sluit ELSE[1] Af
?>	
