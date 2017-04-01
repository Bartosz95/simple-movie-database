<?php
require_once "seanse.php";
session_start();
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>Kino Wrocław</title>
</head>

<body>
	<title1>Kino</title1><br/><br/>

<?php
$DZIEN="2017-06-18";
	$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
	if($polaczenie->connect_errno!=0){	
		echo "Error: ".$polaczenie->connect_errno." Brak połączenia z bazą filmów";
	}else{
	
		$sql="SELECT * FROM seanse ORDER BY id_film";
		if($rezultat=@$polaczenie->query($sql)){
			$ile_filmow = $rezultat->num_rows;
			if($ile_filmow=0){
				echo "Nie ma takich filmow";
			}else{
				while($wynik= $rezultat->fetch_assoc()){
					$tablica[]=$wynik;
				}
				echo "Repertuar na dziś: ".$DZIEN.'<br/><br/>';
				for ($i=0;$i<count($tablica); $i++){
						$id_film=$tablica[$i]['id_film'];
						if(($i=='0')||($id_film>$tablica[$i-1]['id_film'])){
							$sql2="SELECT * FROM filmy WHERE id_film='$id_film' ";
							$rezultatFILM=@$polaczenie->query($sql2);
							$FILM= $rezultatFILM->fetch_assoc();
							$tytul=$FILM['tytul'];
							echo '<br/>'.'<br/>'.$FILM['tytul'].'<br/>'.$FILM['gatunek'].'  '.$FILM['czas trwania'].'<br/>'.$FILM['rezyser'].'<br/>';
							echo '      '.$tablica[$i]['godzina'];
						}else{
						echo '	'.$tablica[$i]['godzina'];
						}		
				}				
			}
		}	
	
	
	$polaczenie->close();
	}
?>
</body>
</html>