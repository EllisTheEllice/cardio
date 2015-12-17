<?php

$pass = 'mypass';
$user = 'myuser';
$dbName = 'cardio';

try {
    $dbh = new PDO('mysql:host=localhost;dbname=' . $dbName, $user, $pass);

    $keys = array('systolic', 'diastolic', 'pulse', 'date');

    if ($_POST['data'] !== null) { //ajax call
        parse_str($_POST['data'], $output);
        $systole = $output['systolic'];
        $diastole = $output['diastolic'];
        $pulse = $output['pulse'];
        $timestamp = $output['date'];

        $sth = $dbh->prepare('INSERT INTO `values` (systole,diastole,pulse,timestamp) VALUES (:systole,:diastole,:pulse,:timestamp)');
        $sth->bindParam(':systole', $systole, PDO::PARAM_INT);
        $sth->bindParam(':diastole', $diastole, PDO::PARAM_INT);
        $sth->bindParam(':pulse', $pulse, PDO::PARAM_INT);
        $sth->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, 10);
        $sth->execute();
    } else {
        $stmt = $dbh->query('SELECT * FROM `values` ORDER BY id');
        $all = $stmt->fetchAll();
        echo json_encode($all);
    }

    $dbh = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
