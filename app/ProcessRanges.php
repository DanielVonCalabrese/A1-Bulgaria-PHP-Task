<?php

require_once "Connection.php";
require_once "NumericRanges.model.php";

use TestTaskA1\Connection as Connection;
use TestTaskA1\NumericRanges as NumericRanges;

try {
    $pdo = Connection::get()->connect();
} catch(\PDOException $e) {
    echo $e->getMessage();
}
		
$numericRangesModel = new NumericRanges($pdo);
$result = $numericRangesModel->updateItem($_POST);
print_r(json_encode($result));		
die;