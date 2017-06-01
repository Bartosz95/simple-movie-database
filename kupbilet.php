<?php
require_once "seanse.php";
session_start();
if (!isset($_SESSION['wybrana'])){
    header('Location: wybierz_ilosc.php');
    exit();
}
// do wyswietlania miejsc
$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
    if($polaczenie->connect_errno!=0){	
        echo "Error: ".$polaczenie->connect_errno."Brak połączenia z bazą rezerwacji Kina";
    }else{
		$id_seans = $_SESSION['id_seans'];
		$sql = "SELECT * FROM rezerwacje WHERE id_seans='$id_seans'";
       if($rezultat=@$polaczenie->query($sql)){
		   $ile=$rezultat->num_rows;
		   for($i=0;$i<$ile;$i=$i+1){
			   $MIEJSCE=$rezultat->fetch_assoc();
			   $_SESSION['e_zajete'][$MIEJSCE['miejsce']]=true;
			   echo $MIEJSCE['miejsce'];
		   }	
		$rezultat->free_result();		   
        }
		//$polaczenie->close();
	}

	
		
if (isset($_POST['email'])){
   // $polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);

    if($polaczenie->connect_errno!=0){	
        echo "Error: ".$polaczenie->connect_errno."Brak połączenia z bazą rezerwacji Kina";
    }
    else{
        $rezerwacja_OK = true;
        $ilosc_miejsc_rezerwowanych = $_SESSION['ilosc_miejsc_do_rezerwacji'];
        $ilosc_miejsc_w_sali = $_SESSION['ilosc_miejsc_w_sali'];
        $id_seans = $_SESSION['id_seans'];

        for($i=0; $i<$ilosc_miejsc_rezerwowanych; $i=$i+1){
            $miejsce = $_POST['miejsce'][$i];//gdzieś tu jest błąd w konwersji
            $_SESSION['miejsce'][$i] = $_POST['miejsce'][$i];
            $sqlREZ = "SELECT * FROM rezerwacje WHERE id_seans='$id_seans' AND miejsce='$miejsce'";
            if ($rezultatREZ = @$polaczenie->query($sqlREZ)){
                if (is_numeric($miejsce) == false){
                    $_SESSION['e_czy_int'][$i] = "Miejsc musi być liczbą całkowitą";
                    $wszystko_OK = false;
                }
                elseif (($miejsce<1)||($miejsce>$ilosc_miejsc_w_sali)){
                    $_SESSION['e_wielkosc'][$i] = "Sala ".$_SESSION['id_sala']." nie posiada miejsca ".$miejsce."<br/>Miejsce o największym numerze to nr. ".$ilosc_miejsc_w_sali;
						$rezerwacja_OK=false;
					}
					elseif(($rezultatREZ->num_rows)>0){
						$_SESSION['e_zajete'][$i]="Miejsce".$miejsce." niestety jest już zajęte";
						$rezerwacja_OK=false;
					}
				}else{
					$rezerwacja_OK=false;
				}
				$rezultatREZ->free_result();
			}
			//poprawnosc maila
			$email=$_POST['email'];
			$emailB=filter_var($email,FILTER_SANITIZE_EMAIL);
			if((filter_var($emailB,FILTER_VALIDATE_EMAIL)==false)||($emailB!=$email)){
				$rezerwacja_OK=false;
				$_SESSION['e_email']="Podaj poprawny adres e-mail";
			}$rezerwacja_OK=true;
			if($rezerwacja_OK==true){
				for($i=0;$i<$ilosc_miejsc_rezerwowanych;$i=$i+1){
					$miejsce=$_POST['miejsce'][$i];
					$email=$_POST['email'];
					$sqlZAREZERWUJ="INSERT INTO rezerwacje VALUES(NULL,$id_seans,$miejsce,'$email')";
					if($rezultatZAREZERWUJ=@$polaczenie->query($sqlZAREZERWUJ)){
						unset($_SESSION['wybrana']);
						$_SESSION['bilet']=true;
						for($i=0;$i<$ilosc_miejsc_rezerwowanych;$i=$i+1){
							unset($_SESSION['e_wielkosc'][$i]);
							unset($_SESSION['e_czy_int'][$i]);
							unset($_SESSION['e_zajete'][$i]);
						}
						header("Location: bilet.php");
						echo "</br>"."REZERWACJA MIEJSCE ".$_POST['miejsce'][$i]." PRZEBIEGLA POMYŚLNIE<br/>";
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
	if(isset($_POST['powrot'])){
		$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
		if($polaczenie->connect_errno!=0){	
			echo "Error: ".$polaczenie->connect_errno."Brak połączenia z bazą rezerwacji Kina";
		}else{
			$id_seans=$_SESSION['id_seans'];
			$nowa_wartosc=$_SESSION['ilosc_miejsc_wolnych']+$_SESSION['ilosc_miejsc_do_rezerwacji'];
			$UPDATE_MIEJSC="UPDATE seanse SET wolne_miejsca='$nowa_wartosc' WHERE id_seans='$id_seans'";
			if($rezultat=@$polaczenie->query($UPDATE_MIEJSC)){
				unset($_SESSION['wybrana']);
				$ilosc_miejsc_rezerwowanych=$_SESSION['ilosc_miejsc_do_rezerwacji'];
				for($i=0;$i<$ilosc_miejsc_rezerwowanych;$i=$i+1){
					unset($_SESSION['e_wielkosc'][$i]);
					unset($_SESSION['e_czy_int'][$i]);
					unset($_SESSION['e_zajete'][$i]);
				}
				header("Location: index.php");
			}else{
				$_SESSION['e_up_miejsc']="Błąd połączenia z bazą. Skontaktuj się z Administratorem.";
			}
		}
	}
?>
<!DOCTYPE HTML>
    <html lang="pl">
        <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <title>Kup bilet - Takie Kino</title>
        <link rel="stylesheet" href="css/styles.css">
        </head>

        <body>
        <section>
<?php
    $tytul = $_SESSION['tytul'];
    $dzien = $_SESSION['dzien'];
    $godzina = $_SESSION['godzina'];
    $id_sala = $_SESSION['id_sala'];
	$id_seans = $_SESSION['id_seans'];
	$ilosc_miejsc = $_SESSION['ilosc_miejsc_w_sali'];
	$wolne_miejsca = $_SESSION['ilosc_miejsc_wolnych'];
	$ilosc_miejsc_rezerwowanych = $_SESSION['ilosc_miejsc_do_rezerwacji'];


    echo "REZERWACJA BILETU NA SEANS: ".$tytul."<br/>";
	echo "Data seansu: ".$dzien."<br/>";
	echo "Godzina senasu: ".$godzina."<br/>";
	echo "Sala: ".$id_sala."<br/>";
    echo "<br/>";

// Wizualizacja miejsc na sali
$x = 0;
echo "<table>";
echo "<tr>";

    	for($i=0;$i<$ilosc_miejsc;$i=$i+1){
		if(isset($_SESSION['e_zajete'][$i+1])){ // <----------------- sprawdza czy zajete
		echo "zajete".$i;} // wyswietla numerek zajetego{
		else{ echo "wolne".$i;
		}
	}
	
for ($i=0;$i<$ilosc_miejsc;$i=$i+1) {
    if ($x == 22) {
        echo "</tr>";
        echo "<tr>";
        $x = 0;
    }
    $place = $i+1;
    if (isset($_SESSION['e_zajete'][$i])) {
        echo "<td style=\"background-color:\#FF0000\"> $place </td>";
    }
    else {
        echo "<td style=\"background-color:\#00FF00\"> $place </td>";
    }

    $x = $x + 1;
}
echo "</tr>";
echo "</table>";
echo "<br/>";

	echo "WYBIERZ ".$ilosc_miejsc_rezerwowanych." MIEJSCA:";
echo "<br/>";
?>

	<form method="POST">
	<?php
	for($i=0;$i<$ilosc_miejsc_rezerwowanych;$i=$i+1){
		?>
		Miejsce <?php echo $i+1;?>: <input type="text" name="miejsce[]" /><br/><br/>
		<?php	
		if(isset($_SESSION['e_wielkosc'][$i])){
			echo '<div class="error">'.$_SESSION['e_wielkosc'][$i].'</div>';
			unset($_SESSION['e_wielkosc'][$i]);
		}
		if(isset($_SESSION['e_czy_int'][$i])){
			echo '<div class="error">'.$_SESSION['e_czy_int'][$i].'</div>';
			unset($_SESSION['e_czy_int'][$i]);
		}
		if(isset($_SESSION['e_zajete'][$i])){
			'<div class="error">'.$_SESSION['e_zajete'][$i].'</div>';
			unset($_SESSION['e_zajete'][$i]);
		}
	}
echo "<br/>";
	?>


	E-mail: <br/><input type="text" name="email"/><br/>
	<?php
	if(isset($_SESSION['e_email'])){
			echo '<div class="error">'.$_SESSION['e_email'].'</div>';
			unset($_SESSION['e_email']);
	}
	?>
	<input type="submit" name="rezerwuj" value='Rezerwuj'>
	</form>
	<?php
	if(isset($_SESSION['e_blad'])){
			echo '<div class="error">'.$_SESSION['e_blad'].'</div>';
			unset($_SESSION['e_blad']);
	}
	?>
	<form method="POST" >
	<input type="submit" name="powrot" value='Powrót do strony domowej'>
	</form>
	<?php
?>
        </section>
</body>
</html>