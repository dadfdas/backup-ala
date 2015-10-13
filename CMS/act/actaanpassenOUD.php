<?php
session_start();
//Controleer eerst of er al een session is (!)
if (!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{
  if(isset($_POST['s1']))
  {
    if(empty($_POST['Week']) || empty($_POST['Jaar']) || empty($_POST['Dag']) || empty($_POST['Titel']) || 
	   empty($_POST['Tijd']) || empty($_POST['Lokaal']) || empty($_POST['Trefwoord']) || empty($_POST['Limiet']))
	{
	  error_msg("Niet alle velden zijn ingevoerd!");
	}
	else
	{	
	  $fields = $_POST;
	  //-------------------------------//
	  if (empty ($fields[GeverID]))
	  {
	  	$fields[GeverID] = 0;
	  }
	
	  $ActID = $_GET['actid'];
	  //Controle van de ingevoerde velden tbt de database...
	  //------------------------------------------------------------------------------------------------------//
	  //controleer tijds notatie------//
	  $start = substr($fields[Tijd],0,5);
	  $eind = substr($fields[Tijd],6,5);
	  strtotime($start);
	  strtotime($eind);
	  $start = ereg_replace(':', '.', $start); 
	  $eind = ereg_replace(':', '.', $eind); 
	  
	  if($eind < $start)
	  { 
	    error_msg("De eindtijd is eerder dan de begintijd!");
	  }
	  else
	  {
	    $sT = mysql_query("SELECT * FROM tblactiviteiten 
		             	  WHERE `Week` = '$fields[Week]' AND `Dag` = '$fields[Dag]'
				          AND `Jaar` ='$fields[Jaar]' AND `Lokaal` = '$fields[Lokaal]'");
		$n = @mysql_num_ROWS($sT);
		
		if($n)
		{
		  $chkT = @mysql_fetch_array($sT);
		  $ostart = substr($chkT[Tijd],0,5);
		  $oeind = substr($chkT[Tijd],6,5);	  
		  strtotime($ostart);
		  strtotime($oeind);
		  strtotime($oeind);			  
		  //Als start > opgeslagen eind, sla op
		   
		  if(($eind <= $ostart))
		  {
		  	saveit($_GET['actid'],$fields);
		  }
		  elseif(($oeind <= $start))
		  { 
		  	saveit($_GET['actid'],$fields);
		  }
		  else
		  {
		  	error_msg("Pas de tijd aan!");
		  }
		}
		else
		{
		  saveit($_GET['actid'],$fields);
		}
	  }
	}
  }
  elseif(isset($_POST['s2']))
  {
    $fields = $_POST;
	if(1==9)
	{ // Hier komt het upload gedoe :P
	}
	else
	{
    //Sla de activiteit op
	  mysql_query("UPDATE `tblactiviteiten` SET 
	        	  `Info` = '$fields[Info]'
				   WHERE `IDActiviteit` = '$fields[IDActiviteit]' ") or die ("<DIV ID='fout'>". mysql_error() . "</DIV>");
	    $week = $_GET['week'];
	    $jaar = $_GET['jaar'];
	    echo("<DIV ID='goed'> De activiteit is opgeslagen! </DIV>
			<meta http-equiv='REFRESH' content='0;URL=index.php?page=actoverzicht&week=$week&jaar=$jaar'>");
	}
  }
  elseif(isset($_POST['s3']))
  {
    if(empty($_POST['Week']) || empty($_POST['Jaar']) || empty($_POST['Dag']) || empty($_POST['Titel']) || 
	   empty($_POST['Tijd']) || empty($_POST['Lokaal']) || empty($_POST['Trefwoord']) || empty($_POST['Limiet']))
	{
	  error_msg("Niet alle velden zijn ingevoerd!");
	}
	else
	{	
	  $fields = $_POST;
	  //-------------------------------//
	  if (empty ($fields[GeverID]))
	  {
	    $fields[GeverID] = 0;
	  }
	 		
	  $actnum = mysql_query("SELECT MAX(IDActiviteit) FROM tblactiviteiten") or die (mysql_error());
	  $num = mysql_fetch_row($actnum) or die (mysql_error());
	  $fields['Num']= $num[0] +1;
	  //Controle van de ingevoerde velden tbt de database...
	  //------------------------------------------------------------------------------------------------------//
	  //controleer tijds notatie------//
	  $start = substr($fields[Tijd],0,5);
	  $eind = substr($fields[Tijd],6,5);
	  strtotime($start);
	  strtotime($eind);
	  $start = ereg_replace(':', '.', $start); 
	  $eind = ereg_replace(':', '.', $eind); 	  
	
	  if($eind < $start)
	  { 
	    error_msg("De eindtijd is eerder dan de begintijd!");
	  }
	  else
	  {
	    $sT = mysql_query("SELECT * FROM tblactiviteiten 
		             	  WHERE `Week` = '$fields[Week]' AND `Dag` = '$fields[Dag]'
				          AND `Jaar` ='$fields[Jaar]' AND `Lokaal` = '$fields[Lokaal]'");
		$n = @mysql_num_ROWS($sT);
		
		if($n)
		{
		  $chkT = @mysql_fetch_array($sT);
		  $ostart = substr($chkT[Tijd],0,5);
		  $oeind = substr($chkT[Tijd],6,5);
		  strtotime($ostart);
		  strtotime($oeind);
		  strtotime($oeind);
		  //Als start > opgeslagen eind, sla op
		   
		  if(($eind <= $ostart))
		  {
		  	insertit($_GET['actid'],$fields);
		  }
		  elseif(($oeind <= $start))
		  { 
		  	insertit($_GET['actid'],$fields);
		  }
		  else
		  {
		  	error_msg("Pas de tijd aan!");
		  }
		}
		else
		{
		  insertit($_GET['actid'],$fields);
		}
	  }
	}
  }
  elseif(isset($_POST['cancel']))
  {
  	cancelactiviteit();	
  }	
  else
  {
    $SQL = mysql_query("SELECT * FROM tblactiviteiten WHERE IDActiviteit = ".$_GET['actid']);
	$fields = mysql_fetch_array($SQL);
	show_data($_GET['actid'],$_SESSION['usertype'],$fields); 
  }
}

//Functions --------------------------------------------------------------------------------------------------------------------------------------------------------

function show_data($id,$session,$fields)
{
  switch($fields['Aanmelden'])
  { 
  	case 1: $chkval = "CHECKED";break; 
  }
  switch ($session)
  {
    case "Admin": //Show form admin 
	  $verplicht = "<A STYLE=CURSOR:help; title='Dit veld is verplicht'>
	                  <img src='images/info_icon_r.gif' border='0'  height='13' WIDTH='13'>
					</A>";
	  $semi = "<A STYLE=CURSOR:help; title='Verplicht veld als controle checkbox is aangevinkt'>
	             <img src='images/info_icon.gif' border='0' height='13' WIDTH='13'>
			   </A>";
	  $niet = "<A STYLE=CURSOR:help; title='Dit veld is niet verplicht'>
	             <img src='images/info_icon_g.gif' border='0' height='13' WIDTH='13'></a>";
	  $bestand23 = $fields[Bestand];
	  
	  if(!empty($bestand23))
	  {
	    $bestand23 = "<i>$bestand23 is nu gekoppelt aan deze activiteit.</i>";
	  }
	 
	  echo("
	  <Script Language=\"JavaScript\">
	    function fillText()
		{
		  document.act_edit.Trefwoord.value=document.act_edit.Titel.value;
		}
	  </Script>
	  <FORM METHOD='POST' ACTION='' NAME='act_edit'>
	    <TABLE ID='tab'>
		  <TR>
		    <TD COLSPAN='2'>
			  <INPUT TYPE='checkbox' NAME='Aanmelden' $chkval>Aanmelden is mogelijk</INPUT>
		    </TD>
			<TD colspan='2'>
			  <a href=\"act/files.php?ID=$id\" onclick=\"return popitup('act/files.php?ID=$id')\">Bestand</a> 
			  $bestand23
			</TD>
		  </TR>
		  <TR>
		    <TD>Titel: <A STYLE=CURSOR:help; $verplicht</TD>
		    <TD COLSPAN='1'> 
		      <INPUT TYPE='text' NAME='Titel' VALUE='$fields[Titel]' SIZE='40' onchange='fillText()' MAXLENGTH='30'>                  </TD>
		    <TD WIDTH='100'> Trefwoorden: $verplicht</TD>
		    <TD COLSPAN='1'> <INPUT TYPE='text' VALUE='$fields[Trefwoord]' size='40' NAME='Trefwoord'></TD>
		  </TR>
		  <TR>						
	        <TD> Dag: <a style=CURSOR:help; $semi</TD>
	        <TD> 
	          <select name='Dag'>
		      ");
		        for ($i=1;$i<6;$i++)
		        {
		          switch ($i)
			      {
			        case 1: $day = "Maandag";break;
			        case 2: $day = "Dinsdag";break;
			        case 3: $day = "Woensdag";break;
			        case 4: $day = "Donderdag";break;
			        case 5: $day = "Vrijdag";break;
			      }
			      if ($i == $fields['Dag'])
			      {
			        echo "<option value='$i' SELECTED>$day</option>";
			      }
			      else
			      {
			        echo " <option value='$i'>$day</option>";
		          }
		        }
		      echo("
		      </select>
	        </TD>
			<TD> Tijd: 
			  <A STYLE=CURSOR:help; title='Notatie: 12:00-14:00; Dit veld is verplicht als de controle 
			    checkbox is aangevinkt'><img src='images/info_icon.gif' border='0' height='13' WIDTH='13'>
			  </A> 
			</TD>
			<TD> 
		      <INPUT TYPE='text' NAME='Tijd' VALUE='$fields[Tijd]' SIZE='40' MAXLENGTH='11'> 
			</TD>
	      </TR>
		  <TR>
		    <TD> Week: <A STYLE=CURSOR:help; $semi </TD>
		    <TD> 
		      <SELECT NAME='Week'>
			  ");
			  //Genereer de weeknummers
			    for($i=1;$i<=52;$i++)
			    {
			      if ($i <= 9) 
				  {
				    $i = "0" . $i;
				  }
				  $iYear = date('Y');
				  if ($i == $fields[Week])
				  {
				    echo("<OPTION VALUE='$i' SELECTED><B>Week $i</B></OPTION>\t\n\n\t");
				  }
				  else
				  {
				    echo("<OPTION VALUE='$i'> Week $i</OPTION>\t\n\n\t");}
				  }
			    // Ga weer door...
			    $year = date('Y');
		 	    echo("
			  </SELECT>
			</TD>
		    <TD> Jaar: <A STYLE=CURSOR:help; $semi</TD>
		    <TD> 
		      <SELECT NAME='Jaar'>
			    ");
		        for($i=$year;$i<$year+4;$i++)
			    {
			      //echo("<OPTION VALUE='$i'>$i</OPTION>\t\n\n\t");
				  if ($i == $fields[Jaar])
				  {
				    echo("<OPTION VALUE='$i' SELECTED><B>$i</B></OPTION>\t\n\n\t");
				  }
				  else
				  {
				    echo("<OPTION VALUE='$i'>$i</OPTION>\t\n\n\t");
				  }
			    }
		        echo("
			  </SELECT>
			</TD>
	      </TR>
	      <TR>
	        <TD> Lokaal: <A STYLE=CURSOR:help; $semi</TD>
		    <TD> 
		      <SELECT NAME='Plaats'>
		        <OPTION VALUE='0'>G</OPTION>
		        <OPTION VALUE='1'>Z</OPTION>
		      </SELECT> 
		    <INPUT TYPE='text' NAME='Lokaal' VALUE='$fields[Lokaal]' SIZE='10' MAXLENGTH='30'> 
		  </TD>
		  <TD> Limiet: $semi</TD>
		  <TD> <INPUT TYPE='text' NAME='Limiet' VALUE='$fields[Limiet]' SIZE='10' MAXLENGTH='3'></TD>
	    </TR>		
	    <TR>
	      <TD> Niveau:  <A STYLE=CURSOR:help; $semi</TD>
	      <TD>");
	        echo ("<SELECT name='IDNiveau'>\n\t");			
			     $SQLNiveau = mysql_query("SELECT * FROM tblniveau ORDER BY IDNiveau");
				 $ActNiveau = mysql_query("SELECT * FROM `tblactiviteiten` WHERE IDActiviteit = $id");
				 $Samenniveau = @mysql_fetch_array($ActNiveau);
				
				 while($niveau = mysql_fetch_array($SQLNiveau))
				 {
				   $idniveau = $niveau['IDNiveau'];
				   $niveautext = $niveau['Niveau'];

				   if(empty($niveautext))
				   { 
				     $niveautext = "Dummy"; 
				   }
				   if ($Samenniveau['IDNiveau'] == $niveau['IDNiveau'])
				   {
				     echo("<OPTION value='$idniveau' SELECTED>$niveautext</OPTION>\n\t");
				   }
				   else 
				   { 
				   	 echo("<OPTION value='$idniveau'>$niveautext</OPTION>\n\t");
				   }
				 }
		    echo("</SELECT>
		  </TD>
		  <TD> Soort: <A STYLE=CURSOR:help; $semi</TD>
	      <TD>");
	        echo ("<SELECT name='IDSoort'>\n\t");			
			     $SQLSoort = mysql_query("SELECT * FROM tblsoort ORDER BY IDSoort");
				 $ActSoort = mysql_query("SELECT * FROM `tblactiviteiten` WHERE IDActiviteit = $id");
				 $Samensoort = @mysql_fetch_array($ActSoort);
				
				 while($soort = mysql_fetch_array($SQLSoort))
				 {
				   $idsoort = $soort['IDSoort'];
				   $soorttext = $soort['Soort'];

				   if(empty($soorttext))
				   { 
				     $soorttext = "Dummy"; 
				   }
				   if ($Samensoort['IDSoort'] == $soort['IDSoort'])
				   {
				     echo("<OPTION value='$idsoort' SELECTED>$soorttext</OPTION>\n\t");
				   }
				   else 
				   { 
				   	 echo("<OPTION value='$idsoort'>$soorttext</OPTION>\n\t");
				   }
				 }
		    echo("</SELECT>
	     </TD>
	   </TR>
	   <TR>
	     <TD COLSPAN='1'>Docent: <A STYLE=CURSOR:help; $semi </TD>
	     <TD>
	       <SELECT NAME='GeverID'>\n\t");			
		     $SQL3 = mysql_query("SELECT * FROM tblgever ORDER BY Naam");
		     $S = mysql_query("SELECT * FROM `tblactiviteiten` WHERE IDActiviteit = $id");
		     $G = @mysql_fetch_array($S);
		     while($gever = mysql_fetch_array($SQL3))
		     {
		       $naam = $gever['Naam'];
			   $voornaam = $gever['gVoornaam'];
			   $GeverID = $gever['IDGever'];
			 
			   if(empty($naam))
			   { 
			     $naam = "Dummy"; 
			   }
			 
			   if ($G['IDGever'] == $gever['IDGever'])
			   {
			     echo("<OPTION VALUE='$GeverID' SELECTED>$naam, $voornaam </OPTION>\t\n\n\t");
			   }
			   else 
			   { 
			     echo("<OPTION VALUE='$GeverID'>$naam, $voornaam </OPTION>\t\n\n\t");
			   }
		     }
		   echo("
		   </SELECT>
	     </TD>
	     <TD> Opleiding: <A STYLE=CURSOR:help; $semi </TD>
	     <TD>");
	        echo ("<SELECT name='IDOpleiding'>\n\t");			
			     $SQLOpleiding = mysql_query("SELECT * FROM tblopleiding ORDER BY IDOpleiding");
				 $ActOpleiding = mysql_query("SELECT * FROM `tblactiviteiten` WHERE IDActiviteit = $id");
				 $Samenopleiding = @mysql_fetch_array($ActOpleiding);
				
				 while($opleiding = mysql_fetch_array($SQLOpleiding))
				 {
				   $idopleiding = $opleiding['IDOpleiding'];
				   $opleidingtext = $opleiding['Opleiding'];

				   if(empty($opleidingtext))
				   { 
				     $opleidingtext = "Dummy"; 
				   }
				   if ($Samenopleiding['IDOpleiding'] == $opleiding['IDOpleiding'])
				   {
				     echo("<OPTION value='$idopleiding' SELECTED>$opleidingtext</OPTION>\n\t");
				   }
				   else 
				   { 
				   	 echo("<OPTION value='$idopleiding'>$opleidingtext</OPTION>\n\t");
				   }
				 }
		    echo("</SELECT>
	    </TD>
      </TR>
	  <TR>
	    <TD> Informatie: <A STYLE=CURSOR:help; $niet </TD>
	    <TD COLSPAN='3'>
	      <DIV ID='wysiwyg'>
	 	    <TEXTAREA COLS='100' ROWS='10' ID='info' NAME='Info'>$fields[Info]</TEXTAREA>
		  </DIV>
		  <SCRIPT LANGUAGE='javascript1.2'>
		    generate_wysiwyg('info');
		  </SCRIPT>
	    </TD>
	  </TR>
    </TABLE>
    <CENTER>
      <INPUT TYPE='submit' NAME='s1' VALUE='Aanpassen'>
	  <INPUT TYPE='submit' NAME='s3' VALUE='Invoeren'>
	  <INPUT TYPE='reset' VALUE='Reset'>
	  <INPUT TYPE='submit' NAME='cancel' VALUE='Annuleren'>
    </CENTER>");  	  
    break;
    default: 
    echo ("
    <FORM METHOD='POST' ACTION='' NAME='frmIntern'>
      <DIV ID='head'>Activiteit - $fields[Titel]<BR /></DIV>
	  <TABLE ID='tab'>
	    <TR>
	      <TD VALIGN='top'>Dag:</TD>
		  <TD WIDTH='273'>$fields[Dag]</TD>	   
		  <TD VALIGN='top'>Tijd:</TD>
		  <TD WIDTH='273'>$fields[Tijd]</TD>
	    </TR>
	    <TR>
	      <TD VALIGN='top'>Week:</TD>
		  <TD WIDTH='273'>$fields[Week]</TD>
		  <TD VALIGN='top'>Jaar:</TD>
		  <TD WIDTH='273'>$fields[Jaar]</TD>
	    </TR>
	  </TABLE>
	  <TABLE ID='tab'>  
	    <TR>
	      <TD VALIGN='top'>Info:</TD>
		  <TD COLS PAN='5'>
		    <DIV ID='wysiwyg'>
		      <TEXTAREA COLS='100' ROWS='20' ID='info' NAME='Info' />
		  	    $fields[Info]
		      </TEXTAREA>
		    </DIV>
		  </TD>
	    </TR>
	  </TABLE>
	  <TABLE ID='tab'>  
	    <TR>
	      <TD VALIGN='top'>Lokaal:</TD>
		  <TD WIDTH='273'>$fields[Lokaal]</TD>
		  <TD VALIGN='top'>Bestand:</TD>
		  <TD WIDTH='246' VALIGN='top'>
		    <a href=\"act/files.php?ID=$id\" onclick=\"return popitup('act/files.php?ID=$id')\">Bestand</a>");
		    if (!empty($fields[Bestand]))
		    {
		      echo("<BR /><i><A href='../uploads/$fields[Bestand]'>$fields[bestand]</i></a> 
	              is nu gekoppelt aan deze activiteit.<BR />Als er een nieuw bestand wordt geupload, wordt dit 
				  bestand in de datbase overschreven.");
		    }
		  echo("</TD>
	    <TR>
	  </TABLE>
	  <SCRIPT language='javascript1.2'>
	    generate_wysiwyg('info');
	  </SCRIPT>
	  <INPUT TYPE='submit' VALUE='Verzenden' NAME='s2' />
	  <INPUT TYPE='reset' VALUE='reset' NAME='reset' />
	  <INPUT TYPE='submit' NAME='cancel' VALUE='Annuleren'>
	  <INPUT TYPE='hidden' VALUE='$fields[Titel]' NAME='Titel' />
	  <INPUT TYPE='hidden' VALUE='$fields[Week]' NAME='Week' />
	  <INPUT TYPE='hidden' VALUE='$fields[Jaar]' NAME='Week' />
	  <INPUT TYPE='hidden' VALUE='$fields[IDActiviteit]' NAME='IDActiviteit' />
    </FORM>
	");
	break;
  }
}

//Save

function saveit ($id,$fields)
{
  //Zet de checkbox Aanmelden om naar 1 of 0
  switch ($fields[Aanmelden])
  {
    case "on": $fields[Aanmelden] = 1;break;
	default: $fields[Aanmelden] = 0;break;
  }

  //Sla de activiteit op
  mysql_query("UPDATE `tblactiviteiten` SET 
			  `Week` = '$fields[Week]',
			  `Jaar` = '$fields[Jaar]',
			  `Dag` = '$fields[Dag]',
			  `Titel`= '$fields[Titel]',
			  `Info` = '$fields[Info]',
			  `IDGever` = '$fields[GeverID]', 
			  `IDNiveau` = '$fields[IDNiveau]',
			  `IDSoort` = '$fields[IDSoort]',
			  `IDOpleiding` = '$fields[IDOpleiding]',
			  `Tijd` = '$fields[Tijd]',
			  `Lokaal` = '$fields[Lokaal]',
			  `Plaats` = '$fields[Plaats]',
			  `Trefwoord` = '$fields[Trefwoord]',
			  `Bestand` = '$fields[Bestand]',
			  `Limiet` = '$fields[Limiet]',
			  `Aanmelden` = '$fields[Aanmelden]',
			  `Animo` = '0'
			  WHERE `IDActiviteit` = '$id' ") or die ("<DIV ID='fout'>". mysql_error() . "</DIV>");

  //Echo dat de gegevens juist zijn opgeslagen..
  echo("<DIV ID='goed'> De activiteit is opgeslagen! </DIV>
  <meta http-equiv='REFRESH' content='2;URL=index.php?page=actoverzicht&week=$fields[Week]&jaar=$fields[Jaar]'>	");
}

//Insert

function insertit ($id,$fields)
{
  //Zet de checkbox Aanmelden om naar 1 of 0
  switch ($fields[Aanmelden])
  {
    case "on": $fields[Aanmelden] = 1;break;
	default: $fields[Aanmelden] = 0;break;
  }

  //Sla de activiteit op
  mysql_query("INSERT INTO `tblactiviteiten`
	(`IDActiviteit`,`Week`,`Jaar`,`Dag`,`Titel`,`Info`,`IDGever`,`IDNiveau`,`IDSoort`,`IDOpleiding`,`Tijd`,
	`Lokaal`,`Plaats`,`Trefwoord`,`Bestand`, `Limiet`,`Aanmelden`,`Animo`)
			   VALUES(
			   '$fields[Num]',
			   '$fields[Week]',
			   '$fields[Jaar]',
			   '$fields[Dag]',
			   '$fields[Titel]',
			   '$fields[Info]',
			   '$fields[GeverID]',
			   '$fields[IDNiveau]',
			   '$fields[IDSoort]',
			   '$fields[IDOpleiding]',
			   '$fields[Tijd]',
			   '$fields[Lokaal]',
			   '$fields[Plaats]',
			   '$fields[Trefwoord]',
			   '$fields[Bestand]',
			   '$fields[Limiet]',
			   '$fields[Aanmelden]',
			   '0')") or die ("<DIV id='fout'>". mysql_error() . "</DIV>");	
  
  //Echo dat de gegevens juist zijn opgeslagen..
  echo("<p id='goed'> De activiteit is opgeslagen! </p>
  <meta http-equiv='REFRESH' content='2;URL=index.php?page=actoverzicht&week=$fields[Week]&jaar=$fields[Jaar]'>	");	

}

function cancelactiviteit()
{
	$fields = $_POST;
    echo("<meta http-equiv='REFRESH' content='0;URL=index.php?page=actoverzicht&week=$fields[Week]
	&jaar=$fields[Jaar]'>");		
}

function error_msg($msg){
	echo("<DIV ID='fout'>$msg</DIV>");
	$fields = $_POST;
	switch ($fields['Aanmelden']){
		case "on": $fields['Aanmelden'] = '1';break;
	}
	show_data($_GET['actid'],$_SESSION['usertype'],$fields);
}
?>