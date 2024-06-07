<?php

$stream = fopen('new_apr_keys.csv', 'r');

$keys = fgetcsv($stream, null, ';');

fclose($stream);

$stream = fopen('tests.csv', 'w');

fputcsv($stream, $keys);

$stream2 = fopen('new_apr.csv', 'r');

while ($row = fgetcsv($stream2, null, ';')) {
    
    fputcsv($stream, $row, ';');
}