<?php
	session_start();
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true)){
		header('Location: panel_administratora.php');
		exit();
	}
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>Logowanie do panelu administratora - Takie Kino</title>
</head>

<body>
	Jeżeli jesteś administratorem zaloguj się.
	<form action="zaloguj.php" method="post">
	E-mail: <br/><input type="text" name="email"/><br/>
	Hasło: <br/><input type="password" name="haslo"/><br/>
	<input type="submit" value="Zaloguj się"/>
	</form>
	
	Jeżeli nie wróć do strony domowej.
	</form>
	<form action="index.php" >
	<input type="submit" value='Powrót do strony domowej'>
	</form>
<?php

 if(isset($_SESSION['blad'])){
 echo $_SESSION['blad'];
 }
?>
</body>
</html>