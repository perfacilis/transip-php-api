<?php

require_once dirname(__DIR__) . '/autoload.php';
require_once dirname(__DIR__) . '/TransIP_AccessToken.php';

$accesstoken = new Transip_Accesstoken();

$domain_name = isset($argv[1]) ? $argv[1] : '';
if (!$domain_name) {
    print 'Usage: php -f ' . $argv[0] . ' domain.tld' . PHP_EOL;
    exit(1);
}

try {
    $domain_service = new Transip\DomainService($accesstoken);
    var_dump($domain_service->getWhois($domain_name));
    exit(0);
} catch (Exception $ex) {
    echo $ex->getMessage();
    exit(1);
}