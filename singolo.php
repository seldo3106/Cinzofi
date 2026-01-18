<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "cinzofi";

$connessione = new mysqli($host,$user,$password,$database);

if($connessione == false){
    die("Errore di connessione: " . $connessione->connect_error);
}

$id = $_GET['id'];

//Salva la ennupla con le info di un singolo
$temp = $connessione->query("SELECT * FROM singolo WHERE idsingolo = ". $id);
$singolo = $temp->fetch_array();

//Salva la ennupla con le info di un album
$temp = $connessione->query("SELECT * FROM album WHERE idalbum = ". $singolo['idalbum']);
$album = $temp->fetch_array();
$nomeImmagine = str_replace("?","",$album['nome']);

//Salva la ennupla con le info di chi l'ha cantato
$idartisti = $connessione->query("SELECT * FROM cantanti_singolo WHERE idsingolo = ". $id);

//Estrapola il nome di chi ha fatto la canzone per trovare la cartella dove è salvata la copertina dell'album o singolo
$temp = $connessione->query("SELECT * FROM cantanti_singolo WHERE idsingolo = ". $id. " AND grado = 1");
$idartista = $temp->fetch_array();
$temp = $connessione->query("SELECT * FROM artista WHERE idartista = ". $idartista['idartista']);
$artista = $temp->fetch_array();


//Salva la ennupla con le info di chi l'ha prodotto
$idprod = $connessione->query("SELECT * FROM prod_singolo WHERE idsingolo = ". $id);

?>

<!DOCTYPE html>
<html>
    <head>
        <title><?= $singolo['nome'] ?></title>
        <link rel="stylesheet" href="singolo.css">
		<link rel="stylesheet" href="style_236451.css">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <div class="barra" style="background-image: linear-gradient(#<?= $album['colore1'] ?>,#<?= $album['colore2'] ?>);">
			<img src="copertine/<?= $artista['nome'] ?>/<?= $nomeImmagine ?>.png" width="300" height="300" id="copertina">
			<div id="tedio">
				<h1><?= $singolo['nome'] ?></h1>
                <div id="info">
                    <div class="dati">
                        <h4 id="dati1">Prodotto da:</h4>
                        <p id="info1"><?php
                            $produttori = [];
                            while($temp = $idprod->fetch_array()){
                                $tempo = $connessione->query("SELECT * FROM artista WHERE idartista = ". $temp['idartista']);
                                $prod = $tempo->fetch_array();
                                $produttori[] = $prod['nome'];
                            }
                            echo implode(", ",$produttori);
                        ?>
                        </p>
                    </div>
                    <div class="dati">
                        <h4 id="dati2">Cantato da:</h4>
                        <p id="info2"><?php
                            $cant = [];
                            while($temp = $idartisti->fetch_array()){
                                $tempo = $connessione->query("SELECT * FROM artista WHERE idartista = ". $temp['idartista']);
                                $cantante = $tempo->fetch_array();
                                $cant[] = $cantante['nome'];
                            }
                            echo implode(", ",$cant);
                        ?></p>
                    </div>
                </div>
				<a href="<?= $singolo['link'] ?>" id="collegamento">Collegamento alla canzone</a>
			</div>
		</div>
        <a href="artisti.php?id=<?= $artista['idartista'] ?>">
			<div id="indietro">
				<img src="asset/left-arrow.png" height="75px">
			</div>
		</a>
        <div id="testo">
            <h3>Testo della canzone</h3>
            <p><?= $singolo['testo'] ?></p>
        </div>
    </body>
</html>