
  <div id='head'> Animo</div>
  <div id='main'>	
  <?php
/**
*   Overzicht maken van animo activiteiten.
*
*/
  
/**
* 	Pagina met lijst van activiteiten waarvoor eerst genoeg animo moet zijn.
*
*/

  $query = mysql_query("SELECT * FROM tblactiviteiten 
	  				      RIGHT JOIN tblgever on tblactiviteiten.IDGever = tblgever.IDGever
	  				      LEFT JOIN tblsoort on tblactiviteiten.IDSoort = tblsoort.IDSoort
	  				      LEFT JOIN tblniveau on tblactiviteiten.IDNiveau = tblniveau.IDNiveau
	  				      LEFT JOIN tblopleiding on tblactiviteiten.IDOpleiding = tblopleiding.IDOpleiding
						  WHERE Animo = '1'
						  ORDER  BY IDActiviteit"); 
  
 if ( mysql_num_rows($query) ) {

    echo '<table id="tabel">
	      <tr>
		   <th>Titel</th>
		   <th> Type </th>
		   <th> Niveau</th>
		   <th> Lokatie </th>
		   <th> Docent </th>
		   <th>Aanmeldingen</th>
		   <th>Limiet</th>
		 </tr>
		 ';
		 
  while($row = mysql_fetch_array($query) ) 
  {
    $id = $row['IDActiviteit'];
    $Titel = $row['Titel'];
    $Type = $row['Soort'];
    $Niveau = $row['Niveau'];
    $Lokaal = $row['Lokaal'];
    $voornaam = substr($row['gVoornaam'],0,1);
    $Gever = $row['Naam'];
    $Limiet = $row['Limiet'];
	
	$sql2 = mysql_query("SELECT * FROM tblinschrijvingen
				          WHERE tblinschrijvingen.IDActiviteit = '".$id."' AND tblinschrijvingen.Activated = '1'");
	
	$sql3 = mysql_query("SELECT * FROM tblinschrijvingen 
						WHERE tblinschrijvingen.IDActiviteit = '".$id."'
						AND tblinschrijvingen.Activated = '0' 
						ORDER BY Nummer ASC
					") or die ( mysql_error() );	
	
	$nums3 = mysql_num_rows($sql3);			        
	
	// aantal aanmeldingen
	$aantal_aanmeldingen = mysql_num_rows($sql2);
	
	  switch($row['Plaats'])
	  {
	    case 0: $plaats = "G";break;
		case 1: $plaats = "Z";break;
		case 2: $plaats = "E";break;
	  }
	  
	  echo '<tr>
			  <td><b><a href="index.php?page=infoanimo&actid='.$id.'">'.$Titel.'</b></a>
			  <td>'.$Type.'</td>
			  <td>'.$Niveau.'</td>
			  <td>'.$plaats.' '.$Lokaal.'</td>
			  <td>'.$voornaam.' '.$Gever.'</td>
			  <td>'.$aantal_aanmeldingen.'</td>
			  <td>'.$Limiet.'</td>
			</tr>'; 
  }
  echo '</table>'; 

}
else
{
    echo 'Er zijn geen animo resultaten gevonden.';
}

  ?>
  </div>