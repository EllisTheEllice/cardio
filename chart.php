<?php

//Hole dir die Werte
$unserialize = unserialize(file_get_contents('cardio.txt'));
$systole = array();
$diastole = array();
$pulse = array();

// Breite/Höhe des Diagramms
$imgBreite = 900;
$imgHoehe = 900;

// Image-Objekt erzeugen und Farben definieren
$bild = imagecreate($imgBreite, $imgHoehe);
$farbeWeiss = imagecolorallocate($bild, 255, 255, 255);
$farbeGrau = imagecolorallocate($bild, 220, 220, 220);
$farbeBlau = imagecolorallocate($bild, 0, 0, 255);
$farbeSchwarz = imagecolorallocate($bild, 0, 0, 0);
$farbeRot = imagecolorallocate($bild, 255, 0, 0);
$farbeHellrot = imagecolorallocate($bild, 255, 100, 100);
$farbeHellblau = imagecolorallocate($bild, 100, 100, 255);

foreach ($unserialize as $arr => $val) {
    $systole[] = $val['systolic'];
    $diastole[] = $val['diastolic'];
    $pulse[] = $val['pulse'];
}

// Skalierung berechnen
$vertical_scale = ($imgHoehe / maximum()) - 1; //Vertikale Skalierung berechnen
$horizontal_scale = $imgBreite / count($pulse); //Horizontale Skalierung berechnen
// PNG-Grafik definieren
header("Content-type: image/png");

// Rand für die Grafik erzeugen
imageline($bild, 0, 0, 0, $imgHoehe, $farbeGrau);
imageline($bild, 0, 0, $imgBreite, 0, $farbeGrau);
imageline($bild, $imgBreite - 1, 0, $imgBreite - 1, $imgHoehe - 1, $farbeGrau);
imageline($bild, 0, $imgHoehe - 1, $imgBreite, $imgHoehe - 1, $farbeGrau);


// Raster erzeugen
for ($i = 1; $i < $imgHoehe; $i++) {
    imageline($bild, 0, $i * 25, $imgBreite, $i * 25, $farbeGrau);
}
for ($i = 1; $i < $imgBreite; $i++) {
    imageline($bild, $i * 25, 0, $i * 25, $imgHoehe, $farbeGrau);
}

// Legende erzeugen
imagettftext($bild, 12, 0, 10, 20, $farbeBlau, "./arial.ttf", 'Diastole');
imagettftext($bild, 12, 0, 10, 40, $farbeRot, "./arial.ttf", 'Systole');
imagettftext($bild, 12, 0, 10, 60, $farbeSchwarz, "./arial.ttf", 'Puls');

//Ideallinien zeichnen
if (!isset($_GET['no_systolic']))
    imagedashedline($bild, 0, $imgHoehe - 120 * $vertical_scale, $imgBreite, $imgHoehe - 120 * $vertical_scale, $farbeHellrot); //Ideallinie Systole
if (!isset($_GET['no_diastolic']))
    imagedashedline($bild, 0, $imgHoehe - 80 * $vertical_scale, $imgBreite, $imgHoehe - 80 * $vertical_scale, $farbeHellblau); //Ideallinie Diastole
//
//Liniendiagramm zeichnen und Durchschnitt berechnen
$systole_durchschnitt = 0;
$diastole_durchschnitt = 0;
$puls_durchschnitt = 0;

for ($i = 0; $i < count($diastole); $i++) {
    if ($diastole[$i + 1] != null) { //Null Linie Vermeiden 
        if (!isset($_GET['no_diastolic']))
            imageline($bild, $i * $horizontal_scale, ($imgHoehe - $diastole[$i] * $vertical_scale), ($i + 1) * $horizontal_scale, ($imgHoehe - $diastole[$i + 1] * $vertical_scale), $farbeBlau);
        if (!isset($_GET['no_systolic']))
            imageline($bild, $i * $horizontal_scale, ($imgHoehe - $systole[$i] * $vertical_scale), ($i + 1) * $horizontal_scale, ($imgHoehe - $systole[$i + 1] * $vertical_scale), $farbeRot);
        if (!isset($_GET['no_pulse']))
            imageline($bild, $i * $horizontal_scale, ($imgHoehe - $pulse[$i] * $vertical_scale), ($i + 1) * $horizontal_scale, ($imgHoehe - $pulse[$i + 1] * $vertical_scale), $farbeSchwarz);
    }
    if (!isset($_GET['no_diastolic']))
        imagettftext($bild, 10, 0, ($i) * $horizontal_scale, ($imgHoehe - $diastole[$i] * $vertical_scale), $farbeBlau, "./arial.ttf", $diastole[$i]);
    if (!isset($_GET['no_systolic']))
        imagettftext($bild, 10, 0, ($i) * $horizontal_scale, ($imgHoehe - $systole[$i] * $vertical_scale), $farbeRot, "./arial.ttf", $systole[$i]);
    if (!isset($_GET['no_pulse']))
        imagettftext($bild, 10, 0, ($i) * $horizontal_scale, ($imgHoehe - $pulse[$i] * $vertical_scale), $farbeSchwarz, "./arial.ttf", $pulse[$i]);

    $systole_durchschnitt+=$systole[$i];
    $diastole_durchschnitt+=$diastole[$i];
    $puls_durchschnitt+=$pulse[$i];
}

//Durchscnitt berechnen und ausgeben
$systole_durchschnitt = $systole_durchschnitt / count($systole);
$diastole_durchschnitt = $diastole_durchschnitt / count($diastole);
$puls_durchschnitt = $puls_durchschnitt / count($pulse);
imagettftext($bild, 12, 0, 70, 20, $farbeBlau, "./arial.ttf", "&oslash; " . round($diastole_durchschnitt, 1));
imagettftext($bild, 12, 0, 70, 40, $farbeRot, "./arial.ttf", "&oslash; " . round($systole_durchschnitt, 1));
imagettftext($bild, 12, 0, 70, 60, $farbeSchwarz, "./arial.ttf", "&oslash; " . round($puls_durchschnitt, 1));

// Diagramm ausgeben und Grafik
// aus dem Speicher entfernen
imagepng($bild, 'chart.png');
imagepng($bild);
imagedestroy($bild);

/**
 * Berechnet das Maximum aller Werte
 * @global type $pulse Alle Puls Werte
 * @global type $diastole Alle Diastolischen Werte
 * @global type $systole Alle Systolischen Werte
 * @return type integer Das Maximum als Ganzzahl
 */
function maximum() {
    global $pulse, $diastole, $systole;
    return max(max($pulse), max($diastole), max($systole));
}

?>