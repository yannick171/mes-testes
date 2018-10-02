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
<form action="dummy.php" method="post">
<TABLE>
  
  <TR>
    <TD>Verein:</TD>
    <TD>Sportart:</TD></TR>
  <TR>
    <TD>
		<SELECT size=1 name=verein>
			<option value=0>egal</option>
			<option value=1>ATSV Neustadt</option>
			<option value=2>SV Sesamstrasse</option>
			<option value=3>TUS Entenhausen</option>
		</SELECT>
		</TD>
    <TD>
		<SELECT size=1 name=sportart>
			<option value=0>egal</option>
			<option value=1>Ballett</option>
			<option value=2>Fußball</option>
			<option value=3>Handball</option>
			<option value=4>Volleyball</option>
		</SELECT>
		</TD>
	</TR>
	<tr>
		<td colspan="2"><br><br><input type="submit" value="Zeig's mir!!"></td>
	</tr>
</TABLE>
</form>
	
</FONT>

Folgende Spieler üben Fußball im Verein SV Sesamstrasse aus:
<?php
	if((!isset($_POST['verein'])OR (!isset($_POST['sportart'])))
          
	        
			
	

?>
<p>Graf Zahl<br>
<a href="grafzahl@123.com">grafzahl@123.com</a>
</p>
<p>Krümel Monster<br>
<a href="kruemel@monster.de">kruemel@monster.de</a>
</p>
</P>
	
</body>
</html>
