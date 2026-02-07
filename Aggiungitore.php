<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "cinzofi";

$connessione = new mysqli($host, $user, $password, $database);
if ($connessione->connect_error) { die("Errore: " . $connessione->connect_error); }

$messaggio = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recupero dati e conversione testo con <br> (se presente)
    $idalbum = !empty($_POST['idalbum']) ? $_POST['idalbum'] : null;
    $num_traccia = !empty($_POST['num_traccia']) ? $_POST['num_traccia'] : 0;
    $data_pub = !empty($_POST['data_pub']) ? $_POST['data_pub'] : date("Y-m-d");
    $nome = $_POST['nome'];
    $link = $_POST['link'];
    $testo = nl2br($_POST['testo']); 
    
    // 2. Calcolo nuovo ID Singolo (Ultimo + 1)
    $res = $connessione->query("SELECT MAX(idsingolo) AS max_id FROM singolo");
    $row = $res->fetch_assoc();
    $nuovo_id = $row['max_id'] + 1;

    // 3. Inserimento nella tabella 'singolo'
    $stmt = $connessione->prepare("INSERT INTO singolo (idsingolo, nome, testo, data_pub, link, idalbum, num_traccia) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssii", $nuovo_id, $nome, $testo, $data_pub, $link, $idalbum, $num_traccia);
    
    if ($stmt->execute()) {
        // 4. Gestione Artisti (Principale + Feat)
        if(!empty($_POST['id_artista_principale'])){
            $id_principale = $_POST['id_artista_principale'];
            $stmt_art = $connessione->prepare("INSERT INTO cantanti_singolo (idartista, idsingolo, grado) VALUES (?, ?, 1)");
            $stmt_art->bind_param("ii", $id_principale, $nuovo_id);
            $stmt_art->execute();
        }

        if(!empty($_POST['id_feats'])){
            $feats = explode(',', $_POST['id_feats']);
            foreach ($feats as $f_id) {
                $f_id = trim($f_id);
                if(!empty($f_id)){
                    $stmt_f = $connessione->prepare("INSERT INTO cantanti_singolo (idartista, idsingolo, grado) VALUES (?, ?, 0)");
                    $stmt_f->bind_param("ii", $f_id, $nuovo_id);
                    $stmt_f->execute();
                }
            }
        }

        // 5. Gestione Produttori
        if(!empty($_POST['id_produttori'])){
            $prods = explode(',', $_POST['id_produttori']);
            foreach ($prods as $p_id) {
                $p_id = trim($p_id);
                if(!empty($p_id)){
                    $stmt_p = $connessione->prepare("INSERT INTO prod_singolo (idartista, idsingolo) VALUES (?, ?)");
                    $stmt_p->bind_param("ii", $p_id, $nuovo_id);
                    $stmt_p->execute();
                }
            }
        }
        
        $messaggio = "<p class='success'>Dati inviati! Nuovo ID: $nuovo_id</p>";
    } else {
        $messaggio = "<p class='error'>Errore: " . $connessione->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Admin Relaxed - Cinzofi</title>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Work Sans', sans-serif; background-color: #FFFACC; padding: 20px; }
        .form-container { background: white; max-width: 600px; margin: auto; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #CFBCDF; text-align: center; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        input, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        textarea { height: 100px; grid-column: span 2; }
        .full-width { grid-column: span 2; }
        button { width: 100%; padding: 15px; background-color: #CFBCDF; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; margin-top: 20px; }
        button:hover { background-color: #bfa8cf; }
        .success { color: #4CAF50; font-weight: bold; text-align: center; }
        .error { color: #F44336; font-weight: bold; text-align: center; }
        label { font-size: 0.8em; font-weight: bold; color: #666; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Inserimento Rapido Singolo</h2>
    <?= $messaggio ?>
    
    <form method="POST" action="">
        <div class="grid">
            <div>
                <label>ID Album</label>
                <input type="number" name="idalbum">
            </div>
            <div>
                <label>Num. Traccia</label>
                <input type="number" name="num_traccia">
            </div>
            <div class="full-width">
                <label>Nome Canzone</label>
                <input type="text" name="nome">
            </div>
            <div class="full-width">
                <label>Data Pubblicazione</label>
                <input type="date" name="data_pub">
            </div>
            <div class="full-width">
                <label>Link</label>
                <input type="text" name="link">
            </div>
            
            <div class="full-width">
                <label>ID Artista Principale</label>
                <input type="number" name="id_artista_principale">
            </div>
            <div>
                <label>ID Feat (es: 2,5)</label>
                <input type="text" name="id_feats">
            </div>
            <div>
                <label>ID Produttori (es: 1,3)</label>
                <input type="text" name="id_produttori">
            </div>

            <label class="full-width">Testo</label>
            <textarea name="testo"></textarea>
        </div>

        <button type="submit">INVIA DATI</button>
    </form>
</div>

</body>
</html>