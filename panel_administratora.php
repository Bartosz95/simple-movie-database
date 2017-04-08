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
	<script type="text/javascript" src="calendar.js"></script>
	<title>Panel administratora - Takie Kino</title>
</head>

<body>
<?php
	echo"<p>Witaj ".$_SESSION['email'].'     [<a href="logout.php">Wyloguj się</a>]</p>';
	$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
	if($polaczenie->connect_errno!=0){	
			echo "Error: ".$polaczenie->connect_errno."Brak połączenia z bazą rezerwacji Kina";
	}else{
		?>
		<form action="" method="post">
		<input type="submit" name="dodaj_film" value='Dodaj film'>
		</form>
		<?php
		if(isset($_POST['dodaj_film'])){
			?>
			Wypełnij wszystkie pola by dodać film do bazy.
			<form method="post">
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
		?>
		</br>
		<form  method="post">
		<input type="submit" name="usun_film" value='Usuń film'>
		</form>
		<?php
		if(isset($_POST['usun_film'])){
			$sql="SELECT * FROM filmy";
			if($rezultat=@$polaczenie->query($sql)){
				echo '</br>'."Lista filmów obecnych w bazie:".'</br>';
				while($wynik= $rezultat->fetch_assoc()){
					echo $wynik['tytul'].'</br>';
				}
			}
			?>
			<br/>
			Podaj nazwę filmu do usunięcia.
			<form method="post">
			Tutuł: <input type="text" name="tytul"/><br/>
			<input type="submit" name="USUN_FILM" value='Usuń'>
			</form>
			<?php
		}

		if(isset($_POST['USUN_FILM'])){
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

		?>
		</br>
		<form method="post">
		<input type="submit" name="dodaj_seans" value='Dodaj seans'>
		</form>
		<?php
		if(isset($_POST['dodaj_seans'])){
			if(isset($_GET['wybierz_godzine'])){
				unset($_GET['wybierz_godzine']);
			}
			$sqlFILM="SELECT * FROM filmy";
			$sqlSALA="SELECT * FROM sale";
			if(($rezultatFILM=@$polaczenie->query($sqlFILM))&&
				($rezultatSALA=@$polaczenie->query($sqlSALA))){
				?>
				<form method="GET" >
				<br/>Wybierz film
				<select name="film" >
				<?php
				while($FILM=$rezultatFILM->fetch_assoc()){
					?>
					<option value="<?php echo $FILM['id_film'];?>"> <?php echo $FILM['tytul'];?> </option>
					<?php
				}
				?>
				</select>
				<br/><br/>Wybierz sale 
				<select name="sala" >
				<?php
				while($SALA=$rezultatSALA->fetch_assoc()){
					?>
					<option value="<?php echo $SALA['id_sala'];?>"> <?php echo $SALA['id_sala'];?> </option>
					<?php
				}
				?>
				</select>
				<br/><br/>Wybierz date
				<script>DateInput('data', true, 'YYYY-MON-DD')</script>
				<input type="submit" name="wybierz_godzine" value='WYBIERZ GODZINĘ'>
				</form>
				<?php
			}
		}
		if(isset($_GET['wybierz_godzine'])){
			$_SESSION['sala']=$_GET['sala'];
			$_SESSION['film']=$_GET['film'];
			$DATA=$_GET['data'];
			$SALA=$_GET['sala'];
			$FILM=$_GET['film'];
			$partDATA = explode('-',$DATA);
			$data = mktime(1,1,1,$partDATA[1],$partDATA[2],$partDATA[0]);
			$DATA = date('Y-m-d',$data);
			$_SESSION['data']=$DATA;
			$sqlSEANS="SELECT * FROM seanse WHERE id_sala='$SALA' AND dzien='$DATA' ORDER BY godzina";
			$sqlFILM_WYBRANY="SELECT * FROM filmy WHERE id_film='$FILM'";
			if(($rezultatSEANS=@$polaczenie->query($sqlSEANS))&&($rezultatFILM_WYBRANY=@$polaczenie->query($sqlFILM_WYBRANY))){
				$FILM_WYBRANY = $rezultatFILM_WYBRANY->fetch_assoc();
				$_SESSION['czas trwania']=$FILM_WYBRANY['czas trwania'];
				$_SESSION['tytul']=$FILM_WYBRANY['tytul'];
				$_SESSION['id_film']=$FILM_WYBRANY['id_film'];
				echo "<FILM TRWA".$FILM_WYBRANY['czas trwania']."br/>";
				if($rezultatSEANS->num_rows>0){
					echo "Zarezerwowane godziny na tej sali w dniu: ".$DATA."<br/>";
					while($SEANS = $rezultatSEANS->fetch_assoc()){
						$film = $SEANS['id_film'];
						$sqlFILM ="SELECT * FROM filmy WHERE id_film='$film'";
						if($rezultatFILM = @$polaczenie->query($sqlFILM)){
							$FILM = $rezultatFILM->fetch_assoc();
							$POCZATEK =$SEANS['godzina'];
							$CZAS = $FILM['czas trwania'];
							$partCZAS = explode(':',$POCZATEK);
							$time = mktime($partCZAS[0],$partCZAS[1]+$CZAS,$partCZAS[2]);
							$KONIEC = date('H:i:s',$time);
							echo $POCZATEK." - ".$KONIEC."<br/>";
						}
					}
				}else{
					echo "Brak filmów w SALI ".$SALA." w dniu: ".$DATA."<br/>";
				}
			}
			?>
			<form method="GET">
			Wybierz Godzinę:
			<select name="godzina" >
			<?php
			for($i=1;$i<24;$i++){
				?>
				<option value="<?php echo $i;?>"> <?php echo $i;?> </option>
				<?php
			}
			?>
			</select>
			godz
			<select name="minuta" >
			<?php
			for($i=0;$i<60;$i++){
				?>
				<option value="<?php echo $i;?>"> <?php echo $i;?> </option>
				<?php
			}
			?>
			</select>
			min
			<br/><input type="submit" name="wyb_godz" value='DODAJ'>
			</form>
			<form method="post">
			<br/><input type="submit" name="dodaj_seans" value='Powrot do wyboru daty'>
			</form>
			<?php
		}
		if(isset($_GET['wyb_godz'])){
			$data=$_SESSION['data'];
			$sala=$_SESSION['sala'];
			$tytul=$_SESSION['tytul'];
			$czas_trwania=$_SESSION['czas trwania'];
			$id_film=$_SESSION['id_film'];
			$godzina = date('H:i',mktime($_GET['godzina'],$_GET['minuta'],00));
			$_SESSION['godzina']=$godzina;
			
			$partCZAS = explode(':',$godzina);//cos tu 
			$time = mktime($partCZAS[0],$partCZAS[1]+$czas_trwania,$partCZAS[2]);
			$zakonczenie = date('H:i:s',$time);
			
			$sqlSEANS = "SELECT * FROM seanse WHERE id_sala='$sala' AND dzien='$data' ORDER BY godzina";
			$wszystko_OK=true;
			echo "tu1";
			if($rezultatSEANS=@$polaczenie->query($sqlSEANS)){
				if($rezultatSEANS->num_rows>0){
					while($SEANS = $rezultatSEANS->fetch_assoc()){
						$film = $SEANS['id_film'];
						$sqlFILM = "SELECT * FROM filmy WHERE id_film='$film'";
						if($rezultatFILM = @$polaczenie->query($sqlFILM)){
							$FILM = $rezultatFILM->fetch_assoc();
							$POCZATEK = $SEANS['godzina'];
							$CZAS = $FILM['czas trwania'];
							$partCZAS = explode(':',$POCZATEK);
							$time = mktime($partCZAS[0],$partCZAS[1]+$CZAS+30,$partCZAS[2]);
							$KONIEC = date('H:i:s',$time);
							if(($godzina>$POCZATEK)&&($godzina<$KONIEC)||($zakonczenie>$POCZATEK)&&($zakonczenie<$KONIEC)){
								$wszystko_OK=false;
								echo "W jednej sali może trwać tylko jeden seans. Po seansie wymagane jest 30 min przerwy przed startem następnego seansu.<br/>";
							}
						}
					}
				}
			}
			if($wszystko_OK==true){
				echo "tu2";
				$sql="SELECT * FROM seanse WHERE id_sala='$sala'";
				if($rezultatSALA = @$polaczenie->query($sqlSALA)){
					$SALA = $rezultatSALA->fetch_assoc();
					$wolne_miejsca=$SALA['ilosc_miejsc'];
					$sqlINSERT="INSERT INTO seanse VALUES(NULL,'$id_film','$sala','$data','$godzina','$wolne_miejsca')";
					if($rezultatINSERT = @$polaczenie->query($sqlINSERT)){
						echo "POPRAWNIE DODANO SEANS</br>";
					}
				}
			}else{
			}
		}
	$polaczenie->close();
	}
	?>
</body>
</html>