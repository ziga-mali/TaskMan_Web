<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)

include("orodja.php"); 					// Vključitev 'orodij'

$zbirka = dbConnect();					// Pridobitev povezave s podatkovno zbirko

header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

error_log($_SERVER["REQUEST_METHOD"]);
error_log($_GET["id"]);

if(IdenAndAuth(apache_request_headers(), $zbirka)){
	if($_SERVER["REQUEST_METHOD"] == 'GET')	
		{
			pridobi_projekte($_GET["id"]);
		}
	else{
		http_response_code(405);
	}		
}else{
	http_response_code(401);
}

function pridobi_projekte($id)

{
	global $zbirka;
    $odgovor = array();
    $id = mysqli_escape_string($zbirka, $id);

    // Check if the user is admin
    $adminCheckQuery = "SELECT admin FROM user WHERE id = $id";
    $adminCheckResult = mysqli_query($zbirka, $adminCheckQuery);

    if ($adminCheckResult) {
        $adminStatus = mysqli_fetch_assoc($adminCheckResult)['admin'];

        if ($adminStatus) {
            $poizvedba = "SELECT * FROM projects";
        } else {
            $poizvedba = "SELECT p.* 
            FROM projects AS p 
            JOIN projectsuser AS pu ON p.id = pu.id_projekta 
            JOIN user AS u ON pu.id_userja = u.id 
            WHERE u.id = $id AND p.koncan = 0";
        }

        $rezultat = mysqli_query($zbirka, $poizvedba);

        while ($vrstica = mysqli_fetch_assoc($rezultat)) {
            $odgovor[] = $vrstica;
        }

        http_response_code(200);
        error_log("Response data: " . json_encode($odgovor)); // OK
        echo json_encode($odgovor);
    } else {
        http_response_code(500);
        error_log("Error checking admin status: " . mysqli_error($zbirka));
        echo json_encode(array("error" => "Internal Server Error"));
    }
}
?>