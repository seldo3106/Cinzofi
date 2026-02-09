<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "cinzofi";

$connessione = new mysqli($host, $user, $password, $database);

if($connessione->connect_error){ 
	die("Errore: " . $connessione->connect_error); 
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
	//Funziona solo se non è vuoto
	if(!empty($_POST['cerca'])){
		//Prende dal form la ricerca dell'utente
		$cerca = $_POST['cerca'];
		//Creo la query qui per imperdire un possibile sql injection
		$query = sprintf("SELECT idartista,nome FROM artista WHERE nome = '%s'",$connessione->real_escape_string($cerca));
		//Manda la richiesta al db
		$nomiArtisti = $connessione->query($query);
		$nome = $nomiArtisti->fetch_array();

		//Se è vuoto non va
		if(!empty($nome)){
			header("location: artisti.php?id=". $nome['idartista']);
			exit();
		}
	}
	
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Cinzofi</title>
		<link rel="stylesheet" href="home.css">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	</head>
	<body>
		<div class="barra">
			<!-- <h1 id="titolo_barra">ciao, spero piaccia<br>(non ho un logo)</h1> -->
			<!-- <img src="logo_par.png" height="50px"> -->
			<div class="barra-interna barra-sinistra">
				<i class="fas fa-music"></i>
			</div>
			<form action="" method="post" class="barra-interna">
				<input type="text" name="cerca" id="cerca">
				<button type="submit">Cerca</button>
			</form>
			
		</div>
		<div class="corpo">
			<h3>scegli quale di questi cantanti vuoi vedere</h3>
			<div class="corpus">
				<div class="artista">
					<p>tha supreme</p>
					<a href="artisti.php?id=1"><img src="copertine/thasup/thasup.gif" width="200px" height="200px"></a>
				</div>
				<div class="artista">
					<p>Ghali</p>
					<a href="artisti.php?id=2"><img src="https://media.tenor.com/U1ER_zJkAJEAAAAM/ghali-face-weird.gif" width="200px" height="200px"></a>
				</div>
				<div class="artista">
					<p>Lazza</p>
					<a href="artisti.php?id=3"><img src="copertine/Lazza/Lazza.png" width="200px" height="200px"></a>
				</div>
				<div class="artista">
					<p>Kid Yugi</p>
					<a href="artisti.php?id=4"><img src="copertine/Kid Yugi/Kid Yugi.jpeg" width="200px" height="200px"></a>
				</div>
				<div class="artista">
					<p>Dark Polo Gang</p>
					<a href="artisti.php?id=13"><img src="copertine/Dark Polo Gang/Dark Polo Gang.gif" width="200px" height="200px"></a>
				</div>
			</div>
		</div>
	</body>
	
</html>