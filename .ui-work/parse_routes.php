<?php
$routes = json_decode(file_get_contents('C:/xampp/htdocs/proyecto2/routes.json'), true);
foreach($routes as $r) {
    echo $r['name'] . '|' . $r['method'] . '|' . $r['uri'] . '|' . $r['action'] . '|' . implode(',', $r['middleware']) . PHP_EOL;
}