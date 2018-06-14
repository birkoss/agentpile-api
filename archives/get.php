<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/archive.php';
include_once '../objects/user.php';
 
$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$user->id = isset($_GET['user_id']) ? $_GET['user_id'] : reject("4003", "No User ID provided");
$user->account_id = isset($_GET['account_id']) ? $_GET['account_id'] : reject("4001", "No Account ID provided");

if (!$user->get()) {
	reject("2002", "Cannot match this user with this account");
}

$archive = new Archive($db);
 
$archive->id = isset($_GET['id']) ? $_GET['id'] : reject("4000", "No ID provided");
$archive->user_id = $user->id;

/* Must create a new archive */
if (preg_match('/^tmp_/', $archive->id)) {
	$archive->id = null;
	$archive->date_added = $archive->date_changed = date("Y-m-d H:i:s");

	if (!$archive->create()) {
		reject("4004", "Cannot create an archive");
	}

}

$archive_arr = array(
    "id" =>  $archive->id,
    "user_id" => $archive->user_id,
    "date_added" => $archive->date_added
);

resolve($archive_arr);
