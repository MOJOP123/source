<?php

$obj = ( isset($_GET['obj']) ? $_GET['obj'] : false );
$object__id = ( isset($_GET['object__id']) ? intval($_GET['object__id']) : 0 );

if(!intval(config_var(12678))){
    die('Algolia is currently disabled');
}

//Call the update function and passon possible values:
view_json(update_algolia($obj, $object__id));