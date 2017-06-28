<?php

require_once dirname(__DIR__) . '/autoload.php';
require_once dirname(__DIR__) . '/TransIP_AccessToken.php';

$accesstoken = new Transip_Accesstoken();

try {
    $domain_service = new Transip\DomainService($accesstoken);
    var_dump($domain_service->getDomainNames());
    exit(0);
} catch (Exception $ex) {
    echo $ex->getMessage();
    exit(1);
}