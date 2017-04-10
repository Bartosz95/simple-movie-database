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
	echo"<p>Witaj w Panelu Administratora<br/>".'     [<a href="logout.php">Wyloguj się</a>]</p>';
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
					if($_POST['tytul']==""){
						echo "Tytul filmu nie może być pusty";
					}elseif(is_numeric($_POST['czas_trwania'])==false){
						echo "Czas filmu musi być podany w minutach i być liczbą całkowitą";
					}elseif($_POST['czas_trwania']<=0){
						echo "Czas fimu musi być dodatni i różny od zera";
					}else{
						if($_POST['gatunek']==""){
							$gatunek="Brak informacji";
						}
						if($_POST['rezyser']==""){
							$rezyser="Brak informacji";
						}
						if($_POST['rodzaj']==""){
							$rodzaj="Brak informacji";
						}
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
		</br>
		<form  method="POST">
		<input type="submit" name="usun_film" value='Usuń film'>
		</form>
		<?php
		if(isset($_POST['usun_film'])){
			$sql="SELECT * FROM filmy";
			if($rezultat=@$polaczenie->query($sql)){
				echo '</br>'."Lista filmów obecnych w bazie:".'</br>';
				?>
				<form  method="POST">
				<select name="id_film" >
				<?php
				while($FILM = $rezultat->fetch_assoc()){
					?>
					<option value="<?php echo $FILM['id_film'];?>"> <?php echo $FILM['tytul'];?> </option>
					<?php
				}
				?>
				</select>
				<input type="submit" name="USUN_FILM" value='Usuń'>
				</form>
				<?php
			}
		}

		if(isset($_POST['USUN_FILM'])){
			$id_film=$_POST['id_film'];
			$sqlSEANS="SELECT * FROM seanse WHERE id_film='$id_film'";
			$sqlFILM="SELECT * FROM filmy WHERE tytul='$id_film'";
			if(($rezultatSEANS=@$polaczenie->query($sqlSEANS))&&($rezultatFILM=@$polaczenie->query($sqlFILM))){
				if($rezultatSEANS->num_rows>0){
					echo "Nie można usunąć filmu ponieważ jest on na seansach:";
					while($SEANS=$rezultatSEANS->fetch_assoc()){
						echo "<br/>Dzień: ".$SEANS['dzien']." | Godzina: ".$SEANS['godzina']." | Sala: ".$SEANS['id_sala'];
					}
				}else{
					$sqlDELETE="DELETE FROM filmy WHERE id_film='$id_film'";
					if($rezultatDELETE=@$polaczenie->query($sqlDELETE)){
						echo "FILM USUNIĘTY POMYŚLNIE";
					}else{
						echo "Nie udało się usunąć filmu. Błąd połączenia z bazą lub zły tytuł filmu.";
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
			if(isset($_POST['wybierz_godzine'])){
				unset($_POST['wybierz_godzine']);
			}
			$sqlFILM="SELECT * FROM filmy";
			$sqlSALA="SELECT * FROM sale";
			if(($rezultatFILM=@$polaczenie->query($sqlFILM))&&
				($rezultatSALA=@$polaczenie->query($sqlSALA))){
				?>
				<form method="POST" >
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
		if(isset($_POST['wybierz_godzine'])){
			$_SESSION['sala']=$_POST['sala'];
			$_SESSION['film']=$_POST['film'];
			$DATA=$_POST['data'];
			$SALA=$_POST['sala'];
			$FILM=$_POST['film'];
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
			<form method="POST">
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
		if(isset($_POST['wyb_godz'])){
			$data=$_SESSION['data'];
			$sala=$_SESSION['sala'];
			$tytul=$_SESSION['tytul'];
			$czas_trwania=$_SESSION['czas trwania'];
			$id_film=$_SESSION['id_film'];
			$godzina = date('H:i',mktime($_POST['godzina'],$_POST['minuta'],00));
			$_SESSION['godzina']=$godzina;
			$zakonczenie = date('H:i',mktime($_POST['godzina']+$_SESSION['czas trwania'],$_POST['minuta'],00));
			$sqlSEANS = "SELECT * FROM seanse WHERE id_sala='$sala' AND dzien='$data' ORDER BY godzina";
			$wszystko_OK=true;
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
							echo $godzina."__".$zakonczenie;
							if(($godzina>$POCZATEK)&&($godzina<$KONIEC)||($zakonczenie>$POCZATEK)&&($zakonczenie<$KONIEC)){
								$wszystko_OK=false;
								echo "W jednej sali może trwać tylko jeden seans. Po seansie wymagane jest 30 min przerwy przed startem następnego seansu.<br/>";
							}
						}
					}
				}
			}
			if($wszystko_OK==true){
				$sqlSALA="SELECT * FROM sale WHERE id_sala='$sala'";
				if($rezultatSALA = @$polaczenie->query($sqlSALA)){
					$SALA = $rezultatSALA->fetch_assoc();
					$wolne_miejsca=$SALA['ilosc_miejsc'];
					$sqlINSERT="INSERT INTO seanse VALUES(NULL,'$id_film','$sala','$data','$godzina','$wolne_miejsca')";
					if($rezultatINSERT = @$polaczenie->query($sqlINSERT)){
						echo "POPRAWNIE DODANO SEANS</br>";
						unset($_POST['wyb_godz']);
					}
				}
			}else{
				$_POST = array('wyb_godz' => null);
			}
		}
		?>
		<br/>
		<form method="post">
		<input type="submit" name="USUN_SENS" value='Usuń seans'>
		</form>
		<?php
		if(isset($_POST['USUN_SENS'])){
			$sqlSALA="SELECT * FROM sale";
			if($rezultatSALA=@$polaczenie->query($sqlSALA)){
				?>
				<form method="POST">
				Wybierz date
				<script>DateInput('data', true, 'YYYY-MON-DD')</script>
				Wybierz sale
				<select name="id_sala" >
				<?php
				
				while($SALA=$rezultatSALA->fetch_assoc()){
					?>
					<option value="<?php echo $SALA['id_sala'];?>"> <?php echo $SALA['id_sala'];?> </option>
					<?php
				}
				?>
				</select>
				<br/><input type="submit" name="wyb_dat" value='Przejdź dalej'>
				</form>
				<?php
				}
		}
		if(isset($_POST['wyb_dat'])){
			$SALA = $_POST['id_sala'];
			$DATA = $_POST['data'];
			$partDATA = explode('-',$DATA);
			$data = mktime(1,1,1,$partDATA[1],$partDATA[2],$partDATA[0]);
			$DATA = date('Y-m-d',$data);
			$sqlSEANSE = "SELECT * FROM seanse WHERE id_sala='$SALA' AND dzien='$DATA'"; //
			if($rezultatSEANSE=@$polaczenie->query($sqlSEANSE)){
				if($rezultatSEANSE->num_rows>0){
					?>
					<form method="POST">
					<select name = "id_seans" >
					<?php
					while($SEANS=$rezultatSEANSE->fetch_assoc()){
						echo $id_film=$SEANS['id_film'];
						$sqlFILM="SELECT * FROM filmy WHERE id_film='$id_film'";
						$rezultatFILM=@$polaczenie->query($sqlFILM);
						$FILM=$rezultatFILM->fetch_assoc();
						?>
						<option value="<?php echo $SEANS['id_seans'];?>"> <?php echo $SEANS['godzina']."-".$FILM['tytul'];?> </option>
						<?php
					}
					?>
					</select>
					<input type="submit" name="usun_seans" value='Usuń'>
					</form>
					<?php
				}else{
					echo "W tym dniu nie ma seansów</br>";
				}
			}
		}
		if(isset($_POST['usun_seans'])){
			$id_seans=$_POST['id_seans'];
			$sqlREZERWACJE="SELECT * FROM rezerwacje WHERE id_seans='$id_seans'";
			if($rezultatREZERWACJE=@$polaczenie->query($sqlREZERWACJE)){
				if($rezultatREZERWACJE->num_rows>0){
					echo "Nie można usunąć seansu ponieważ zarezerwowano na niego bilety.<br/> E-mail'e osób które zarezerwowały bilety to:";
					while($REZERWACJA=$rezultatREZERWACJE->fetch_assoc()){
						echo "<br/>E-mail: ".$REZERWACJA['email'];
					}
				}else{
					$sqlFILM="DELETE FROM seanse WHERE id_seans='$id_seans'";
					if($rezultatFILM=@$polaczenie->query($sqlFILM)){
						echo "SEANS ZOSTAŁ USUNIETY</br>";
					}
				}
			}
		}
	$polaczenie->close();
	}
	?>
</body>
</html>