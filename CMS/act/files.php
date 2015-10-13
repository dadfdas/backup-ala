<?php
session_start();
//Controleer eerst of er al een session is (!)
include_once('../../includes/inc_data.php');

?>
<html>
  <head>
    <title>Bestand koppelen - Kennisbus.nl</title>
    <link href='../inc/default.css' type='text/css' rel='stylesheet'>
  </head>
  <body>
  <?php
  if (!isset ($_SESSION['user']))
  {
    die("Hack attempt");
  }
  else
  {
  	if(isset($_POST['koppel']))
	{
	  $filename = $_POST['filename'];
	  $id = $_POST['id'];
	  mysql_query("UPDATE `tblactiviteiten` SET `Bestand` = '".$filename."'
	               WHERE `IDActiviteit` = '".$id."' ") or die ("<DIV ID='fout'>". mysql_error() . "</DIV>");
	  echo("<p> </p>
	        <p> </p>
			<p> </p>
			<center>
			  <DIV ID='goed'> Het bestand is gekoppeld aan de activiteit! </DIV>
	          <a href=\"\" onClick=\"window.close();\">Sluit venster.</a>
			</center>");
	}
	else
	{
	  $id = $_GET['ID'];
	  # Download manager, onderdeel van QCMS
	  # http://qcms.heathernova.us/
	  #
	  # 
	  # Auteur : Cynthia Fridsma
      $new_url = $_GET[new_url];

      // Dit is het path met de downloads,

	  $path="../../uploads/";

	  // open de directory (deze moet natuurlijk wel aanwezig zijn)!
	  $dh = opendir($path);
	  $SQL = mysql_query("SELECT IDActiviteit, Titel FROM tblactiviteiten WHERE `IDActiviteit` = '".$id."'");
	  $fields = mysql_fetch_assoc($SQL);
	  $bestand = $fields['Bestand'];
	
	  if(!empty($bestand))
	  {
	    $bestand = '<br>'.$bestand.' is nu gekoppelt aan deze activiteit.';
	  }

	  echo ("
		    <TABLE border='0' width='100%'>
		      <tr>
			    <td>
				  <b>Kies hier het bestand om te koppelen aan activiteit: " . $fields['Titel'] . "</b>" . $bestand);
	  echo ("  </td>
	           <td align='right'>
			     <a href='bestand.php?ID=$id'>Bestand uploaden</a>
		       </td>
		 	 </tr>
             <tr>
			   <td colspan='2'>
                 <table ID='tab'>
				   <tr>\n
			         <FORM METHOD='POST' ACTION='' NAME='act_edit_best'>
           ");

	  // Dit is de eerste keer dat het programma gebruikt wordt
	  if ($new_url == "")
	  {
	    while ((@$file = readdir($dh)) !== false)
		{
   		  @$file = trim($file);
   		  @$test = strlen($file);      
   		  @$total = $path . $file;
   		  @$bytes = filesize($total);           

		  // Hebben we te maken met een directory of met een file?
   		  if(is_dir($total))
		  {
      	    $type="dir";      
   		  }
		  else
		  {
     		$type="file";
   		  }
     
		  // Indien het een directory betreft:
   		  if ( $type == "dir" & $file != ".." & $file != ".")
		  {  
   		  ?>
			<td width="25" height="25">Dir </td>
   			<td width="100%">
			  <a href="files.php?new_url=<?php echo $path. "" . $file; ?>/" target="_self"><?php echo $file;?></a> 
   			  open this directory
			</td>
		  </tr>
   		  <?php       
   		  }  

		  // indien het een file betreft :    
   		  if ( $type != "dir" )
		  {          
   		  ?>
   		    <td width="25" height="25">File </td>
   			<td width="100%"><input type='checkbox' value='<?php echo("$file"); ?>' name='filename'>
			  <a title="open or view <?php echo $file; ?> <?php echo $type; ?>" href="get.php?start=<?php echo $path . 
			   $file; ?>&file_name=<?php echo $file; ?>">
               <?php echo $file; ?>
			  </a> 
			  <?php echo $bytes; ?>
    		  bytes
			</td>
		  </tr>
    	  <?php        
   		  } 
		}

		closedir($dh);
		echo ("</table>
			   <input type='hidden' name='id' value='$id'>
			   <INPUT TYPE='submit' NAME='koppel' VALUE='Koppelen'>
			   </form>
			   </td></tr></table>");
	  }

	  // Gebruiker wil in een sub directory kijken....
	  if ($new_url !="")
	  {
	    // beveilings patch

		# het download path moet terugkomen in 
		# new_url, indien niet dan probeert gebruiker verder te 
		# kijken, hetgeen we natuurlijk niet willen...

		# Dit lossen we op met de functie ereg.
	    $patt = $path;

		if(ereg($patt,$new_url))
		{
		  // Het is inorde, we vervolgen de code   

    	  # voorkom dat de gebruiker via een omweg inbreekt,
    	  # dit doen we met str_replace

    	  $new_url=str_replace("../", "", $new_url);
    	  $new_url=str_replace("./", "", $new_url);
    	  $new_url=str_replace("/..", "", $new_url);
    	  $new_url=str_replace("/../", "", $new_url);
    	  $new_url=str_replace(".", "", $new_url);
    	  $new_url=str_replace(chr(92), "", $new_url);
    	  $new_url=str_replace("?", "", $new_url);

    	  // test of gebruiker een slash voor de path plaatst om
    	  // in te breken.

    	  $test = "/" . $path;

    	  if (ereg($test, $new_url))
		  { 
    	    // Vervang de slash voor het path....
        	$new_url=str_replace($test, $path, $new_url);
    	  }
    
    	  echo "<h2>". $new_url . "</h2>";
    	  // test of de directory bestaat
    	  if (is_dir($new_url))
		  {
            @$path=$new_url;
            @$dh = opendir($path);
            echo ("<table border=0><tr>\n");

            while ((@$file = readdir($dh)) !== false)
			{
              @$file = trim($file);
              @$test=strlen($file);      
              @$total = $path . $file;
              @$bytes = filesize($total);   
           
              if(is_dir($total))
			  {
                $type="dir";    
              }
			  else
			  {
                $type="file";
              }   
               
         	  // Als de directory de huidige directory betreft, en de 
          	  // gebruiker klikt hierop, keren we weer terug naar de basis....
    
         	  if ($test ==1)
			  { 
              ?>
                <td width="25" height="25">Dir </td>
				<td width="100%">
				  <a href="files.php" target="_self">..</a>open this directory
				</td>
			  </tr>
              <?php      
        	  }     
       		  
			  // Gebruiker wil een niveau verder kijken   
       		  if ( $type == "dir" & $test >2)
			  {  
        	  ?>
			    <td width="25" height="25">Dir </td>
				<td width="100%">
				  <a href="files.php?new_url=<?php echo $path. "" . $file; ?>/" target="_self"><?php echo $file;?></a>
        		  open this directory
				</td>
			  </tr>
        	  <?php 
       		  }     
       
	   		  //we hebben te maken met een bestand.
       		  if ( $type != "dir" )
			  {          
         	  ?>
          	    <td width="25" height="25">File </td>
          		<td width="100%">
				  <a title="download <?php echo $file; ?> <?php echo $type; ?>" href="get.php?start=<?php echo $path .  
				  $file; ?>&file_name=<?php echo $file; ?>"><?php echo $file; ?></a> 
				  <?php echo $bytes; ?> bytes
				</td>
			  </tr>
              <?php  
       		  }    
       		}   

       		@closedir($dh);
       		echo ("</table></td></tr></table>");
     
    	  }
		  else
		  { 
    	    // $new_url heeft een ongeldige waarde opgeleverd ( zie regel nr 101 )
      	    echo "<br><hr>U krijgt geen toestemming om hier te komen...<br>";
    	  } 
        }
		else
		{
 		  // Gebruiker wil in een directory kijken waar hij/zij niets te
 		  // zoeken heeft .. 
   		  echo "<br><hr>U krijgt geen toestemming om hier te komen...<br>";
		}
	  }
	}
  }
?> 