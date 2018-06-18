<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/account.php';
include_once '../objects/book.php';
 
$database = new Database();
$db = $database->getConnection();
 
$account = new Account($db);
$account->id = isset($_GET['account_id']) ? $_GET['account_id'] : reject("5000", "No Account ID provided");
  
if (!$account->get()) {
	reject("5001", "No Account is matching this information");
}

$book = new Book($db);
$book->id = isset($_GET['id']) ? $_GET['id'] : reject("5003", "No ID provided");

/* Must create a new session */
if (preg_match('/^tmp_/', $book->id)) {
	$book->id = null;
	$book->account_id = $account->id;

    $book->date_added = $book->date_changed = date("Y-m-d H:i:s");

    if (!$book->create()) {
        reject("5002", "Cannot create the book");
    }
}

if (!$book->get()) {
	reject("5003", "Cannot match this book with this account");
}

$book->name = isset($_GET['name']) ? urldecode($_GET['name']) : reject("5004", "No Book Name provided");
$book->author = isset($_GET['author']) ? urldecode($_GET['author']) : reject("5005", "No Book Author provided");

if (isset($_GET['status'])) {
    switch($_GET['status']) {
        case 'deleted':
            $book->date_deleted = $user->date_changed = date("Y-m-d H:i:s");
            break;
    }
}

if (!$book->update()) {
    reject("5006", "Cannot update this book");
}

$book_arr = array(
    "id" =>  $book->id,
    "account_id" => $book->account_id,
    "date_added" => $book->date_added,
    "date_changed" => $book->date_changed,
    "date_deleted" => $book->date_deleted,
    "name" => $book->name,
    "author" => $book->author
);
 
resolve($book_arr);