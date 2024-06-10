<?php

$cmId = ['campaign_id', 'gb_id', 'banner_id', 'pharse_id', 'retargeting_id'];

$stream = fopen('new_apr.csv', 'r');
$stream2 = fopen('cm_id.csv', 'w');

$title = false;

do {
    


    $cmData = explode('_', $result[7]);



    fputcsv($stream2, [$result[0], $cmData[0], $cmData[1]], ';');

} while ($result = fgetcsv($stream, null, ';'));

