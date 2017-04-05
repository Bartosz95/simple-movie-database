<?php
require_once "seanse.php";
session_start();
	if(isset($_POST['email'])){
		$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
		if($polaczenie->connect_errno!=0){	
			echo "Error: ".$polaczenie->connect_errno."Brak połączenia z bazą rezerwacji Kina";
		}else{
			$rezerwacja_OK=true;
			$ilosc_miejsc_rezerwowanych=$_SESSION['ilosc_miejsc_do_rezerwacji'];
			$ilosc_miejsc_w_sali=$_SESSION['ilosc_miejsc_w_sali'];
			$id_seans=$_SESSION['id_seans'];
			for($i=0;$i<$ilosc_miejsc_rezerwowanych;$i=$i+1){
				$miejsce=$_POST['miejsce'][$i];//gdzieś tu jest błąd w konwersji 
				$sqlREZ="SELECT * FROM rezerwacje WHERE id_seans='$id_seans' AND miejsce='$miejsce'";
				if($rezultatREZ=@$polaczenie->query($sqlREZ)){
					if(($miejsce<1)||($miejsce>$ilosc_miejsc_w_sali)){
						$_SESSION['e_wielkosc'][$i]="Sala ".$_SESSION['id_sala']." nie posiada miejsca ".$miejsce;
						$rezerwacja_OK=false;
					}
					echo $rezultatREZ->num_rows;
					if(($rezultatREZ->num_rows)>0){
						$_SESSION['e_zajete'][$i]="Miejsce".$miejsce." niestety jest już zajęte";
						$rezerwacja_OK=false;
					}
				}else{
					$rezerwacja_OK=false;
				}
				$rezultatREZ->free_result();
			}
			if($rezerwacja_OK==true){
				for($i=0;$i<$ilosc_miejsc_rezerwowanych;$i=$i+1){
					$miejsce=$_POST['miejsce'][$i];
					$email=$_POST['email'];
					$sqlZAREZERWUJ="INSERT INTO rezerwacje VALUES(NULL,$id_seans,$miejsce,'$email')";
					if($rezultatZAREZERWUJ=@$polaczenie->query($sqlZAREZERWUJ)){
						echo "</br>"."REZERWACJA MIEJSCA ".$_SESSION['miejsce'][$i]." PRZEBIEGLA POMYŚLNIE";
					}else{
						echo "NIE UDAŁO SIĘ ZAREZERWOWAĆ MIEJSCA ".$_SESSION['miejsce'][$i]." !</br>";
					}
				}
			}else{
				$_SESSION['e_blad']= "</br>"."PROSZĘ WYPEŁNIĆ POLA ZGODNIE Z ZALECENIAMI"."</br>";
			}
		$polaczenie->close();
		}
	}
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
	$tytul=$_SESSION['tytul'];
	$dzien=$_SESSION['dzien'];
	$godzina=$_SESSION['godzina'];
	$id_sala=$_SESSION['id_sala'];
	$id_seans=$_SESSION['id_seans'];
	$ilosc_miejsc=$_SESSION['ilosc_miejsc_w_sali'];
	$wolne_miejsca=$_SESSION['ilosc_miejsc_wolnych'];
	$ilosc_miejsc_rezerwowanych=$_SESSION['ilosc_miejsc_do_rezerwacji'];
	echo "REZERWACJA BILETU NA SEANS: ".$tytul."<br/>";
	echo "Data seansu: ".$dzien."<br/>";
	echo "Godzina senasu: ".$godzina."<br/>";
	echo "Sala: ".$id_sala."<br/>";
	echo "WYBIERZ ".$ilosc_miejsc_rezerwowanych." MIEJSCA:";
	?>
	<form method="POST">
	<?php
	for($i=0;$i<$ilosc_miejsc_rezerwowanych;$i=$i+1){
		?>
		Miejsce <?php echo $i+1;?>: <input type="text" name="miejsce[]" /><br/>
		<?php
		
		if(isset($_SESSION['e_wielkosc'][$i])){
			echo '<div class="error">'.$_SESSION['e_wielkosc'][$i].'</div>';
			unset($_SESSION['e_wielkosc'][$i]);
		}
	/*	if(isset($_SESSION['e_zajete'][$i])){
			echo $_SESSION['e_zajete'][$i];
			unset($_SESSION['e_zajete'][$i]);
		}*/

	}
	?>
	E-mail: <br/><input type="text" name="email"/><br/>
	<input type="submit" name="rezerwuj" value='Rezerwuj'>
	</form>
	<?php
	if(isset($_SESSION['e_blad'])){
			echo '<div class="error">'.$_SESSION['e_blad'].'</div>';
			unset($_SESSION['e_blad']);
	}
?>
</body>
</html>