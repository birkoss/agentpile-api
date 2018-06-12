<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/account.php';
 
$database = new Database();
$db = $database->getConnection();
 
$account = new Account($db);
 
$account->token = isset($_GET['token']) ? $_GET['token'] : reject("1000", "No Token provided");
$account->platform = isset($_GET['platform']) ? $_GET['platform'] : reject("1001", "No Platform provided");

/* Create a new account if necessary */
if (!$account->get()) {
	$account->date_added = $account->date_changed = date("Y-m-d H:i:s");
	$account->create();

	if (!$account->get()) {
		reject("1002", "Cannot fetch the account");
	}
}

$account_arr = array(
    "id" =>  $account->id,
    "token" => $account->token,
    "platform" => $account->platform,
    "date_added" => $account->date_added,
    "date_changed" => $account->date_changed
);
 
resolve($account_arr);
