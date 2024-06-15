<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)

include("orodja.php"); 					// Vključitev 'orodij'

$zbirka = dbConnect();					// Pridobitev povezave s podatkovno zbirko

header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

if(IdenAndAuth(apache_request_headers(), $zbirka)){
	switch($_SERVER["REQUEST_METHOD"])		
	{
		case 'GET':
			if(!empty($_GET["id"]))
			{
				pridobi_projekt($_GET["id"]);		
			}
			else
			{
				pridobi_projekte();					
			}
			break;
			

		case 'POST':
			nov_projekt();
			break;
			
		case 'PUT':
			if(!empty($_GET["id"]))
			{
				posodobi_projekt($_GET["id"]);
			}
			else
			{
				http_response_code(400);	
			}
			break;
		
		case 'DELETE':
			if(!empty($_GET["id"]))
			{
				odstrani_projekt($_GET["id"]);
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






mysqli_close($zbirka);					


// ----------- konec skripte, sledijo funkcije -----------

function pridobi_projekte()
{
	global $zbirka;
	$odgovor=array();
	
	$poizvedba="SELECT id, ime, opis, koncan FROM projects";	
	
	$rezultat=mysqli_query($zbirka, $poizvedba);
	
	while($vrstica=mysqli_fetch_assoc($rezultat))
	{
		$odgovor[]=$vrstica;
	}
	
	http_response_code(200);		//OK
	echo json_encode($odgovor);
}

function pridobi_projekt($id)
{
	global $zbirka;
	$odgovor = array();
	$id=mysqli_escape_string($zbirka, $id);
	
	$poizvedba="SELECT ime, opis, id, koncan FROM projects WHERE id='$id'";
	
	$rezultat=mysqli_query($zbirka, $poizvedba);

	if(mysqli_num_rows($rezultat)>0)	
	{
		$odgovor[] = mysqli_fetch_assoc($rezultat);
		
		http_response_code(200);		
		echo json_encode($odgovor);
	}
	else							
	{
		http_response_code(404);		
	}
}

function nov_projekt()
{
	global $zbirka, $DEBUG;
	
	$podatki = json_decode(file_get_contents('php://input'), true);
	
	if(isset($podatki["ime"], $podatki["opis"], $podatki["userID"]))
	{
		$ime = mysqli_escape_string($zbirka, $podatki["ime"]);
		$opis = mysqli_escape_string($zbirka, $podatki["opis"]);
		$userID = mysqli_escape_string($zbirka, $podatki["userID"]);

		if(isset($podatki["dostop"]) && !empty($podatki["dostop"])) {
			$dostopIDs = $podatki["dostop"];
		} else {
			$dostopIDs = [];
		}
			
        $poizvedbaPostProjekt="INSERT INTO projects (ime, opis) VALUES ('$ime', '$opis')";
        
        if(mysqli_query($zbirka, $poizvedbaPostProjekt))
        {
            http_response_code(201);	// Created
			$projektID = $zbirka -> insert_id;
            $odgovor=URL_vira($projektID) . "/tasks";
			echo json_encode($odgovor);

			$allUserIDs = array_merge([$userID], $dostopIDs);

			foreach($allUserIDs as $dostopID) {
				$dostopID = mysqli_escape_string($zbirka, $dostopID);
				
				$poizvedbaProjektUser = "INSERT INTO projectsuser (id_projekta, id_userja) VALUES ('$projektID', '$dostopID')";
				
				if(mysqli_query($zbirka, $poizvedbaProjektUser)) {
					http_response_code(201);
				} else {
					http_response_code(500);    // Internal Server Error
					
					if($DEBUG) {
						pripravi_odgovor_napaka(mysqli_error($zbirka));
					}
					return; // Exit function early if error occurs
				}
			}
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

function posodobi_projekt($id)
{
	global $zbirka, $DEBUG;
	
	$id = mysqli_escape_string($zbirka, $id);
	
	$podatki = json_decode(file_get_contents("php://input"), true);
		
	if (existing_project($id)) {
		$updateFields = [];

		if (isset($podatki["ime"])) {
			$ime = mysqli_escape_string($zbirka, $podatki["ime"]);
			$updateFields[] = "ime='$ime'";
		}

		if (isset($podatki["opis"])) {
			$opis = mysqli_escape_string($zbirka, $podatki["opis"]);
			$updateFields[] = "opis='$opis'";
		}

		if (isset($podatki["koncan"])) {
			$koncan = mysqli_escape_string($zbirka, $podatki["koncan"]);
			$updateFields[] = "koncan='$koncan'";
		}

		if (!empty($updateFields)) {
			$poizvedba = "UPDATE projects SET " . implode(", ", $updateFields) . " WHERE id='$id'";
			
			if (mysqli_query($zbirka, $poizvedba)) {
				http_response_code(204);    // OK with no content
			} else {
				http_response_code(500);    // Internal Server Error (ni nujno vedno streznik kriv!)

				if ($DEBUG) {    // Pozor: vračanje podatkov o napaki na strežniku je varnostno tveganje!
					pripravi_odgovor_napaka(mysqli_error($zbirka));
				}
			}
		} else {
			http_response_code(400);    // Bad Request
			pripravi_odgovor_napaka("No valid fields provided for update.");
		}
	} else {
		http_response_code(404);    // Not Found
	}
}
	
	
function odstrani_projekt($id)
{	
	global $zbirka, $DEBUG;
	$id=mysqli_escape_string($zbirka, $id);

	if(existing_project($id))
	{
		$poizvedbaProjectsUser="DELETE FROM projectsuser WHERE id_projekta='$id'";
		
		if(mysqli_query($zbirka, $poizvedbaProjectsUser))
		{
			$poizvedbaProjects="DELETE FROM projects WHERE id='$id'";
			if(mysqli_query($zbirka, $poizvedbaProjects)){
				http_response_code(204);	//OK with no content
			}
			
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