<?PHP
/**
*  Lijst met week nummers weergeven in menu.
*
*  Deze pagina bevat de functie: -
*  Deze pagina gebruikt de functie: -
*/
require_once('inc_data.php');

if ( !empty($_GET['id']) && ctype_digit($_GET['id']) ) {
				 // Locatie 3 betekent alles
				if ( $_GET['id'] == 3 ) {
				   $query = "SELECT Jaar, Week, Animo, AArchief FROM tblactiviteiten
								WHERE Animo = '0'
									AND AArchief = '0'
								GROUP BY Jaar, Week
                                ORDER BY Jaar DESC, Week DESC";
				}
				else {
					$_GET['id'] = $_GET['id'] - 1;
				   $query = "SELECT Jaar, Week, Animo, AArchief, Plaats FROM tblactiviteiten
								WHERE (Plaats = '".$_GET['id']."' 
									OR Plaats = '2')
									AND Animo = '0'
									AND AArchief = '0'
								GROUP BY Jaar, Week
                                ORDER BY Jaar DESC, Week DESC";
				}
	  			   $result = mysql_query($query);
				   
					echo '<select onChange="window.location=value;">
							<option>Week</option>';
					
  				   while ($row = mysql_fetch_array($result) )
  				   {
    				     $week = $row['Week'];
    				     $jaar = $row['Jaar'];
    
    				     echo '<option value="index.php?week='.$week.'&amp;jaar='.$jaar.'&amp;l='.$_GET['id'].'">'.$week.'-'.$jaar.'</option>';

  				   }
				   echo '</select>';
}
else {
	echo '	<select>
				<option>Geen</option>
			</select>';	
}
?>