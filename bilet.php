<?php
require_once "seanse.php";
session_start();
	if(!isset($_SESSION['bilet'])){
			header("Location: index.php");
		}
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>Bilet - Takie Kino</title>
</head>

<body>

<?php
	echo "BILET NA SEANS: ".$_SESSION['tytul']."<br/>";
	echo "Data seansu: ".$_SESSION['dzien']."<br/>";
	echo "Godzina senasu: ".$_SESSION['godzina']."<br/>";
	echo "Sala: ".$_SESSION['id_sala']."<br/>";
	echo "Zarezerwowane miejsca:";
	for($i=0;$i<$_SESSION['ilosc_miejsc_do_rezerwacji'];$i=$i+1){
		echo "  ".$_SESSION['miejsce'][$i];
	}
	?>
</body>
</html>