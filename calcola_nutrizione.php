<?php
// Dati nutrizionali degli alimenti (per 100g)
$alimenti = array(
    "riso" => array("calorie" => 130, "proteine" => 2.7, "carboidrati" => 28, "grassi" => 0.3),
    "pollo" => array("calorie" => 165, "proteine" => 31, "carboidrati" => 0, "grassi" => 3.6),
    "insalata" => array("calorie" => 15, "proteine" => 1.3, "carboidrati" => 2.9, "grassi" => 0.2),
    "pasta" => array("calorie" => 131, "proteine" => 5, "carboidrati" => 25, "grassi" => 1.1),
    "tonno" => array("calorie" => 132, "proteine" => 29, "carboidrati" => 0, "grassi" => 1.2),
    "uova" => array("calorie" => 155, "proteine" => 13, "carboidrati" => 1.1, "grassi" => 11),
    "pane" => array("calorie" => 265, "proteine" => 13, "carboidrati" => 43, "grassi" => 3.4),
    "yogurt" => array("calorie" => 59, "proteine" => 10, "carboidrati" => 3.6, "grassi" => 0.4)
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera i dati dal form
    $alimento = $_POST['alimento'];
    $quantita = $_POST['quantita'];
    $pasto = $_POST['pasto'];
    $storico = isset($_POST['storico']) ? $_POST['storico'] : '';
    
    // Verifica che l'alimento esista nell'array
    if (array_key_exists($alimento, $alimenti)) {
        // Calcola i valori nutrizionali
        $fattore = $quantita / 100;
        $calorie = $alimenti[$alimento]["calorie"] * $fattore;
        $proteine = $alimenti[$alimento]["proteine"] * $fattore;
        $carboidrati = $alimenti[$alimento]["carboidrati"] * $fattore;
        $grassi = $alimenti[$alimento]["grassi"] * $fattore;
        
        // Prepara il risultato
        $risultato = "<h3>Risultato per " . ucfirst($alimento) . "</h3>";
        $risultato .= "<p>Quantità: " . $quantita . "g</p>";
        $risultato .= "<p>Calorie: " . round($calorie, 1) . " kcal</p>";
        $risultato .= "<p>Proteine: " . round($proteine, 1) . " g</p>";
        $risultato .= "<p>Carboidrati: " . round($carboidrati, 1) . " g</p>";
        $risultato .= "<p>Grassi: " . round($grassi, 1) . " g</p>";
        
        // Gestione dello storico tramite campo nascosto
        $consumi = array();
        if (!empty($storico)) {
            $consumi = json_decode($storico, true);
        }
        
        $nuovo_consumo = array(
            "alimento" => $alimento,
            "quantita" => $quantita,
            "pasto" => $pasto,
            "calorie" => round($calorie, 1),
            "proteine" => round($proteine, 1),
            "carboidrati" => round($carboidrati, 1),
            "grassi" => round($grassi, 1)
        );
        
        array_push($consumi, $nuovo_consumo);
        
        // Limita lo storico agli ultimi 10 consumi
        if (count($consumi) > 10) {
            $consumi = array_slice($consumi, -10);
        }
        
        // Prepara la tabella dello storico
        $tabella = "";
        foreach ($consumi as $c) {
            $tabella .= "<tr>";
            $tabella .= "<td>" . ucfirst($c['alimento']) . "</td>";
            $tabella .= "<td>" . $c['quantita'] . "</td>";
            $tabella .= "<td>" . ucfirst($c['pasto']) . "</td>";
            $tabella .= "<td>" . $c['calorie'] . "</td>";
            $tabella .= "<td>" . $c['proteine'] . "</td>";
            $tabella .= "<td>" . $c['carboidrati'] . "</td>";
            $tabella .= "<td>" . $c['grassi'] . "</td>";
            $tabella .= "</tr>";
        }
        
        // Converti l'array in JSON per il campo nascosto
        $storico_json = json_encode($consumi);
    } else {
        $errore = "<p class='error'>Errore: alimento non riconosciuto.</p>";
        $risultato = $errore;
        $tabella = isset($_POST['storico_tabella']) ? $_POST['storico_tabella'] : '';
        $storico_json = isset($_POST['storico']) ? $_POST['storico'] : '';
    }
} else {
    // Se il metodo non è POST, reindirizza alla pagina principale
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolatore Valore Nutrizionale</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }
        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #219653;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9f7ef;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            background-color: #e9f7ef;
            color: #27ae60;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Calcolatore Valore Nutrizionale</h1>
        
        <form id="nutritionForm" action="calcola_nutrizione.php" method="POST">
            <div class="form-group">
                <label for="alimento">Alimento:</label>
                <select id="alimento" name="alimento" required>
                    <option value="">Seleziona un alimento</option>
                    <option value="riso" <?php if(isset($alimento) && $alimento == 'riso') echo 'selected'; ?>>Riso</option>
                    <option value="pollo" <?php if(isset($alimento) && $alimento == 'pollo') echo 'selected'; ?>>Pollo</option>
                    <option value="insalata" <?php if(isset($alimento) && $alimento == 'insalata') echo 'selected'; ?>>Insalata</option>
                    <option value="pasta" <?php if(isset($alimento) && $alimento == 'pasta') echo 'selected'; ?>>Pasta</option>
                    <option value="tonno" <?php if(isset($alimento) && $alimento == 'tonno') echo 'selected'; ?>>Tonno</option>
                    <option value="uova" <?php if(isset($alimento) && $alimento == 'uova') echo 'selected'; ?>>Uova</option>
                    <option value="pane" <?php if(isset($alimento) && $alimento == 'pane') echo 'selected'; ?>>Pane integrale</option>
                    <option value="yogurt" <?php if(isset($alimento) && $alimento == 'yogurt') echo 'selected'; ?>>Yogurt greco</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="quantita">Quantità (grammi):</label>
                <input type="number" id="quantita" name="quantita" min="1" step="1" value="<?php if(isset($quantita)) echo $quantita; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="pasto">Tipo di pasto:</label>
                <select id="pasto" name="pasto" required>
                    <option value="">Seleziona</option>
                    <option value="colazione" <?php if(isset($pasto) && $pasto == 'colazione') echo 'selected'; ?>>Colazione</option>
                    <option value="pranzo" <?php if(isset($pasto) && $pasto == 'pranzo') echo 'selected'; ?>>Pranzo</option>
                    <option value="cena" <?php if(isset($pasto) && $pasto == 'cena') echo 'selected'; ?>>Cena</option>
                    <option value="spuntino" <?php if(isset($pasto) && $pasto == 'spuntino') echo 'selected'; ?>>Spuntino</option>
                </select>
            </div>
            
            <!-- Campi nascosti per mantenere lo storico -->
            <input type="hidden" name="storico" value='<?php if(isset($storico_json)) echo $storico_json; ?>'>
            <input type="hidden" name="risultato" value='<?php if(isset($risultato)) echo htmlspecialchars($risultato); ?>'>
            <input type="hidden" name="storico_tabella" value='<?php if(isset($tabella)) echo htmlspecialchars($tabella); ?>'>
            
            <button type="submit">Calcola Valori Nutrizionali</button>
        </form>
        
        <div id="risultato" class="result">
            <?php
            if (isset($risultato)) {
                echo $risultato;
            }
            ?>
        </div>
        
        <h2>Storico Alimenti Consumati</h2>
        <table id="tabellaPasti">
            <thead>
                <tr>
                    <th>Alimento</th>
                    <th>Quantità (g)</th>
                    <th>Pasto</th>
                    <th>Calorie</th>
                    <th>Proteine (g)</th>
                    <th>Carboidrati (g)</th>
                    <th>Grassi (g)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($tabella)) {
                    echo $tabella;
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
