<?php 
/**
* 	Formulier om suggestie of opmerking te sturen over de kennisbus.
* 	Daarvoor moet een bij het systeem bekend zijn student nummer worden ingevoerd. 
*  Deze pagina gebruikt de functie:
*     quote_smart() staat in: inc_functions.php
*/
  if ( $_SERVER['REQUEST_METHOD'] == "POST" ) {
   
    if( empty($_POST['studnr']) || !ctype_digit($_POST['studnr']) )
	{  
	  echo '<div id="main">U heeft geen studentnummer ingevuld!<br />';  
	  echo "<a href='javascript:history.back();' style='color: black; text-decoration:none;'>
	          Klik hier om terug te gaan!
			</a></div>";  
	}  
	elseif( empty($_POST['bericht']) )
	{  
	  echo '<div id="main">U heeft geen bericht ingevuld!<br />';
	  echo "<a href='javascript:history.back();' style='color: black; text-decoration:none;'>
	          Klik hier om terug te gaan!
			</a></div>";  
	}
	else
	{
		$naar = "info@kennisbus.nl";
		
		$sql = mysql_query("SELECT * FROM tblstudent WHERE IDStudent='".quote_smart($_POST['studnr'])."'");
	  
	  if ( mysql_num_rows($sql) )
	  {
		$row = mysql_fetch_assoc($sql);
		
		$naam = $row['Achternaam'] . ", " . $row['Voornaam'];
		
		$verzonden_bericht = nl2br($_POST['bericht']); 
		$bericht = " 
				   <html> 
				     <head> 
				       <title>Suggestie ingestuurd van de website.</title> 
				     </head> 
				     <body> 
				       ".$naam." heeft u een email verzonden.
				       <br>
				       <br>
				       <hr width='100%'>
				       <TABLE border='0' width='100%'> 
				         <TR> 
					       <TD width='50%' valign='top'>".$verzonden_bericht."</TD> 
				         </TR> 
				       </TABLE> 
				       <hr width='100%'>
				     </body> 
				   </html> 
				   ";  
		
		if(strtoupper(substr(PHP_OS,0,3)=='WIN'))
		{
		  $eol="\r\n";
		  $sol="\n";
		}
		elseif(strtoupper(substr(PHP_OS,0,3)=='MAC'))
		{
          $eol="\r";
		}
		else
		{
		  $eol="\n";
		}
		
		$Servername = "PHPMAILSERVER";
		$Momentn = mktime().".".md5(rand(1000,9999));
		$headers      .= 'From: "' . $naam . '" < ' . $studnr . '@ict-idcollege.nl>'.$eol;
		$headers      .= "Message-ID: <".$Momentn."@".$Servername.">".$eol;
		$headers      .= 'Date: '.date('r').$eol;
		$headers      .= 'Sender-IP: '.$_SERVER["REMOTE_ADDR"].$eol;
		$headers      .= 'X-Mailser: iPublications Adv.PHP Mailer 1.6'.$eol;
		$headers      .= 'MIME-Version: 1.0'.$eol;
		$headers      .= "Content-Type: text/html; charset=iso-8859-1".$eol;
		$headers      .= "Content-Transfer-Encoding: 8-bit".$eol.$eol;
		
		if(mail($naar,"Suggestie ingestuurd van de website." ,$bericht,$headers))
		{  
		  echo '
				<div id="main">
				  <center>
				    Uw Bericht is naar '.$naar.' verstuurd! Bedankt voor het meedenken.<br>
					<meta http-equiv=\"REFRESH\" content=\"2;URL=index.php\">
				  </center>
				</div>';
		}
		else
		{  
		  echo '
				<div id="main">
				  <center>
				    Uw Bericht is helaas niet verstuurd!<br>
					<meta http-equiv=\"REFRESH\" content=\"2;URL=index.php?page=aanvraag\">
				  </center>
				</div>';
		}  
	  }
	  else
	  {
	    echo '
		      <div id="main">
			    <center>
				  Het leerlingnummer bestaat niet.<br>
				  <meta http-equiv=\"REFRESH\" content=\"2;URL=index.php?page=aanvraag\">
				</center>
			  </div>';
	  }
	} 
  } 
  else  
  {  
    ?>
	<div id="head">Aanvragen</div>
	<div id="main">
	  Heb je ideeën voor een workshop, presentatie of excursie? <br />Meld het aan het Kennisbus team.
      <form method="post" name="email">
        <fieldset style="width: 350px;">
          <table cellpadding="3" id="main">
		    <tr> 
			  <td>
				Studentnummer:
				<input type="text" name="studnr" size="5" maxlength="5" />
				<input type="hidden" name="onderwerp" value="text voor onderwerp" />
		      </td> 
		    </tr> 
		    <tr> 
			  <td>
				Bericht:<br>
				<textarea name="bericht" cols="60" rows="5"></textarea>
			  </td>
		    <tr> 
			  <td colspan="2" align="center">
			    <input type="submit" value="stuur in" name="send" />
				<input name="reset" type="reset" value="reset" />
			  </td> 
		    </tr>
          </table>
        </fieldset>
      </form>
    <?php  
  }  
?>