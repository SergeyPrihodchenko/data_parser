<?php

$stream = fopen('./apr.csv', 'r');
$stream2 = fopen('new_apr.csv', 'w');
$pattern = '/^https:\/\/.*$/';

$i = false;
$row = '';
$data = [];

do {

    if(!$i) {

        fgetcsv($stream);

        $i = true;

    } else {

        $data['date'] = $row[0];

        if(preg_match($pattern, $row[1])) {

            $parseUrl_metric = parse_url($row[1]);

            $data['scheme_metric'] = $parseUrl_metric['scheme'] ?: 'none';

            $data['host_metric'] = $parseUrl_metric['host'] ?: 'none';

            $data['path_metric'] = $parseUrl_metric['path'] ?: 'none';

            $queries = praseQuery($parseUrl_metric['query']);

            foreach ($queries as $key => $value) {
                
                $data[$key] = $value;

            }

            $fragments = parseFragments($fragmentsString = array_key_exists('fragment', $parseUrl_metric) ? $parseUrl_metric['fragment'] : '');

            if(count($fragments)) {

                foreach ($fragments as $key => $value) {
                
                    $data[$key] = $value;
        
                }                       

            }

        }

        if(preg_match($pattern, $row[3])) {

            $parseUrl_fl_visitor = parse_url($row[2]);

            $data['scheme_FL_visitor'] = $parseUrl_fl_visitor['scheme'] ?: 'none';

            $data['host_FL_visitor'] = $parseUrl_fl_visitor['host'] ?: 'none';

            $data['path_FL_visitor'] = $parseUrl_fl_visitor['path'] ?: 'none';

            $queries = praseQuery($parseUrl_fl_visitor['query']);

            foreach ($queries as $key => $value) {
                
                $data[$key] = $value;

            }

            $fragments = parseFragments($fragmentsString = array_key_exists('fragment', $parseUrl_fl_visitor) ? $parseUrl_fl_visitor['fragment'] : '');

            if(count($fragments)) {

                foreach ($fragments as $key => $value) {
                
                    $data[$key] = $value;
        
                }

            }

        }

        // if(preg_match($pattern, $row[3])) {

    
        // }

        // print_r($data);

        // break;

        // написать шапку
    
        fputcsv($stream2, $data, ';');
        
    }  

} while ($row = fgetcsv($stream, null, ';'));


// file_put_contents('./new_apr.csv', '');  перезаписать вверх файла


function praseQuery(string $queryString): array
{

    $queries = [];

    $querySep = explode('&', $queryString);

    foreach ($querySep as $value) {
        
        $nv = explode('=', $value);

        $queries[$nv[0]] = $nv[1];

    }

    return $queries;
}

function parseFragments(string $fragmentsString = ''): array
{
    
    $fragments = [];

        if(strlen($fragmentsString)) {

            $fragments = explode(';', $fragmentsString);

            foreach ($fragments as $key => $value) {
                
                $fragments[$key] = $value;

            }

        }

    return $fragments;

}