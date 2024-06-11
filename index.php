<?php

$companiesStream = fopen('companies.csv', 'r');
$grpoupsStream = fopen('groups.csv', 'r');

$company = [];
$group = [];

while ($result = fgetcsv($companiesStream, null, ',')) {
    
    if(isset($result)) {
        $company[$result[0]] = $result[1];
    }

}

while ($result = fgetcsv($grpoupsStream, null, ',')) {
    
    if(isset($result)) {
        $group[$result[0]] = $result[1];
    }

}

$companyKeys  = [];
$groupKeys  = [];

foreach ($company as $key => $value) {
    $companyKeys[] = $key;
}

foreach ($group as $key => $value) {
    $groupKeys[] = $key;
}

$stream = fopen('apr.csv', 'r');
$stream2 = fopen('new_apr.csv', 'w');
$pattern = '/^https:\/\/.*$/';

$i = false;
$row = '';
$data = [];

$keys = [];

$counter = 0; // ******************************************************************* тестовый счетчик

do {

    if(!$i) {

        fgetcsv($stream);

        $i = true;

    } else {

        $data['date'] = $row[0];

        if(preg_match($pattern, $row[1])) {

            $parseUrl_metric = parse_url($row[1]);

            $data['metric_URL'] = $row[1] ?: 'none';

            $queries = praseQuery($parseUrl_metric['query'], $companyKeys, $groupKeys, $company, $group);

            foreach ($queries as $key => $value) {
                
                $data[$key] = $value;

            }

        }

        if(preg_match($pattern, $row[2])) {

            $parseUrl_fl_visitor = parse_url($row[2]);

            $data['FL_visitor_URL'] = $row[2] ?: 'none';

            $queries = praseQueryFL($parseUrl_fl_visitor['query'], $companyKeys, $groupKeys, $company, $group);

            foreach ($queries as $key => $value) {
                
                $data[$key . ''] = $value;

            }

        }

        $data['metric_ip'] = checkSpace($row[3]) ?: 'none';

        $data['fl_visitors_remote_addr'] = checkSpace($row[4]) ?: 'none';

        $data['metrics_uid'] = checkSpace($row[5]) ?: 'none';

        $data['fl_visitors_ym_uid'] = checkSpace($row[6]) ?: 'none';

        $data['roistat_id'] = checkSpace($row[7]) ?: 'none';

        $data['fingerprint_id'] = checkSpace($row[8]) ?: 'none';

        $data['vid'] = checkSpace($row[9]) ?: 'none';

        $data['roistat_vid'] = checkSpace($row[10]) ?: 'none';

        $dataKeys = array_keys($data);

        foreach ($dataKeys as $value) {
            if(!in_array($value, $keys)) {

                $keys[] = $value;

            }
        }

        // print_r($data);

        // break;

    
        fputcsv($stream2, $data, ';');
        
        // $counter++; //*************************************** test file

        // if($counter == 15) {
        //     break;
        // }
    }  

} while ($row = fgetcsv($stream, null, ';'));

fclose($stream2);

$stream3 = fopen('new_apr_keys.csv', 'w');

fputcsv($stream3, $keys, ';');

// file_put_contents('./new_apr.csv', '');  перезаписать вверх файла

function praseQuery(string $queryString, $companyKeys, $groupKeys, $company, $group): array
{

    $queries = [];

    $querySep = explode('&', $queryString);

    foreach ($querySep as $value) {
        
        $nv = explode('=', $value);

        foreach ($nv as $key => $value) {

            if($value == 'cm_id') {
                
                $parsed = explode('_', $nv[$key + 1]);
    
                $companyKey = array_search($parsed[0], $companyKeys);
                $groupKey = array_search($parsed[1], $groupKeys);
    
                if($companyKey !== false) {
                    // echo $company[$companyKeys[$companyKey]] . ' : ' . $companyKeys[$companyKey] . "\n";
    
                    $queries['company_id_metric'] = $companyKeys[$companyKey];
                    $queries['company_name_metric'] = $company[$companyKeys[$companyKey]];
                }
    
                if($groupKey !== false) {
                    // echo $group[$groupKey[$groupKey]] . ' : ' . $groupKeys[$groupKey] . "\n";
    
                    $queries['group_id_metric'] = $groupKeys[$groupKey ? $groupKey : 'none'];
                    $queries['group_name_metric'] = $group[$groupKeys[$groupKey]];
                }
            }

        }

    }

    return $queries;
}

function praseQueryFL(string $queryString, $companyKeys, $groupKeys, $company, $group): array
{

    $queries = [];

    $querySep = explode('&', $queryString);

    foreach ($querySep as $value) {
        
        $nv = explode('=', $value);

        foreach ($nv as $key => $value) {

            if($value == 'cm_id') {
                
                $parsed = explode('_', $nv[$key + 1]);
    
                $companyKey = array_search($parsed[0], $companyKeys);
                $groupKey = array_search($parsed[1], $groupKeys);
    
                if($companyKey !== false) {
                    // echo $company[$companyKeys[$companyKey]] . ' : ' . $companyKeys[$companyKey] . "\n";
    
                    $queries['company_id_FL'] = $companyKeys[$companyKey];
                    $queries['company_name_FL'] = $company[$companyKeys[$companyKey]];
                }
    
                if($groupKey !== false) {
                    // echo $group[$groupKey[$groupKey]] . ' : ' . $groupKeys[$groupKey] . "\n";
    
                    $queries['group_id_FL'] = $groupKeys[$groupKey ? $groupKey : 'none'];
                    $queries['group_name_FL'] = $group[$groupKeys[$groupKey]];
                }
            }

        }

    }

    return $queries;
}

function parseFragments(string $fragmentsString = ''): array
{
    
    $fragments = [];

        if(strlen($fragmentsString)) {

            $fragments = explode(';', $fragmentsString);

            foreach ($fragments as $key => $value) {
                
                $fragments['fragment_' . $key] = $value;

            }

        }

    return $fragments;

}

function checkSpace(string $str): null|string
{

    $result = trim($str);

    if(mb_strlen($result)) {

        return $str;

    }

    return null;
}