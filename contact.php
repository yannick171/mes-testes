
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Unbenannt</title>
	
</head>
<body>

<?php
try
{
	$connexionAmaBase = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '');
}
catch(Exception $e)
{
        die('Erreur : '.$e->getMessage());
}
$contenuDeLaRequete = $connexionAmaBase ->query('SELECT*FROM contacts ORDER BY nom');

while($donneesUnilinaire = $contenuDeLaRequete -> fetch())
{
	
	echo'<p>'.$donneesUnilinaire['nom'].'</p>';
}
?>
</body>
</html>