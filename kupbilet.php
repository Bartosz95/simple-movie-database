<?php
require_once "seanse.php";
session_start();
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>Kup bilet - Takie Kino</title>
</head>

<body>

<?php

	$id_seans = $_GET['paczka'];
	$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
	if($polaczenie->connect_errno!=0){	
		echo "Error: ".$polaczenie->connect_errno."Brak połączenia z bazą rezerwacji Kina";
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
				$id_sala=$wynik['id_sala'];
				$sqlSALA="SELECT * FROM sale WHERE id_sala='$id_sala'";
				$rezultatSALA=@$polaczenie->query($sqlSALA);
				$SALA = $rezultatSALA->fetch_assoc();
				?>
				<form action="" method="post">
				Miejsce: <br/><input type="text" name="miejsce"/><br/>
				<input type="submit" name="submit" value='Wyślij'>
				</form>
				
				<?php
				if($_POST){
					$miejsce=$_POST['miejsce'];
				}
				/*trzeba jeszcze posprawdzac czy miejsce miesci sie w granicach 
				*/
				if (isset($_POST['submit'])){
					$id_seans=$wynik['id_seans'];
					$sqlREZ="SELECT * FROM rezerwacje WHERE id_seans='$id_seans' AND miejsce='$miejsce'";
					if($rezultatREZ=@$polaczenie->query($sqlREZ)){
						//if(($miejsce>=1)&&($miejsce<=)){
						if($rezultatREZ->num_rows>0){
							echo 'To miejsce niestety jest już zajęte';
						}else{
							$sqlZAREZERWUJ="INSERT INTO rezerwacje VALUES(NULL,$id_seans,$miejsce)";
							if($rezultatREZ=@$polaczenie->query($sqlZAREZERWUJ)){
								echo "REZERWACJA PRZEBIEGLA POMYSLNIE";
							}else{
								echo "BŁĄD REZERWACJI";
							}
						}
					}else{
							echo 'Error Nie ma takiego filmu';
					}
				}
			}
		$rezultat->free_result();
		}
		$polaczenie->close();
	}

?>
</body>
</html>