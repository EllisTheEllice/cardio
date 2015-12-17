<?php

$file = './cardio.txt';





if (isset($_GET['type'])) {
    switch ($_GET['type']) {
        case 'csv':
            $csvdata = exportToCSV(); // <- hier die csv-Daten rein
            header("content-type: application/csv-tab-delimited-table");
            header("content-length: " . strlen($csvdata));
            header("content-disposition: attachment; filename=\"cardio.csv\"");
            exit($csvdata);
            break;
        case 'pdf':
            exportToPDF();
            exit();
            break;
        case 'data':
            exit(readAll($_GET['page']));
            break;
    }
}

if (isset($_POST['data']) || isset($_POST['systolic'])) {
    parse_str($_POST['data'], $output);

    $systolic=null;
    $diastolic=null;
    $pulse=null;
    $date=null;
    
    
    
    
    if (empty($output)) {
        $systolic=$_POST['systolic'];
        $diastolic=$_POST['diastolic'];
        $pulse=$_POST['pulse'];
        $date=$_POST['date'];
    } else {
        $systolic = $output['systolic'];
        $diastolic = $output['diastolic'];
        $pulse = $output['pulse'];
        $date = $output['date'];
    }
    
    
//    if ((ctype_digit($systolic) || is_int($systolic)) && (ctype_digit($diastolic) || is_int($diastolic)) && (ctype_digit($pulse) || is_int($pulse))) {
        add($systolic, $diastolic, $pulse, $date);
        echo json_encode(array('date' => $date, 'systolic' => $systolic, 'diastolic' => $diastolic, 'pulse' => $pulse));
//        echo readAll();
//        exportToCSV();
//    } else {
//        exit(json_encode(array('error' => 'Not a number')));
//    }
}

function add($sys, $dia, $pulse, $date) {
    global $file;

    $values = null;
    if (file_exists($file)) {
        $values = unserialize(file_get_contents($file));
    } else {
        $values = array();
    }
    $filepointer = fopen($file, "w");
    $values[] = array('systolic' => $sys, 'diastolic' => $dia, 'pulse' => $pulse, 'date' => $date);
    fputs($filepointer, serialize($values));
    fclose($filepointer);
}

function readAll($page) {
    global $file;


    $unserialize = unserialize(file_get_contents($file));
    if (is_array($unserialize)) {
        if ($page) {
            $retVal = array();
            if ($page === 'last') {
                for ($i = count($unserialize) - 10; $i < count($unserialize); $i++) {
                    $retVal[] = $unserialize[$i];
                }
            } else {
                if ($page < 1)
                    $page = 1;
                $retVal = array();
                for ($i = ($page - 1) * 10; $i < $page * 10; $i++) {
                    $retVal[] = $unserialize[$i];
                }
            }
            return json_encode(array('count' => count($unserialize), 'data' => $retVal));
        } else {
            return json_encode(array('count' => count($unserialize), 'data' => $unserialize));
        }
    } else {
        return json_encode(array('error' => 'could not unserialize'));
    }
}

function exportToCSV() {
    global $file;

    $values = unserialize(file_get_contents($file));
    $filepointer = fopen('export.csv', 'w');
//    fputcsv($filepointer, array_keys($values[0]));
    foreach ($values as $fields) {
        fputcsv($filepointer, $fields);
    }
    fclose($filepointer);
    return file_get_contents('export.csv');
}

function exportToPDF() {
    global $file;

    define('FPDF_FONTPATH', '/var/www/cardio/fpdf/font/');
    include 'fpdf/fpdf.php';

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->Image('chart.png', 10, 10, 180, 180, 'PNG');

    //Tabellenkopf
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(10, 200);
    $pdf->Cell(30, 8, "Datum", 0, 0, 'C');
    $pdf->Cell(30, 8, "Systole/Diastole", 0, 0, 'C');
    $pdf->Cell(30, 8, "Puls", 0, 0, 'C');
    $pdf->Ln();

    //Tabelleninhalt
    $pdf->SetFont('Arial', '', 10);
    $values = unserialize(file_get_contents($file));
    foreach ($values as $key => $value) {
        $pdf->Cell(30, 8, $value['date'], 0, 0, 'C');
        $pdf->Cell(30, 8, $value['systolic'] . '/' . $value['diastolic'], 0, 0, 'C');
        $pdf->Cell(30, 8, $value['pulse'], 0, 0, 'C');
        $pdf->Ln();
    }

    $pdf->Output();
}

?>
