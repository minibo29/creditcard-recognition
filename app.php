<?php
require_once 'vendor/autoload.php';

use App\Container;
use App\DTO\TransactionsMapper;
use App\Services\CommissionTransactionService;

$container = new Container;

/** @var CommissionTransactionService $commissionTransactionService */
$commissionTransactionService = $container->get(CommissionTransactionService::class);
/** @var TransactionsMapper $transactionsMapper */
$transactionsMapper = $container->get(TransactionsMapper::class);

$file_handle = fopen($argv[1], 'r');
while (!feof($file_handle)) {
    $fileLine = fgets($file_handle);
    $transactionsDto = $transactionsMapper->mapDto($fileLine);
    if (!$transactionsDto)
        continue;
    try {
        echo $commissionTransactionService->getTransactionCommission($transactionsDto) . PHP_EOL;
    } catch (\Exception $exception) {
        //log exception
    }
}

fclose($file_handle);
