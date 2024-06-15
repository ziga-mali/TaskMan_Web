<?php

/**
 * Funkcija vzpostavi povezavo z zbirko podatkov na proceduralni način
 *
 * @return $conn objekt, ki predstavlja povezavo z izbrano podatkovno zbirko
 */
function dbConnect()
{
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "t-man";

	// Ustvarimo povezavo do podatkovne zbirke
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	mysqli_set_charset($conn,"utf8");
	
	// Preverimo uspeh povezave
	if (mysqli_connect_errno())
	{
		printf("Povezovanje s podatkovnim strežnikom ni uspelo: %s\n", mysqli_connect_error());
		exit();
	} 	
	return $conn;
}

/**
 * Funkcija pripravi odgovor v obliki JSON v primeru napake
 *
 * @param $vsebina Znakovni niz, ki opisuje napako
 */
function pripravi_odgovor_napaka($vsebina)
{
	$odgovor=array(
		'status' => 0,
		'error_message'=>$vsebina
	);
	echo json_encode($odgovor);
}

/**
 * Funkcija preveri, če podan igralec obstaja v podatkovni zbirki
 *
 * @param $vzdevek Vzdevek igralca
 * @return true, če igralec obstaja, v nasprotnem primeru false
 */

 function existing_user($id)
 {	
	 global $zbirka;
	 $id=mysqli_escape_string($zbirka, $id);
	 
	 $poizvedba="SELECT * FROM user WHERE id='$id'";
	 
	 if(mysqli_num_rows(mysqli_query($zbirka, $poizvedba)) > 0)
	 {
		 return true;
	 }
	 else
	 {
		 return false;
	 }	
 }

 function existing_project($id)
{	
	global $zbirka;
	$id=mysqli_escape_string($zbirka, $id);
	
	$poizvedba="SELECT * FROM projects WHERE id='$id'";
	
	if(mysqli_num_rows(mysqli_query($zbirka, $poizvedba)) > 0)
	{
		return true;
	}
	else
	{
		return false;
	}	
}

function existing_task($id)
{	
	global $zbirka;
	$id=mysqli_escape_string($zbirka, $id);
	
	$poizvedba="SELECT * FROM tasks WHERE id='$id'";
	
	if(mysqli_num_rows(mysqli_query($zbirka, $poizvedba)) > 0)
	{
		return true;
	}
	else
	{
		return false;
	}	
}


/**
 * Funkcija pripravi URL podanega vira
 *
 * @param $vir Ime vira
 * @return $url URL podanega vira
 */
function URL_vira($vir)
{
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
	{
		$url = "https"; 
	}
	else
	{
		$url = "http"; 
	}
	$url .= "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $vir;
	
	return $url; 
}

	

function IdenAndAuth($headersBearer, $zbirka){
	$headerUsers = $headersBearer;

	if(isset($headerUsers['Identification'])) {
		$headers = trim($headerUsers["Identification"]);
		
		if (preg_match('/UserID\s(\S+)/', $headers, $userMatches)) {
			
			if($userMatches[1]){
				$id = $userMatches[1];
				$poizvedba="SELECT vzdevek, geslo FROM user WHERE id='$id'";
				$rezultat=mysqli_query($zbirka, $poizvedba);

				if (mysqli_num_rows($rezultat) > 0) {
					$odgovor = mysqli_fetch_assoc($rezultat);
				}
		
				$json_odgovor = json_encode($odgovor);
				$json_decode = json_decode($json_odgovor);
				$vzdevek = $json_decode -> vzdevek;
				$geslo = $json_decode -> geslo;
				
				
			}
			else{
				echo "Tule je slo narobe 1 Userja ni";
				http_response_code(401);
			}
		}
		else{
			echo "Tule je slo narobe 2";
			http_response_code(401);
		}
	}
	else{
		echo "Tule je slo narobe 3";
		http_response_code(401);
	}

	if(isset($headersBearer['Authorization'])) {
		$headers = trim($headersBearer["Authorization"]);
		
		if (preg_match('/Bearer\s(\S+)/', $headers, $matchesAuth)) {
			
			if($matchesAuth[1] == hash("md5", $vzdevek.$geslo)){
				return True;		
			}
			else{
				echo "Ni pravilni žeton!";
				http_response_code(401);
			}
		}
		else{
			echo "Ni žetona!";
			http_response_code(401);
		}
	}
	else{
		echo "Napačna avtorizacija";
		http_response_code(401);
	}
}
?>