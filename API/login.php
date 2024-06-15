<?php
	$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)					// Vključitev 'orodij'

	include("orodja.php");

	$zbirka = dbConnect();
	header('Content-Type: application/json');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

	//uporabnisko ime in geslo enega izmed registriranih uporabnikov:
	$podatki = json_decode(file_get_contents('php://input'), true);
	
	if(isset($podatki["vzdevek"],$podatki["geslo"])){

		$vzdevek=mysqli_escape_string($zbirka, $podatki["vzdevek"]);
		$geslo=mysqli_escape_string($zbirka, $podatki["geslo"]);

		$poizvedba="SELECT id, vzdevek, geslo, admin FROM user WHERE vzdevek='$vzdevek' AND geslo='$geslo'";
		$rezultat=mysqli_query($zbirka, $poizvedba);

		if (mysqli_num_rows($rezultat) > 0) {
			$odgovor = mysqli_fetch_assoc($rezultat);
		}

		$json_odgovor = json_encode($odgovor);
		$json_decode = json_decode($json_odgovor); 	
		$vzdevek = $json_decode -> vzdevek;
		$geslo = $json_decode -> geslo;
		$id = $json_decode->id;
		$admin = $json_decode->admin;

		if($podatki["vzdevek"] == $vzdevek && $podatki["geslo"] == $geslo){
			$token = hash("md5",$vzdevek.$geslo);
			echo json_encode([array('token'=>$token, 'user_id' => $id, 'admin' => $admin)]);
			http_response_code(200);
		}
		else{
			http_response_code(401);
		}
	}
	else{
	http_response_code(400);
	}
?>