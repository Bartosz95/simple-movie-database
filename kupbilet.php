<?php
require_once "seanse.php";
session_start();
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>Kup bilet - Kino Wrocław</title>
</head>

<body>

<?php
	$id_seans = $_GET['paczka'];
	$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
	if($polaczenie->connect_errno!=0){	
		echo "Error: ".$polaczenie->connect_errno." Brak połączenia z bazą filmów";
	}else{
		$sql="SELECT * FROM seanse WHERE id_seans='$id_seans'";
		if($rezultat=@$polaczenie->query($sql)){
			$ilosc = $rezultat->num_rows;
			if($ilosc==0){
				echo "Złe zapytanie";
			}else{
				$wynik= $rezultat->fetch_assoc();
				$id_film=$wynik['id_film'];
				$sqlFILM="SELECT * FROM filmy WHERE id_film='$id_film'";
				$rezultatFILM=@$polaczenie->query($sqlFILM);
				$FILM= $rezultatFILM->fetch_assoc();
				echo "REZERWACJA BILETU NA SEANS: ".$FILM['tytul']."<br/>";
				echo "Data seansu: ".$wynik['dzien']."<br/>";
				echo "Godzina senasu: ".$wynik['godzina']."<br/>";
				echo "Sala: ".$wynik['id_sala']."<br/>";
				echo "WYBIERZ MIEJSCE:";
				/*?>
				<form>
				<select multiple="multiple" id="selectElem" name="selectElem[]">
				<option value="ham">Ham</option>
				<option value="cheese">Cheese</option>
				<option value="hamcheese">Ham and Cheese</option>
				</select>
				</form>
				<?php*/
			}
		$rezultat->free_result();
		}
		$polaczenie->close();
	}
?>
</body>
</html>