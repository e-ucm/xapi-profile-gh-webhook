<?php

require_once dirname(__DIR__).'/bootstrap.php';

$envvars=array('XAPI_PROFILE_GENERATOR_QUEUE_FOLDER' => XAPI_PROFILE_GENERATOR_QUEUE_FOLDER, 'XAPI_PROFILE_PUBLIC_SITE' => XAPI_PROFILE_PUBLIC_SITE, 'PWD' => __DIR__);

if ($argc > 1 && array_key_exists($argv[1], $envvars)) {
    echo $envvars[$argv[1]];
}
