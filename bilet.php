<?php
require_once "seanse.php";
session_start();
	if(isset($_POST['ilosc_miejsc_do_rezerwacji'])){
		$wszystko_OK=true;
		$ilosc_miejsc_do_rezerwacji=$_POST['ilosc_miejsc_do_rezerwacji'];
		$id_seans=$_SESSION['id_seans'];
		echo $id_seans;
		//czy zawiera się w przedziale
		if(($ilosc_miejsc_do_rezerwacji<1)||($ilosc_miejsc_do_rezerwacji>$_SESSION['ilosc_miejsc_wolnych'])){
			$wszystko_OK=false;
			$_SESSION['e_wielkosc']="Ilość miejsc do rezerwacji musi zawierać się w przedziale od 1 do ".$_SESSION['ilosc_miejsc_wolnych'];
		}
		$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
		if($polaczenie->connect_errno!=0){	
			echo "Error: ".$polaczenie->connect_errno."Brak połączenia z bazą rezerwacji Kina";
		}else{
			$nowa_wartosc=$_SESSION['ilosc_miejsc_wolnych']-$ilosc_miejsc_do_rezerwacji;
			echo $nowa_wartosc;
			$UPDATE_MIEJSC="UPDATE seanse SET wolne_miejsca='$nowa_wartosc' WHERE id_seans='$id_seans'";
			if(($rezultat=@$polaczenie->query($UPDATE_MIEJSC))!=true){
				$wszystko_OK=false;
				$_SESSION['e_up_miejsc']="Nie udało się zarezerwować miejsc";
			}
		}
		if($wszystko_OK==true){
			$_SESSION['ilosc_miejsc_do_rezerwacji']=$_POST['ilosc_miejsc_do_rezerwacji'];
			$dalej="kupbilet.php";
			header("Location: $dalej");
			echo "udana walidacja";exit();
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
				$SEANS= $rezultat->fetch_assoc();
				$id_film=$SEANS['id_film'];
				$id_sala=$SEANS['id_sala'];
				$sqlFILM="SELECT * FROM filmy WHERE id_film='$id_film'";
				$sqlSALA="SELECT * FROM sale WHERE id_sala='$id_sala'";
				if(($rezultatFILM=@$polaczenie->query($sqlFILM))&&($rezultatSALA=@$polaczenie->query($sqlSALA))){
					$FILM= $rezultatFILM->fetch_assoc();
					$SALA = $rezultatSALA->fetch_assoc();
					$_SESSION['tytul']=$FILM['tytul'];
					$_SESSION['dzien']=$SEANS['dzien'];
					$_SESSION['godzina']=$SEANS['godzina'];
					$_SESSION['id_sala']=$SEANS['id_sala'];
					$_SESSION['id_seans']=$SEANS['id_seans'];
					$_SESSION['ilosc_miejsc_w_sali']=$SALA['ilosc_miejsc'];
					$_SESSION['ilosc_miejsc_wolnych']=$SEANS['wolne_miejsca'];
					echo "REZERWACJA BILETU NA SEANS: ".$FILM['tytul']."<br/>";
					echo "Data seansu: ".$SEANS['dzien']."<br/>";
					echo "Godzina senasu: ".$SEANS['godzina']."<br/>";
					echo "Sala: ".$SEANS['id_sala']."<br/>";
					echo "WYBIERZ ILOSC MIEJSC DO ZAREZERWOWANIA<br/>(wolnych: ".$_SESSION['ilosc_miejsc_wolnych']."miejsc)";
					?>
					<form method="post">
					Ilość miejsc: <br/><input type="text" name="ilosc_miejsc_do_rezerwacji"/><br/>
					<?php
					if(isset($_SESSION['e_wielkosc'])){
						echo '<div class="error">'.$_SESSION['e_wielkosc'].'</div>';
						unset($_SESSION['e_wielkosc']);
					}
					?>
					<input type="submit" name="dalej" value='Przejdz dalej'>
					</form>
					<form action="index.php" >
					<input type="submit" value='Powrót do strony domowej'>
					</form>
					<?php
					if(isset($_SESSION['e_up_miejsc'])){
						echo '<div class="error">'.$_SESSION['e_up_miejsc'].'</div>';
						unset($_SESSION['e_up_miejsc']);
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