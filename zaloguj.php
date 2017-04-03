<?php
	
	session_start();
	
	if((!isset($_POST['email']))||(!isset($_POST['haslo']))){
		header('Location: logowanie.php');
		exit();
	}
	require_once "seanse.php";

	$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);

	if($polaczenie->connect_errno!=0){
		echo "Error:".$polaczenie->connect_errno;
	}else{
		$email = $_POST['email'];
		$haslo = $_POST['haslo'];
		$email = htmlentities($email,ENT_QUOTES, "UTF-8");
		$haslo = htmlentities($haslo,ENT_QUOTES, "UTF-8");
		if ($rezultat = @$polaczenie->query(
		sprintf("SELECT*FROM administratorzy WHERE email='%s' AND haslo='%s'",
		mysqli_real_escape_string($polaczenie,$email),
		mysqli_real_escape_string($polaczenie,$haslo)))){
			$ilu_adminow = $rezultat->num_rows;
			if($ilu_adminow>0){
				$_SESSION['zalogowany']=true;
				$wiersz=$rezultat->fetch_assoc();
				$_SESSION['email']=$wiersz['email'];
				$_SESSION['haslo']=$wiersz['haslo'];
				unset($_SESSION['blad']);
				$rezultat->free_result();
				header('Location: panel_administratora.php');
			}else{
			$_SESSION['blad']='<span style="color:red">Niepoprawny login lub haslo!</span>';
			header('Location: logowanie.php');
			}
		}
		$polaczenie->close();
	}



?>