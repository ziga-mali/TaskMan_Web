<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)

include("orodja.php"); 					// Vključitev 'orodij'

$zbirka = dbConnect();					// Pridobitev povezave s podatkovno zbirko

header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

$headersBearer = apache_request_headers();
$headerUsers = $headersBearer;	

if(IdenAndAuth(apache_request_headers(), $zbirka)){
	switch($_SERVER["REQUEST_METHOD"])		// Glede na HTTP metodo v zahtevi izberemo ustrezno dejanje nad virom
	{
		case 'GET':
			if(!empty($_GET["id_project"])){			 
				if(isset($_GET["id_task"])){
					pridobi_nalogo($_GET["id_project"], $_GET["id_task"]);
				} else {
					pridobi_naloge($_GET["id_project"]);
				}
			} else 
			{
				http_response_code(400);
			}
			break;

		case 'POST':
			if(!empty($_GET["id_project"]) && empty($_GET["id_task"])){
				nova_naloga($_GET["id_project"]);
			}
			else{
				http_response_code(400);	// Bad Request
			}
			
			break;
			
		case 'PUT':
			if(!empty($_GET["id_task"])){

				posodobi_nalogo($_GET["id_task"]);

			}else
			{
				http_response_code(400);	// Če ne posredujemo vzdevka je to 'Bad Request'
			}
			break;
		
		case 'DELETE':
			if(!empty($_GET["id_task"]))
			{
				odstrani_nalogo($_GET["id_task"]);
			}
			else
			{
				http_response_code(400);	// Bad Request
			}
			break;
			
		case 'OPTIONS':						//Options dodan zaradi pre-fight poizvedbe za CORS (pri uporabi metod PUT in DELETE)
			http_response_code(204);
			break;
			
		default:
			http_response_code(405);		//Če naredimo zahtevo s katero koli drugo metodo je to 'Method Not Allowed'
			break;
	}
}

mysqli_close($zbirka);					// Sprostimo povezavo z zbirko


// ----------- konec skripte, sledijo funkcije -----------

function pridobi_naloge($id_projekta)
{
	global $zbirka;
	$odgovor=array();
	
	$poizvedba="SELECT id, ime, opis, zac_cas, kon_cas, koncano FROM tasks WHERE id_projekta = '$id_projekta'";	
	
	$rezultat=mysqli_query($zbirka, $poizvedba);
	
	while($vrstica=mysqli_fetch_assoc($rezultat))
	{
		$odgovor[]=$vrstica;
	}
	
	http_response_code(200);		//OK
	echo json_encode($odgovor);
}

function pridobi_nalogo($id_projekta, $id_naloge)
{
	global $zbirka;
	$odgovor = array();
	$id_projekta=mysqli_escape_string($zbirka, $id_projekta);
    $id_naloge=mysqli_escape_string($zbirka, $id_naloge);
	
	$poizvedba="SELECT ime, opis, zac_cas, kon_cas, koncano FROM tasks WHERE id_projekta = '$id_projekta' AND id = '$id_naloge'";
	
	$rezultat=mysqli_query($zbirka, $poizvedba);

	if(mysqli_num_rows($rezultat)>0)	
	{
		$odgovor [] = mysqli_fetch_assoc($rezultat);
		
		http_response_code(200);		//OK
		echo json_encode($odgovor);
	}
	else							
	{
		http_response_code(404);		//Not found
	}
}

function nova_naloga($id_projekta)
{
	global $zbirka, $DEBUG;
	
	$podatki = json_decode(file_get_contents('php://input'), true);
	
	if(isset($podatki["ime"], $podatki["opis"], $podatki["kon_cas"]))
	{
		$ime = mysqli_escape_string($zbirka, $podatki["ime"]);
		$opis = mysqli_escape_string($zbirka, $podatki["opis"]);
		$kon_cas = mysqli_escape_string($zbirka, $podatki["kon_cas"]);
			
        $poizvedbaPost="INSERT INTO tasks (ime, opis, kon_cas, id_projekta) VALUES ('$ime', '$opis', '$kon_cas', $id_projekta)";
        
        if(mysqli_query($zbirka, $poizvedbaPost))
        {
            http_response_code(201);	// Created
            $odgovor=URL_vira($zbirka -> insert_id);
            echo json_encode($odgovor);
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

function posodobi_nalogo($id_naloge)
{
    global $zbirka, $DEBUG;

    $podatki = json_decode(file_get_contents("php://input"), true);
    
    if(existing_task($id_naloge))
    {
        
		$setValues = array();

		if(isset($podatki["ime"])){
			$ime = mysqli_escape_string($zbirka, $podatki["ime"]);
			$setValues[] = "ime='$ime'"; 
		}

		if(isset($podatki["opis"])){
			$opis = mysqli_escape_string($zbirka, $podatki["opis"]);
			$setValues[] = "opis='$opis'"; 
		}

		if(isset($podatki["koncano"])){
			$koncano = mysqli_escape_string($zbirka, $podatki["koncano"]);
			$setValues[] = "koncano='$koncano'"; 
		}

		if(isset($podatki["kon_cas"])){
			$kon_cas = mysqli_escape_string($zbirka, $podatki["kon_cas"]);
			$setValues[] = "kon_cas='$kon_cas'"; 
		}
		
		$setClause = implode(', ', $setValues);		
		echo $setClause;
		
		$poizvedba = "UPDATE tasks SET $setClause WHERE id='$id_naloge'";
		echo $poizvedba;
		
		if(mysqli_query($zbirka, $poizvedba))
		{
			http_response_code(204);    //OK with no content
		}
		else
		{
			http_response_code(500);    // Internal Server Error (ni nujno vedno streznik kriv!)
			
			if($DEBUG)    //Pozor: vračanje podatkov o napaki na strežniku je varnostno tveganje!
			{
				pripravi_odgovor_napaka(mysqli_error($zbirka));
			}
		}
	
    }
    else
    {
        http_response_code(404);    // Not Found
    }
}  
	
	
function odstrani_nalogo($id_naloge)
{	
	global $zbirka, $DEBUG;

	if(existing_task($id_naloge))
	{
		$poizvedba="DELETE FROM tasks WHERE id='$id_naloge'";
		
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