<?php
// Array associativo con valori nutrizionali per grammo
$valoriNutrizionali = [
    "mela" => ["calorie" => 0.52, "proteine" => 0.003, "carboidrati" => 0.14],
    "banana" => ["calorie" => 0.89, "proteine" => 0.011, "carboidrati" => 0.23],
    "pane" => ["calorie" => 2.65, "proteine" => 0.09, "carboidrati" => 0.49]
];

// Recupero dati dal form
$alimento = $_POST['alimento'] ?? '';
$quantita = floatval($_POST['quantita'] ?? 0);

// Controllo che i dati siano validi
if (isset($valoriNutrizionali[$alimento]) && $quantita > 0) {
    $nutrizione = $valoriNutrizionali[$alimento];
    $calorie = $nutrizione['calorie'] * $quantita;
    $proteine = $nutrizione['proteine'] * $quantita;
    $carboidrati = $nutrizione['carboidrati'] * $quantita;
    $grassi = $nutrizione['grassi'] * $quantita;

    echo "<h1>Risultato Calcolo Nutrizione</h1>";
    echo "<p>Alimento: " . htmlspecialchars($alimento) . "</p>";
    echo "<p>Quantità: " . $quantita . " grammi</p>";
    echo "<p>Calorie: " . round($calorie, 2) . " kcal</p>";
    echo "<p>Proteine: " . round($proteine, 2) . " g</p>";
    echo "<p>Carboidrati: " . round($carboidrati, 2) . " g</p>";
    echo "<p>Grassi: " . round($grassi, 2) . " g</p>";
} else {
    echo "<p>Errore: alimento non valido o quantità non corretta.</p>";
}
?>
