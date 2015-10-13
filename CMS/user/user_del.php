<?php
//Controleer eerst of er al een session is (!)
if (!isset ($_SESSION['user']))
{
  die("Hack attempt");
}
else
{	## Else[1] (deze wordt onderin afgesloten)
  ?><head>
    <SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
      <!--
      function checkAll() 
      {
        var boxes = tabel.getElementsByTagName("input");
        for (var i = 0; i < boxes.length; i++) 
		{
          myType = boxes[i].getAttribute("type");
          if ( myType == "checkbox") 
		  {
            boxes[i].checked=1;
          }
        }
      }
      
	  function checkNone()
      {
        var boxes = tabel.getElementsByTagName("input");
        for (var i = 0; i < boxes.length; i++) 
		{
          myType = boxes[i].getAttribute("type");
          if ( myType == "checkbox") 
		  {
            boxes[i].checked=0;
          }
        }
      }
      -->
    </script>
    <style type="text/css">
      <!---
      #tab TABLE
	  {
	    border:0px  #515100;
	  }
 
      #tab TD, TH
	  {
	    padding: 1px 3px;
	    color:#515100;
	    font-size:13px;
	    text-align: left;
	    border: 1px dotted #515100;
      }
    
	  #tab TH 
	  {   
	    font-weight:bold;
	    border: 1px solid #515100;
	  }
	
      #tab a, tabel a:visited, tabel a:hover
	  {   
	    color:#515100;
	    font-size:13px;
	    text-decoration: none;
	  }

      #tab a:hover
	  {   
	    text-decoration: underline;	
	  }
      /* <<< activiteiten tabel format*/
      ---->
    </style>
  </head>
  
  <div id='main'>
  <?
  if(!isset($_POST['submit'])) 
  {
    //form isn't submitted
	echo "<b>Studenten</b><br><br>";
	
	echo '<form action="" method="post" name="arch">';
	
	$query = "SELECT * FROM tblstudent WHERE Archief='0' ORDER BY achternaam";
	$result = mysql_query($query);
	
	echo '
	      <table id="tab">
	        <th> Student </th>
	        <th> Achternaam</th>
	        <th> Voornaam </th>
	        <th> E-mail </th>
	        <th> <input type="checkbox" disabled="disabled"> </th>
	      </tr>
	     ';
	 
	while($row = mysql_fetch_array($result)) 
	{
	  $id = $row['IDStudent'];
	  $achternaam = $row['Achternaam'];
	  $voornaam = $row['Voornaam'];
	  $nummer = $row['IDStudent'];
	  $email = $row['Email'];
	  $email = '<a href="mailto:'.$email.'?subject=kennisbus.nl">'.$email.'</a>';
	  
	  echo '
		    <tr>
			  <td>'.$nummer.'</td>
		      <td>'.$achternaam.'</td>
		      <td>'.$voornaam.'</td>
		      <td>'.$email.'</td>
		      <td>
			    <input type="checkbox" name="log_'.$id.'" value="1">
			  </td>
		    </tr>';
	}
	
	echo '<tr><td colspan="5">';
	echo '<center><input type="submit" name="submit" value="Archiveer geselecteerde"></td>
	</tr></table>';
  //input button

  } 
  else 
  {
	foreach ($_POST as $key => $value) 
	{
	  if(strstr($key, 'log_'))
	  {
	    if($value == '1')
		{
		  $id = str_replace("log_", "", $key);
		  $query = "UPDATE `tblstudent` SET `Archief` = '1' WHERE `tblstudent`.`IDStudent` ='".$id."';";
		  
		  $user = "SELECT Voornaam, Achternaam FROM tblstudent WHERE IDStudent='".$id."';";
		  mysql_query($query) or die (mysql_error());
		  $result = mysql_query($user);
		  		
		  while($row = mysql_fetch_array($result)) 
		  {
		    echo ("De student <b> ". $row['Voornaam']. " ". $row['Achternaam']." </b>is gearchiveerd<br>\n");
	      }
	    }
	  }
	}
  }
  ?>  
  </div>
  <?php
} ##else[1]
?>