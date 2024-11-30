<?php

require_once '../src/Database.php';
require_once '../src/PriceTrackerService.php';

$db = new Database('sqlite:../database/price_tracker.sqlite');
$tracker = new PriceTrackerService($db);

$tracker->checkPrices();
