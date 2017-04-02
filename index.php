<?php
require_once "seanse.php";
session_start();
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<script type="text/javascript" src="calendar.js"></script>
	<title>Kino Wrocław</title>
</head>

<body>

<font size="8">Kino</font><br/><br/>
	<title1></title1>
<form action="index.php" method="post">
<script>DateInput('wybrana_data', true, 'YYYY/MON/DD')</script>
<input type="submit" value='Zmień datę'/>
</form>

<?php

if($_POST){$DZIEN = $_POST['wybrana_data'];}
else{$DZIEN="2017-06-18";
}
//$DZIEN="2017-06-18";

//date("Y-m-d"); $_POST['nasza_nazwa'];

	$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
	if($polaczenie->connect_errno!=0){	
		echo "Error: ".$polaczenie->connect_errno." Brak połączenia z bazą filmów";
	}else{
	
		$sql="SELECT * FROM seanse WHERE dzien='$DZIEN' ORDER BY id_film";
		if($rezultat=@$polaczenie->query($sql)){
			$ile_filmow = $rezultat->num_rows;
			if($ile_filmow=0){
				echo "Złe zapytanie";
			}else{
				while($wynik= $rezultat->fetch_assoc()){
					$tablica[]=$wynik;
				}
				echo '<br/>'."Repertuar na dziś: ".$DZIEN;
				if($rezultat->num_rows==0){
					echo "<br/>Brak filmów.<br/>";
				}else{
					for ($i=0;$i<count($tablica); $i++){
							$id_film=$tablica[$i]['id_film'];
							if(($i=='0')||($id_film>$tablica[$i-1]['id_film'])){
								$sqlFILM="SELECT * FROM filmy WHERE id_film='$id_film' ";
								$rezultatFILM=@$polaczenie->query($sqlFILM);
								$FILM= $rezultatFILM->fetch_assoc();
								$tytul=$FILM['tytul'];
								echo '<br/>'.'<br/><span style="font-weight: bold">'.$FILM['tytul'].'</span><br/>'.$FILM['gatunek'].' | '.$FILM['czas trwania'].' minuty '.'<br/> Reżyser: '.$FILM['rezyser'].'<br/>';
								$godzina=$tablica[$i]['godzina'];
								$informacja=$tablica[$i]['id_seans'];
								echo 'Dostępne godziny:   '.'<a href = "kupbilet.php?paczka='.$informacja.'">'.$tablica[$i]['godzina'].'</a>'.'    ';
							}else{
								echo '<a href = "kupbilet.php?paczka='.$informacja.'">'.$tablica[$i]['godzina'].' </a>';
						}
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