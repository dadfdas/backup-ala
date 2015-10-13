<?php
/**
*  Activiteit aanpassen
*  Deze pagina bevat de functie: -
*  Deze pagina gebruikt de functie:
*    open_page(), datum(), quote_smart()   staan in: inc_functions.php
*/
#Dit is nodig om warnings uit te schakelen
error_reporting(E_ERROR | E_PARSE);
session_start();
$session_id = session_id();
// Bestand laden die database connectie bevast en links naar de functie bestanden.
require_once('includes/inc_data.php'); 
?>
<html>
  <head>
    <title>Kennisbus.nl :: Knowledge is power!</title>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="includes/default.css" type="text/css" rel="stylesheet" />
    <script src="includes/getweek.js" type="text/javascript"></script>
  </head>
  <body onLoad="load()">
    <center>
      <table id="Table_01" width="938" border="0" cellpadding="0" cellspacing="0">
	    <tr>
		  <td colspan="2" background="images/logo_left.jpg" width="524" height="170" valign="top">
		    <a href="index.php?page=main"><img src="images/transparent.gif" title="home" border="0" align="left" /></a>
		  </td>
		  <td colspan="2" background="images/logo_right.jpg" width="414" height="170" valign="top">
			<div ID="subsection">
			  <center><br>
			    <?php
				  datum();
			    ?>
			  </center>
			</div>
		  </td>
	    </tr>	
	    <tr>
		  <td colspan="4" background="images/topbar.jpg" width="938" height="20" valign="top">
		    <center>
			  <form method="get" action="<?php $_SERVER['PHP_SELF'] ?>">
			    <div id="main"><input type="text" name="search" id="search_input"><input type="submit" value=" zoek" id="search_submit">
			  </form>
			</center>
		  </td>
		</tr>
	    <tr>
		  <td background="images/menu.jpg" width="80" height="100%" valign="top">
		  	<div ID="menu">
            <?php
				if ( isset($_GET['l']) && $_GET['l'] ==0 ) {
				?>
                <script type="text/javascript">
					function load()	{
						getWeek(1);
					}
				</script>
                <?php	
					$loc_gouda	= 'selected="selected"';
					$loc_alle = '';
					$loc_zoet = '';
				}
				elseif ( isset($_GET['l']) && $_GET['l'] == 1 ) {
				?>
                <script type="text/javascript">
					function load()	{
						getWeek(2);
					}
				</script>
                <?php
					$loc_gouda	= '';
					$loc_alle = '';
					$loc_zoet = 'selected="selected"';	
				}
				else {
				// Niets geselecteerd dan alles. Dus locatie 3 betekent alles
				?>
                <script type="text/javascript">
					function load()	{
						getWeek(3);
					}
				</script>
                <?php
					$loc_alle = 'selected="selected"';	
					$loc_gouda = '';
					$loc_zoet = '';
				}
			?>
		  	  <div id="head">Menu</div>
              <form id="menuform">
			    <a href="index.php?page=main" title="home">Home</a><br>
			    <select onChange="getWeek(document.getElementById('locatie').value);" id="locatie" name="locatie">
                    <option <?php echo $loc_alle; ?> value="3">Alle locaties</option>
                    <option <?php echo $loc_gouda; ?> value="1">Gouda</option>
                    <option <?php echo $loc_zoet;	 ?> value="2">Zoetermeer</option>
				</select>
                <div id="weken">
			    <select>
                    <option selected="selected" value="">Week</option>
				</select>
                </div>
				<a href="index.php?page=animo" title="Animo">Animo</a><br>
				<a href="index.php?page=zoek" title="Zoeken">Zoeken</a><br>
				<a href="index.php?page=aanvraag" title="Aanvragen">Aanvragen</a><br>
				<a href="index.php?page=info" title="Info">Info</a><br>
               </form>
			  </div>
			</div>
		  </td>
		  <td colspan="4" background="images/filler.jpg" width="733" height="100%" valign="top">
		    <div id="margin" >
              <?php
	          //Controleer of er een pagina word opgevraagd
              if (isset($_GET['page']))
			  {		
                $pagina = $_GET['page'];
		        switch ($pagina)
                {
			      case 'index':
				    open_page('main');break;
			      case 'infoact':
				    include('infoact.php');break;
			      case 'infoanimo':
				    include('infoanimo.php');break;
				  case 'animo':
				  	include('animo.php');break;  
			      case 'zoek':
				    include('zoek.php');break;
			      case 'aanvraag':
				    include('aanvraag.php');break;
			      case 'accept':
				    //Check in the database if the code exists... else: say that the code isn't correct ;)
				    $code = $_GET['code'];
				    $check = mysql_query("SELECT * FROM tblinschrijvingen WHERE code='".quote_smart($code)."'");

				    if ( !mysql_num_rows($check) )
					{
					  echo "<div id='main'>Deze code kwam niet overeen met een code in de database....</div>\n
					        <meta http-equiv='REFRESH' content='2;URL=index.php'>";
				    }
				    else { 
				      //Doe de MySQLQuery om Activated op 1 te zetten.....
				      //Eerst ff kijken bij welk ActID de code hoort
					  $row = mysql_fetch_assoc($check);
					  
				      $as = mysql_query("SELECT * FROM tblinschrijvingen WHERE IDactiviteit = '".$row['IDActiviteit']."' AND Activated = 1 ");
				      $aanmeldingen = mysql_num_rows($as);
				      // Geef het nummer Numrows + 1
				      $aanmelding = $aanmeldingen + 1;
					  
						// Aantal max aanmeldingen ophalen
						$q_activiteit = mysql_query("
													SELECT
														IDActiviteit,
														Limiet
													FROM
														tblactiviteiten
													WHERE
														IDActiviteit = '".$row['IDActiviteit']."'
													");
						$f_act = mysql_fetch_assoc($q_activiteit);
					  
					  if ( $f_act['onbeperkt'] == 0 && $aanmeldingen == $f_act['Limiet'] ) {
							echo '<div id="main">De activiteit is vol, je kunt je niet meer aanmelden.</div>';
					  }
					  else {
						  //Update de table
						  mysql_query("UPDATE tblinschrijvingen SET `Activated`=1, `Nummer` = '".quote_smart($aanmelding)."'
									   WHERE code='".quote_smart($code)."'");
						  echo("<div id='main'>U bent nu ingeschreven voor de activiteit!</div>\n
								<meta http-equiv='REFRESH' content='2;URL=index.php'>");
					  }
				    }
				    break;
					
			      case 'uitschrijven':
			        echo '<div id="main">';
				    //Check in the database if the code exists... else: say that the code isn't correct ;)
				    $code = $_GET['code'];
				    $check = mysql_query("SELECT * FROM tblinschrijvingen WHERE code='".quote_smart($code)."'");

				    if ( !mysql_num_rows($check) )
					{
					  Echo("<div id='main'>Deze code kwam niet overeen met een code in de database....</div>\n
					        <meta http-equiv='REFRESH' content='2;URL=index.php'>");
				    }
				    else
					{ 
				      //Doe de MySQLQuery om Activated op 1 te zetten.....
				      $code = $_GET['code'];
				      mysql_query("DELETE FROM `tblinschrijvingen` WHERE code='".quote_smart($code)."' LIMIT 1") 
					  or die (mysql_error());
					  
				      echo("<div id='main'>U bent nu uitgeschreven voor de activiteit!</div>\n
					        <meta http-equiv='REFRESH' content='2;URL=index.php'>");
				    }
				    break;
			        default:
				    open_page($pagina);	break;
                }
		      }
	          //Controleer of er een week nummer word opgevraagd
			  //  $_GET['l'] is een L
	          elseif (isset($_GET['week']) && isset($_GET['l']) && isset($_GET['jaar']) )
			  {
		        fetch_rows($_GET['week'],$_GET['jaar'],$_GET['l']);
			  }
	          //Controleer of er een zoekactie word gedaan
	          elseif (isset($_GET['search']))
			  {
		        $search = $_GET['search'];
		        if (!empty ($search))
				{
			      search($search);
		        }
		        else
				{ 
				  echo '<div id="main">Geen zoekgegevens opgegeven!';
		        }
	          }	//Controleer of er een archief-zoekactie word gedaan
	            //Als alle mogelijkheden niet werken; laad de default pagina
	          else
			  {
		        open_page('main');
	          }
              ?>
		    </div>
		  </td>
	    </tr>
		<tr>
		  <td colspan='4' background='images/footer.jpg' width='938' height='51' valign='bottom'>
		    <div id='footer'>
		      &copy; Idcollege <?php echo(date('Y')); ?><br>
			</div>
		  </td>
	    </tr>
	    <tr>
		  <td>
		    <img src='images/spacer.gif' width='205' height='1'>
		  </td>
		  <td>
		    <img src='images/spacer.gif' width='317' height='1'>
		  </td>
		  <td>
		  	<img src='images/spacer.gif' width='238' height='1'>
		  </td>
		  <td>
		  	<img src='images/spacer.gif' width='178' height='1'>
		  </td>
 	    </tr>
      </table>
    </center>
  </body>
</html>