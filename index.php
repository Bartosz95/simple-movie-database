<?php
   require_once "seanse.php";
   session_start();
   ?>

<!DOCTYPE HTML>
<html lang="pl">

  <head>
    <meta http-equiv="content-type" content="text/html"; charset="utf-8">
    <script type="text/javascript" src="calendar.js"></script>
    <title>Takie Kino</title>
    <link rel="stylesheet" href="css/styles.css">
  </head>

  <body>
    <header></header>
    <h1>TAKIE KINO</h1>
    <section>
    <form action="index.php" method="post">
      <script>DateInput('wybrana_data', true, 'YYYY/MON/DD')</script>
      <input type="submit" value='Zmień datę'/>
    </form>
    <br/>

<?php
       if ($_POST){
           $DZIEN = $_POST['wybrana_data'];
       }
       else{
           $DZIEN = "2017-06-18";
       }

$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);

if($polaczenie->connect_errno != 0){
    echo "Error: ".$polaczenie->connect_errno." Brak połączenia z bazą filmów";
    exit();
}
else{
    $sql = "SELECT * FROM seanse WHERE dzien='$DZIEN' ORDER BY id_film, godzina";
    if ($rezultat = @$polaczenie->query($sql)){
        $ile_filmow = $rezultat->num_rows;
        if($ile_filmow=0){
            echo "Złe zapytanie";
        }
        else{
            while($wynik = $rezultat->fetch_assoc()){
                $tablica[] = $wynik;
            }
            echo "Repertuar na dziś: ".$DZIEN;
            if($rezultat->num_rows == 0){
                echo "<br/>Brak filmów.<br/>";
            }
            else{
                for ($i=0; $i<count($tablica); $i++){
                    echo "<pre>";
                    $id_film = $tablica[$i]['id_film'];
                    if (($i == '0') || ($id_film > $tablica[$i-1]['id_film'])){
                        $sqlFILM = "SELECT * FROM filmy WHERE id_film='$id_film' ";
                        $rezultatFILM = @$polaczenie->query($sqlFILM);
                        $FILM = $rezultatFILM->fetch_assoc();
                        $tytul = $FILM['tytul'];
                        echo '<span style="font-weight: bold">'
                            .$FILM['tytul'].'</span><br/>'.$FILM['gatunek'].' | '
                            .$FILM['czas trwania'].' minuty '.'<br/>Reżyser: '.$FILM['rezyser'].'<br/>';
                        $godzina = $tablica[$i]['godzina'];
                        $informacja = $tablica[$i]['id_seans'];
                        echo 'Dostępne godziny:   '.'<a href = "wybierz_ilosc.php?paczka='.$informacja.'">'
                            .$tablica[$i]['godzina'].'</a>';
                        if (!($tablica[$i+1]['id_film'] > $tablica[$i]['id_film'])){
                            echo '    '.'<a href = "wybierz_ilosc.php?paczka='.$informacja.'">'.$tablica[$i+1]['godzina'].'</a>  ';
                            $i++;
                        }
                    }
                    echo "</pre>";
                }
            }
        }
		$rezultat->free_result();
    }


	$polaczenie->close();
}
?>
</section>
</br></br>
<footer>
<div class="container">
<p><a href="logowanie.php">Panel Administratora</a></p>
</div>
</footer>
</body>
</html>
