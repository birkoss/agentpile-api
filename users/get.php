<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/user.php';
 
$database = new Database();
$db = $database->getConnection();

$user = new User($db);
 
$user->id = isset($_GET['id']) ? $_GET['id'] : reject("2000", "No ID provided");
$user->account_id = isset($_GET['account_id']) ? $_GET['account_id'] : reject("2001", "No Account ID provided");

/* Must create a new user */
if (preg_match('/^tmp_/', $user->id)) {
	$user->id = null;
	$user->date_added = $user->date_changed = date("Y-m-d H:i:s");
	$user->create();
}

if (!$user->get()) {
	reject("2002", "Cannot match this user with this account");
}

if (isset($_GET['status'])) {
	switch($_GET['status']) {
		case 'deleted':
			$user->date_deleted = $user->date_changed = date("Y-m-d H:i:s");
			$user->update();
			break;
	}
}

$user_arr = array(
    "id" =>  $user->id,
    "account_id" => $user->account_id,
    "date_added" => $user->date_added,
    "date_changed" => $user->date_changed,
    "date_deleted" => $user->date_deleted
);

resolve($user_arr);
