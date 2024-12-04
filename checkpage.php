<?php
/**
 * Tjekker om Højdvangkirkens side om konfirmationer er ændret siden sidst
 *
 * Created with CO-PILOT in 5 minutes :D
 *
 */

// URL to fetch
$url = 'https://www.hoejdevangskirken.dk/konfirmation-2014-2015';

// File to store the previous data
$file = 'pagedata.html';

// Email details
$to = 't@effersoe.net';
$subject = 'Højdevangskinens konfirmationsside ændret!';

function addLineBreaksToHtmlString($htmlString) {
    return preg_replace('/(>)(<)/', "$1\n$2", $htmlString);
}

// Fetch the current data
$currentData = file_get_contents($url);
// Add a line break after each HTML tag
$currentData = addLineBreaksToHtmlString($currentData);

if ($currentData === false) {
    die('Error fetching the URL');
}

echo "<html><head><meta charset='utf-8'>";

// Check if the file exists
if (file_exists($file)) {
    // Read the previous data
    $previousData = file_get_contents($file);

    if ($previousData === false) {
        die('Error reading the previous data file');
    }

    // Save the current and previous data to temporary files
    $tempFile1 = tempnam(sys_get_temp_dir(), 'prev');
    $tempFile2 = tempnam(sys_get_temp_dir(), 'curr');
    file_put_contents($tempFile1, $previousData);
    file_put_contents($tempFile2, $currentData);

    // Use the diff command to find differences
    $diff = shell_exec("diff -u $tempFile1 $tempFile2");


    // Clean up temporary files
    unlink($tempFile1);
    unlink($tempFile2);

    // If there are differences, send an email
    if (!empty($diff)) {
        echo "<h2>Ændringer</h2>" . $diff . PHP_EOL;
        mail($to, $subject, $diff);

        // Update the file with the current data
        file_put_contents($file, $currentData);
    } else {
        echo "<h2>Ingen ændringer siden sidst</h2>";
    }

} else {

    // Save the current data for the first time
    file_put_contents($file, $currentData);
}
