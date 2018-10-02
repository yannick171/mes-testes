<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Unbenannt</title>
</head>
<body>
<P><FONT face=Arial size=5><STRONG>Sportlerdatenbank</STRONG></FONT></P>
<P><FONT face=Arial>Zeige alle Sportler, die folgende Kriterien 
erfüllen:</FONT></P>
<P><FONT face=Arial>
<form action="php.php" method="post">
<TABLE>
  
  <TR>
    <TD>Verein:</TD>
    <TD>Sportart:</TD></TR>
  <TR>
    <TD>
		<SELECT size=1 name=verein>
			<option value=0>egal</option>
			<option value=3>ATSV Neustadt</option>
			<option value=1>SV Sesamstrasse</option>
			<option value=2>TUS Entenhausen</option>
		</SELECT>
		</TD>
    <TD>
		<SELECT size=1 name=sportart>
			<option value=0>egal</option>
			<option value=4>Ballett</option>
			<option value=1>Fußball</option>
			<option value=2>Handball</option>
			<option value=3>Volleyball</option>
		</SELECT>
		</TD>
	</TR>
	<tr>
		<td colspan="2"><br><br><input type="submit" value="Zeig's mir!!"></td>
	</tr>
</TABLE>
</form>
	

<?php
	
	$tab1=array('egal','ATSV Neustadt','SV Sesamstrasse','TUS Entenhausen');
	$tab2=array('egal','Ballett','Fußball','Handball','Volleyball');
	//to test if the button is pushed 
	if(isset($_POST['sportart'])OR isset($_POST['verein']))
	{
		// Connection to database
					try
			{
				$bdd = new PDO('mysql:host=localhost;dbname=constd_onlinetest;charset=utf8', 'root', '',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			}
			catch(Exception $e)
			{
					die('Erreur : '.$e->getMessage());
			}
		//to test which field is selected
	      if($_POST['sportart']==0 AND $_POST['verein']!=0)
		  {
			// prepare the database to answer depending on selected field
		$requete = $bdd->prepare('SELECT vorname,name,email FROM spieler WHERE verein_id = ?');
		$requete->execute(array($_POST['verein']));	
	    echo'Folgende Spieler üben Sport im Verein '.$tab1[$_POST['verein']].' aus:<br>';
		while($donnees = $requete->fetch())
		{
		echo'<p>'.$donnees['vorname'].' '.$donnees['name'].'<br>
		<a href="'.$donnees['email'].'">'.$donnees['email'].'</a>
		</p>';
		}
		  }
		//to test which field is selected 
		if($_POST['sportart']!=0 AND $_POST['verein']==0)
		{
			// prepare the database to answer depending on selected field
		$requete = $bdd->prepare('SELECT vorname,name,email FROM spieler WHERE sportar_id = ?');
		$requete->execute(array($_POST['sportart']));
		echo'Folgende Spieler üben '.$tab2[$_POST['sportart']].' aus:<br>';
		while($donnees = $requete->fetch())
		{
		echo'<p>'.$donnees['vorname'].' '.$donnees['name'].'<br>
		<a href="'.$donnees['email'].'">'.$donnees['email'].'</a>
		</p>';
		}
		  }
		//to test which field is selected
		if($_POST['sportart']!=0 AND $_POST['verein']!=0)
		{
			// prepare the database to answer depending on selected field
		$requete = $bdd->prepare('SELECT vorname,name,email FROM spieler WHERE sportart_id = ? AND verein_id = ?');
		$requete->execute(array($_POST['sportart'],$_POST['verein']));	
		echo'Folgende Spieler üben '.$tab2[$_POST['sportart']].' im Verein '.$tab1[$_POST['verein']].' aus:<br>';
		while($donnees = $requete->fetch()){
		echo'<p>'.$donnees['vorname'].' '.$donnees['name'].'<br>
		<a href="'.$donnees['email'].'">'.$donnees['email'].'</a>
		</p>';
		}
		}
		//to test which field is selected
		if($_POST['sportart']==0 AND $_POST['verein']==0)
		{
			// Antwort of the database
		$reponse = $bdd->query('SELECT vorname,name,email FROM spieler');
		echo'Folgende Spieler üben Sport im Verein aus:<br>';
		while($donnees = $reponse->fetch()){
		echo'<p>'.$donnees['vorname'].' '.$donnees['name'].'<br>
		<a href="'.$donnees['email'].'">'.$donnees['email'].'</a>
		</p>';
		}
		}
		
		
	
	}
	    
	?>
	</FONT>
</P>
	
</body>
</html>
