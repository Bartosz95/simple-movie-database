<?php
	session_start();
	if(!isset($_SESSION['zalogowany'])){
		header('Location: logowanie.php');
		exit();
	}
	require_once "seanse.php";
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>Panel administratora - Takie Kino</title>
</head>

<body>
<?php
	echo"<p>Witaj ".$_SESSION['email'].'     [<a href="logout.php">Wyloguj się</a>]</p>';
	$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
	?>
	<form action="" method="post">
	<input type="submit" name="dodaj_film" value='Dodaj film'>
	</form>
	<?php
	if(isset($_POST['dodaj_film'])){
		?>
		Wypełnij wszystkie pola by dodać film do bazy.
		<form action="" method="post">
		Tutuł: <br/><input type="text" name="tytul"/><br/>
		Czas trwania: <br/><input type="text" name="czas_trwania"/><br/>
		Gatunek: <br/><input type="text" name="gatunek"/><br/>
		Reżyser: <br/><input type="text" name="rezyser"/><br/>
		Rodzaj(2D/3D): <br/><input type="text" name="rodzaj"/><br/>
		<input type="submit" name="DODAJ_FILM" value='Dodaj'>
		</form>
		<?php
		if($polaczenie->connect_errno!=0){	
			echo "Error: ".$polaczenie->connect_errno."Brak połączenia z bazą rezerwacji Kina";
		}else{
			$sql="SELECT * FROM filmy";
			if($rezultat=@$polaczenie->query($sql)){
				echo "Lista filmów obecnych w bazie:".'</br>';
				while($wynik= $rezultat->fetch_assoc()){
					echo $wynik['tytul'].'</br>';
				}
			}
		}
	}
	if(isset($_POST['DODAJ_FILM'])){
		$tytul=$_POST['tytul'];
		$czas_trwania=$_POST['czas_trwania'];
		$gatunek=$_POST['gatunek'];
		$rezyser=$_POST['rezyser'];
		$rodzaj=$_POST['rodzaj'];
		if($polaczenie->connect_errno!=0){	
			echo "Error: ".$polaczenie->connect_errno."Brak połączenia z bazą rezerwacji Kina";
		}else{
			$sql="SELECT * FROM filmy WHERE tytul='$tytul'";
			if($rezultat=@$polaczenie->query($sql)){
				$ilosc = $rezultat->num_rows;
				if($ilosc>0){
					echo "Film o podanym tytule istenieje już w badzie";
				}else{
					$sqlINSERT="INSERT INTO filmy VALUES(NULL,'$tytul','$czas_trwania','$gatunek','$rezyser','$rodzaj')";
					if($rezultatINSERT=@$polaczenie->query($sqlINSERT)){
						echo "FILM DODANY POMYŚLNIE";
					}else{
						echo "Nie udało się dodać filmu";
					}
				}
			}
		}
	}
	?>
	<form action="" method="post">
	<input type="submit" name="usun_film" value='Usuń film'>
	</form>
	<?php
	if(isset($_POST['usun_film'])){
		if($polaczenie->connect_errno!=0){	
			echo "Error: ".$polaczenie->connect_errno."Brak połączenia z bazą rezerwacji Kina";
		}else{
			$sql="SELECT * FROM filmy";
			if($rezultat=@$polaczenie->query($sql)){
				echo '</br>'."Lista filmów obecnych w bazie:".'</br>';
				while($wynik= $rezultat->fetch_assoc()){
					echo $wynik['tytul'].'</br>';
				}
			}
		}
		?>
		<br/>
		Podaj nazwę filmu do usunięcia.
		<form action="" method="post">
		Tutuł: <input type="text" name="tytul"/><br/>
		<input type="submit" name="USUN_FILM" value='Usuń'>
		</form>
		<?php
	}
	if(isset($_POST['USUN_FILM'])){
		if($polaczenie->connect_errno!=0){	
			echo "Error: ".$polaczenie->connect_errno."Brak połączenia z bazą rezerwacji Kina";
		}else{
			$tytul=$_POST['tytul'];
			$sql="SELECT * FROM filmy WHERE tytul='$tytul'";
				if($rezultat=@$polaczenie->query($sql)){
				$ilosc = $rezultat->num_rows;
				if($ilosc<1){
					echo "Film o podanym tytule istenieje już w badzie";
				}else{
					$sqlDELETE="DELETE FROM filmy WHERE tytul='$tytul'";
					if($rezultatDELETE=@$polaczenie->query($sqlDELETE)){
						echo "FILM USUNIĘTY POMYŚLNIE";
					}else{
						echo "Nie udało się usunąć filmu";
					}
				}
			}
		}
	}
	$polaczenie->close();
	?>
</body>
</html>