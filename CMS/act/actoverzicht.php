<?php
/**
*  Activiteiten overzicht
*  Deze pagina bevat de functie: -
*  Deze pagina gebruikt de functie:
*    fetch_rows_beheer(), archieveren(),  verwijder_activiteit() 
*    staat in: inc_functions_beheer.php
*/

//Controleer eerst of er al een session is (!)
if (!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{	## Else[1] (deze wordt onderin afgesloten)

  if ( isset($_GET['archief']) && !empty($_GET['archief']))
  {
    $aid = $_GET['id'];
	archieveren($archief,$aid,$_GET['week'],$_GET['jaar']);
  } 
  elseif ( isset($_GET['verwijderen']) && $_GET['verwijderen'] == "y" && ctype_digit($_GET['id']) ) {
	if ( $_SESSION['usertype'] == "Admin" ) {
		verwijder_activiteit($_GET['id'],$_GET['week'],$_GET['jaar']) ;	
	}
	else {
		echo "Niet de juiste rechten om te mogen verwijderen.";
	}
  }
  else	{


?>
      <p><b>Activiteiten</b></p>
      <form action="index.php" method="get">
      <div id="main">
      <p>Kies een week om een overzicht te genereren.
<?php
        $session = $_SESSION['usertype'];

      switch ($session)
      {
        case "Admin": //Show form admin 
          $query = "SELECT DISTINCT Jaar, Week FROM `tblactiviteiten` WHERE Animo = '0' ORDER BY Jaar DESC, Week DESC";
          $result = mysql_query($query);
        break;
        case "Intern":  
          $gever = $_SESSION['IDGever'];
          $query = "SELECT DISTINCT Jaar, Week
                    FROM tblactiviteiten a
                    LEFT JOIN tblgever g ON a.IDgever = g.IDgever
                    WHERE a.Animo = '0'
                    AND a.IDgever = '".$gever."'
                    ORDER  BY a.Jaar DESC, a.Week DESC";
          //$query = "SELECT DISTINCT Jaar, Week FROM `tblactiviteiten` WHERE  ORDER BY Jaar, Week";
          
          $result = mysql_query($query);
        break;
      }

        if ( mysql_num_rows($result) ) {
          echo '<select onChange="window.location=value;">
                <option selected="selected" value="" >----</option>';
        
              while ( $row = mysql_fetch_assoc($result) )
              {
                $week = $row['Week'];
                $jaar = $row['Jaar'];
                
                echo '<option value="index.php?page=actoverzicht&week='.$week.'&jaar='.$jaar.'">'.$week.'-'.$jaar.'</option>';
            
                $week_old = $row['Week'];
                $jaar_old = $row['Jaar'];
              }
          
          echo '</select>';
        }
        else {
            echo 'Geen gegevens in de database.';
        }

        echo '</form></p>';
		
	if (isset($_GET['week']) && isset($_GET['jaar']) )	{
		if ( ctype_digit($_GET['week']) && ctype_digit($_GET['jaar']) ) { 
			fetch_rows_beheer($_GET['week'], $_GET['jaar']);
		}
		else {
			echo 'Geen geldige invoer van week en jaar nummer.';	
		}
	}	
  ?>
  </div>
  <?php
  }
} ## else[1]
?>