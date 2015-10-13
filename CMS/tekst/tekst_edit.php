<?php
/**
*  Pagina teksten bewerken van de website.
*  Deze pagina bevat de functie: -
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
  ?>
  <p><b>Teksten aanpassen.</b></p>
  <div id='main'>
    <form action="index.php" method="get">
    Welke pagina? 

      <select onChange="window.location=value;">
	    <option SELECTED VALUE='index.php?page=tekst_edit'>--Selecteer--</option>
	    <option value='index.php?page=tekst_edit&paginaid=main'>Home</option>
	    <!--<option value='index.php?page=tekst_edit&paginaid=highlights'>Highlights</option>-->
	    <option value='index.php?page=tekst_edit&paginaid=info'>Info</option>			
      </select>
      <input type="hidden" value="tekst_edit" name="page" id="page" />
    </form>
  <?php 
  include_once('../includes/inc_data.php'); 

  if ( isset($_GET['save']) && $_GET['save'] == 'ok')
  {
    $id = $_GET['paginaid'];
	$inhoud = $_POST['inhoud'];
	$title = $_POST['title'];
	$query="UPDATE `tblpagina` SET
		    `IDPagina` = '".$id."',
		    `Titel`= '".$title."',
		    `Inhoud` = '".$inhoud."'
		    WHERE `IDPagina` = '".$id."';";
	//Voer de Query uit
	mysql_query($query) or die (mysql_error());
	echo '<br><div id="head2">De tekst is bijgewerkt!</div>';
	
	$query = "SELECT * FROM `tblpagina` WHERE IDPagina = '".$id."'";
    $result = mysql_query($query);
    $numrows=mysql_num_rows($result);	
    $row = mysql_fetch_array($result);

    $id = $row['IDPagina'];
    $inhoud = $row['Inhoud'];
    $title = $row['Titel'];

    echo '
	     <div>Deze tekst is bij gewerkt op de '.$id.'.<br><br></div>
	       <table id="tabel">
		     <TR>
			   <TD>Naam:</TD>
			   <TD>'.$id.'</TD>
			 </TR>
			 <TR>
			   <TD>Titel:</TD>
			   <TD>'.$title.'&nbsp;</TD>
			 </TR>
			 <TR>
			   <TD valign="top">Inhoud:</td>
			   <TD>'.$inhoud.'</TD>
			 </TR>
		   </TABLE>
		  ';
  } 
  else 
  {
    if (  !isset($_GET['paginaid']) || empty($_GET['paginaid']) )
	{
      echo "Kies hier boven een pagina.";
    } 
	else 
	{
      $query = "SELECT * FROM `tblpagina` where IDPagina = '".quote_smart($_GET['paginaid'])."'";
      $result = mysql_query($query);
      $numrows=mysql_num_rows($result);	
      $row = mysql_fetch_array($result);

      $id = $row['IDPagina'];
      $inhoud = $row['Inhoud'];
      $title = $row['Titel'];

      echo '<TABLE id="tab">
	          <form method="POST" action="index.php?page=tekst_edit&paginaid='.$id.'&save=ok">
		        <TR>
			      <td>Naam:</td>
			      <td>'.$id;
		
	  if($id == "main")
	  {
	    echo "(Home)";
	  }
	  else
	  {
	    echo "(Informatiepagina)";
	  }
		
	  echo '
	  	   </TD>
		   <input type="hidden" value="$id" id="naam" name="naam" />
		 </tr>
		 <tr>
		   <td>Title:</td>
		   <td>
		     <input type="text" value="'.$title.'" size="50" maxlength="50" name="title" /></td>
		 </tr>
		 <tr>
		   <td valign="top">Inhoud:</td>
		   <td>
		     <DIV ID="wysiwyg">
		       <textarea cols="100" rows="20" id="inhoud" name="inhoud">'.$inhoud.'</textarea>
		   </td>
		 </tr>
		 <script language="javascript1.2">
		   generate_wysiwyg(';
		 echo "'inhoud');";
		 echo '</script>
	   </TD>
	 </TR>
	 <TR>
	   <TD colspan="2">
	     <CENTER>
		   <input type="submit" value="Verzenden" name="submit">
		   <input type="reset" value="reset" name="reset">
		 </form>
	   </TD>
	 </TR>
   </TABLE>
   ';
  
    }
  }
  ?>
  <?php
} ##Else[1]
?>