<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/user.php';
include_once '../objects/session.php';
 
$database = new Database();
$db = $database->getConnection();
 
$user = new User($db);
$user->id = isset($_GET['user_id']) ? $_GET['user_id'] : reject("3000", "No User ID provided");
$user->account_id = isset($_GET['account_id']) ? $_GET['account_id'] : reject("3001", "No Account ID provided");
  
if (!$user->get()) {
	reject("3002", "No user is matching this information");
}

$session = new Session($db);
$session->id = isset($_GET['id']) ? base64_decode($_GET['id']) : reject("3003", "No ID provided");

/* Must create a new session */
if (preg_match('/^offline_/', $session->id)) {
	$session->id = null;
	$session->user_id = $user_id;

	$session->date_read = isset($_GET['date_read']) ? $_GET['date_read'] : reject("3007", "No Date Read provided");
	$session->date_added = $session->date_changed = date("Y-m-d H:i:s");
	$session->create();
}

if (!$user->get()) {
	reject("3004", "Cannot match this session with this account and this user");
}

$session->bookName = isset($_GET['book_name']) ? $_GET['book_name'] : reject("3005", "No Book Name provided");
$session->bookAuthor = isset($_GET['book_author']) ? $_GET['book_author'] : reject("3006", "No Book Author provided");
$session->isCompleted = isset($_GET['is_completed']) ? ((int)$_GET['is_completed'] == 1 ? true : false) : reject("3008", "No Is Completed provided");
$session->pageBookmark = isset($_GET['page_bookmark']) ? (int)$_GET['page_bookmark'] : reject("3009", "No Page Bookmark provided");
$session->update();

$session_arr = array(
    "id" =>  $session->id,
    "user_id" => $session->user_id,
    "date_added" => $session->date_added,
    "date_changed" => $session->date_changed,
    "date_deleted" => $session->date_deleted,
    "bookAuthor" => $session->bookAuthor,
    "bookName" => $session->bookName,
    "isCompleted" => $session->isCompleted,
    "pageBookmark" => $session->pageBookmark
);
 
resolve($session_arr);