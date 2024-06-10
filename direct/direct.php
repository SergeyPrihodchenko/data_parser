<?php

$stream = fopen('direct.csv', 'r');

$stream2 = fopen('groups.csv', 'w');

$stream3 = fopen('compoanies.csv', 'w');

$i = 0;

$group = [];
$company = [];

do {

    if($i > 3) {

        $group[$result[4]] = $result[5]; 

        $company[$result[9]] = $result[10];

    } else {

        $i++;
        continue;

    }

} while ($result = fgetcsv($stream, null, "\t"));

foreach ($group as $key => $value) {
    fputcsv($stream2, [$key, $value]);
}

fclose($stream2);

foreach ($company as $key => $value) {
    fputcsv($stream3, [$key, $value]);
}

fclose($stream3);

$data = [];

$stream = fopen('cm_id.csv', 'r');

$stream2 = fopen('groups.csv', 'r');

$stream3 = fopen('companies.csv', 'r');

$cmGr = [];

$companies = [];

$group = [];

while ($row = fgetcsv($stream, null, ';')) {
    $cmGr[] = $row;
}

while ($company = fgetcsv($stream3, null, ',')) {
    $companies[$company[1]] = $company[0];
}

while ($group = fgetcsv($stream2, null, ',')) {
    $groups[$group[1]] = $group[0];
}

foreach ($cmGr as $key => $el) {
    $comp_id = array_search($el[1], $companies);
    $grop_id = array_search($el[2], $groups);

    if($comp_id !== false) {
        $data[$key] = [$el[0], $el[1], $comp_id, $el[2], $grop_id];
    }
}

$stream4 = fopen('result.csv', 'w');

fputcsv($stream4, ['DATE', 'COMPANY_ID', 'COMPANY_NAME', 'GROUP_ID', 'GROUP_NAME'], ';');

foreach ($data as $value) {
    fputcsv($stream4, $value, ';');
}