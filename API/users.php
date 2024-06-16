<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)

include("orodja.php"); 					// Vključitev 'orodij'

$zbirka = dbConnect();					// Pridobitev povezave s podatkovno zbirko

header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

if($_SERVER["REQUEST_METHOD"]=="POST"){
	nov_uporabnik();
}else{
	if(IdenAndAuth(apache_request_headers(), $zbirka)){
		switch($_SERVER["REQUEST_METHOD"])		
		{
			case 'GET':
				if(!empty($_GET["id"]))
				{
					pridobi_uporabnika($_GET["id"]);		
				}
				else
				{
					pridobi_uporabnike();					
				}
				break;
				
			case 'PUT':
				if(!empty($_GET["id"]))
				{
					posodobi_uporabnika($_GET["id"]);
				}
				else
				{
					http_response_code(400);	
				}
				break;
			
			case 'DELETE':
				if(!empty($_GET["id"]))
				{
					odstrani_uporabnika($_GET["id"]);
				}
				else
				{
					http_response_code(400);	
				}
				break;
				
			case 'OPTIONS':						
				http_response_code(204);
				break;
				
			default:
				http_response_code(405);		
				break;
		}
	}
}

mysqli_close($zbirka);					


// ----------- konec skripte, sledijo funkcije -----------

function pridobi_uporabnike()
{
	global $zbirka;
	$odgovor=array();
	
	$poizvedba="SELECT id, vzdevek, geslo, ime, priimek, mail FROM user";	
	
	$rezultat=mysqli_query($zbirka, $poizvedba);
	
	while($vrstica=mysqli_fetch_assoc($rezultat))
	{
		$odgovor[]=$vrstica;
	}
	
	http_response_code(200);		//OK
	echo json_encode($odgovor);
}

function pridobi_uporabnika($id)
{
	global $zbirka;
	$id=mysqli_escape_string($zbirka, $id);

	$poizvedba="SELECT id, vzdevek, geslo, ime, priimek, mail FROM user WHERE id='$id'";	
	
	$rezultat=mysqli_query($zbirka, $poizvedba);

	if(mysqli_num_rows($rezultat)>0)	
	{
		$odgovor=mysqli_fetch_assoc($rezultat);
		
		http_response_code(200);		
		echo json_encode($odgovor);
	}
	else							
	{
		http_response_code(404);		
	}
}

function nov_uporabnik()
{
	global $zbirka, $DEBUG;
	
	$podatki = json_decode(file_get_contents('php://input'), true);
	
	if(isset($podatki["vzdevek"], $podatki["geslo"], $podatki["ime"], $podatki["priimek"], $podatki["mail"]))
	{
		$poizvedbaPost="";
		$vzdevek = mysqli_escape_string($zbirka, $podatki["vzdevek"]);
		$geslo = mysqli_escape_string($zbirka, $podatki["geslo"]);
        $ime = mysqli_escape_string($zbirka, $podatki["ime"]);
        $priimek = mysqli_escape_string($zbirka, $podatki["priimek"]);
        $mail = mysqli_escape_string($zbirka, $podatki["mail"]);
		if(isset($podatki["admin"])){
			$admin = mysqli_escape_string($zbirka, $podatki["admin"]);
			$poizvedbaPost="INSERT INTO user (vzdevek, geslo, ime, priimek, mail, admin) VALUES ('$vzdevek', '$geslo', '$ime', '$priimek', '$mail', '$admin')";
		}else{
			$poizvedbaPost="INSERT INTO user (vzdevek, geslo, ime, priimek, mail) VALUES ('$vzdevek', '$geslo', '$ime', '$priimek', '$mail')";
		}			
       
        
        if(mysqli_query($zbirka, $poizvedbaPost))
        {
            http_response_code(201);	// Created
        }
        else
        {
            http_response_code(500);	// Internal Server Error (ni nujno vedno streznik kriv!)
            
            if($DEBUG)	//Pozor: vračanje podatkov o napaki na strežniku je varnostno tveganje!
            {
                pripravi_odgovor_napaka(mysqli_error($zbirka));
            }
		}
	}
	else
	{
		http_response_code(400);// Bad Request
	}
}

function posodobi_uporabnika($id)
{
	global $zbirka, $DEBUG;
	
	$id = mysqli_escape_string($zbirka, $id);
	
	$podatki = json_decode(file_get_contents("php://input"),true);

    if(isset($podatki["vzdevek"], $podatki["geslo"], $podatki["ime"], $podatki["priimek"], $podatki["mail"]))
		
	if(existing_user($id))
	{
		if(isset($podatki["vzdevek"], $podatki["geslo"], $podatki["ime"], $podatki["priimek"], $podatki["mail"]))
        {
            $vzdevek = mysqli_escape_string($zbirka, $podatki["vzdevek"]);
            $geslo = mysqli_escape_string($zbirka, $podatki["geslo"]);
            $ime = mysqli_escape_string($zbirka, $podatki["ime"]);
            $priimek = mysqli_escape_string($zbirka, $podatki["priimek"]);
            $mail = mysqli_escape_string($zbirka, $podatki["mail"]);
                
            $poizvedbaPost="UPDATE users (vzdevek, geslo, ime, priimek, mail) VALUES ('$vzdevek', '$geslo', '$ime', '$priimek', '$mail')";
            
            if(mysqli_query($zbirka, $poizvedbaPost))
            {
                http_response_code(204);	// OK with no content
            }
			else
			{
				http_response_code(500);	// Internal Server Error (ni nujno vedno streznik kriv!)
				
				if($DEBUG)	//Pozor: vračanje podatkov o napaki na strežniku je varnostno tveganje!
				{
					pripravi_odgovor_napaka(mysqli_error($zbirka));
				}
			}
		}
		else
		{
			http_response_code(400);	// Bad Request
		}
	}
	else
	{
		http_response_code(404);	// Not Found
	}
}	
	
function odstrani_uporabnika($id)
{	
	global $zbirka, $DEBUG;
	$id=mysqli_escape_string($zbirka, $id);

	if(existing_user($id))
	{
		$poizvedba="DELETE FROM user WHERE id='$id'";
		
		if(mysqli_query($zbirka, $poizvedba))
		{
			http_response_code(204);	//OK with no content
		}
		else
		{
			http_response_code(500);	// Internal Server Error (ni nujno vedno streznik kriv!)
			
			if($DEBUG)	//Pozor: vračanje podatkov o napaki na strežniku je varnostno tveganje!
			{
				pripravi_odgovor_napaka(mysqli_error($zbirka));
			}
		}
	}
	else
	{
		http_response_code(404);	// Not Found
	}
}
?>