<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "cinzofi";

$connessione = new mysqli($host,$user,$password,$database);

if($connessione->connect_error){
    die("Errore di connessione: " . $connessione->connect_error);
}

//Prende l'id dall'url come intero, per evitare SQL injection
$id = (int) $_GET['id'];

//Fa una richiesta per ottenere la ennupla dell'artista della pagina
$richiesta = $connessione->query("SELECT * FROM artista WHERE idartista = " . $id);
$artista = $richiesta->fetch_array();
//Se l'utente modifica l'id con uno inesistente rimanda alla pagina principale
if(empty($artista)){
	header("location: home.php");
	exit();
}

//Cerca tutti gli album associati all'artista e li mette in ordine cronologico mettendo le più recenti in alto
$album = $connessione->query("SELECT * FROM album INNER JOIN cantanti_album ON album.idalbum = cantanti_album.idalbum WHERE cantanti_album.idartista = ". $id . " ORDER BY data_pub DESC");

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?= $artista['nome'] ?></title>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="artista.css">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
		<a href="home.php">
			<div id="indietro">
				<img src="asset/left-arrow.png" height="75px">
			</div>
		</a>
		<div class="barra">
			<h1><?= $artista['nome'] ?></h1>
			<div id="info">
				<!-- <img src="copertine/<?= $artista['nome'] ?>/<?= $artista['nome'] ?>.gif"> -->
				<div id="testo">
					<p><?= $artista['descrizione'] ?></p>
				</div>
			</div>
		</div>
		
		<div class="corpo">
            <?php

			//Fa un ciclo per caricare tutti gli album
            while($tempora = $album->fetch_array()){

				//Per vedere se è un album, mixtape o EP
				switch($tempora['tipo']){
					case 1:
						$tipo = "Album";
						break;
					case 2:
						$tipo = "Mixtape";
						break;
					case 3:
						$tipo = "EP";
						break;
				}
					
				//Per evitare di avere problemi con il nome dell'immagine
				$nomeImmagine = str_replace("?","",$tempora['nome']);

				//Variabile dove viene scritto il testo html
                $testoHTML = '
            <div class="album">
				<h3>' . $tempora['nome'] . ' - '. $tipo .'</h3>
				<img src="copertine/'. $artista['nome'] .'/'. $nomeImmagine .'.'. $tempora['formato'] .'" height="400px">
				<ol class="tracce">';

				
				//Variabile dove vengono salvati le ennuple che contengono i singoli dell'album
				$tempo = $connessione->query("SELECT * FROM singolo WHERE idalbum = " . $tempora['idalbum'] . " ORDER BY num_traccia ASC");

				//Ciclo per la lista delle canzoni
				while($singolo = $tempo->fetch_array()){
					$testoHTML .= '<li><a href="singolo.php?id=' . $singolo['idsingolo'] . '">'. $singolo['nome'] .'</a></li>';
				}
				$testoHTML .= '</ol></div>';

				echo $testoHTML;
            }
            ?>
		</div>
		
		<?php
			//Cerca i singoli senza album associati all'artista e li mette in ordine cronologico
			$richiesta = $connessione->query("SELECT * FROM singolo INNER JOIN cantanti_singolo ON singolo.idsingolo = cantanti_singolo.idsingolo WHERE cantanti_singolo.idartista = ". $id . " AND singolo.idalbum IS NULL ORDER BY data_pub DESC");

			//Controlla se ci sono singoli
			if($richiesta->num_rows > 0){
				$testoHTML = '
		<div class="barraSin">
			<h4>Singoli senza album</h4>
			<div class="singoli">
				';

				while($singolo = $richiesta->fetch_array()){
					$testoHTML .= '
		<a href="singolo.php?id='. $singolo['idsingolo'] .'"><div class="singolo">
		<h3>'. $singolo['nome'] .'</h3>
		<img src="copertine/'. $artista['nome'] .'/'. $singolo['nome'] .'.png">
		</div></a>';
					
				}
				
				$testoHTML .= '<div><div>';
				echo $testoHTML;
			}
			
		?>
	</body>
</html>