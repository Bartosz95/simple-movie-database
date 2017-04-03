<?php
	function sprawdz_miejsce(){
		$miejsce=$_POST['miejsce'];
		$id_seans=$_SESSION['id_seans'];
		$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
		if($polaczenie->connect_errno!=0){	
			echo "Error: ".$polaczenie->connect_errno."Brak połączenia z bazą rezerwacji Kina";
		}else{
			
			$sql="SELECT * FROM seanse";
			if($rezultat=@$polaczenie->query($sql)){
							echo 'raz';
				//$czy_zajete = $rezultat->num_rows;
				//echo $rezultat->num_rows;
				//if($czy_zajete>0){
				//	echo "ZEJETE";
				
			}else{
							echo 'dwa';
			
			}//$wynik= $rezultat->fetch_assoc();
			//echo $REZ['id_rezerwacji'];
		}
		$polaczenie->close();
		/*
		if($rezultat->num_rows>0){
			echo "MIEJSCE ZAJETE".$_SESSION['miejsce']; return false;
		}else{
			echo "MIEJSCE DOSTEPNE";
		}
		return true;*/
	}
?>
