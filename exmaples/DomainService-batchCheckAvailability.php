<?php

require_once dirname(__DIR__) . '/autoload.php';
require_once dirname(__DIR__) . '/TransIP_AccessToken.php';

$accesstoken = new Transip_Accesstoken();

$domain_names = isset($argv[1]) ? explode(',', $argv[1]) : [];
if (!$domain_names) {
    print 'Usage: php -f ' . $argv[0] . ' domain.tld' . PHP_EOL;
    exit(1);
}

try {
    $domain_service = new Transip\DomainService($accesstoken);
    var_dump($domain_service->batchCheckAvailability($domain_names));
    exit(0);
} catch (Exception $ex) {
    echo $ex->getMessage();
    exit(1);
}