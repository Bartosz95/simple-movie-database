<?php
	
	session_start();
	
	if(!isset($_POST['haslo'])){
		header('Location: logowanie.php');
		exit();
	}
	require_once "seanse.php";

	$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);

	if($polaczenie->connect_errno!=0){
		echo "Error:".$polaczenie->connect_errno;
	}else{
		$haslo = $_POST['haslo'];
		$haslo = htmlentities($haslo,ENT_QUOTES, "UTF-8");
		if ($rezultat = @$polaczenie->query(sprintf("SELECT*FROM hasla WHERE haslo='%s'",mysqli_real_escape_string($polaczenie,$haslo)))){
			if($rezultat->num_rows>0){
				$_SESSION['zalogowany']=true;
				$wiersz=$rezultat->fetch_assoc();
				$_SESSION['haslo']=$wiersz['haslo'];
				unset($_SESSION['blad']);
				$rezultat->free_result();
				header('Location: panel_administratora.php');
			}else{
			$_SESSION['blad']='<span style="color:red">Niepoprawne haslo!</span>';
			header('Location: logowanie.php');
			}
		}
		$polaczenie->close();
	}



?>