<?php
require_once ('./MysqliDb.php');

$db = new MysqliDb ('mysql', 'root', 'root', 'test');
$db->autoReconnect = false;

$request_method=$_SERVER["REQUEST_METHOD"];

switch($request_method)
{
    case 'GET':
        // Retrive Products
        if(!empty($_GET["id"]))
        {
            $id=intval($_GET["id"]);
            get_test($id);
        }
        else
        {
            get_tests();
        }
        break;
    case 'PUT':
        if(!empty($_GET["question_id"])){
            $question_id = intval($_GET["question_id"]);
            $value = $_GET["value"];
            add_answer($question_id, $value);
        }
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function get_tests()
{
    global $db;
    $tests = $db->get('tests');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($tests);
}

function get_test($id)
{
    global $db;
    $db->where("test_id", $id);
    $questions = $db->get("questions", null);

    if ($db->count > 0){
        for($i=0; $i<count($questions); $i++){
            $db->where("question_id",  $questions[$i]["id"]);
            $answers = $db->get ("answers", null);
            $questions[$i]["answers"] = $answers;
        }
    }

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($questions);

}

function add_answer($question_id, $value){
    global $db;

    if($value == 1){
        $data = Array (
            'timesOk' => $db->inc(1),
        );
    }else{
        $data = Array (
            'timesBad' => $db->inc(1),
        );
    }

    $db->where ('id', $question_id);
    $db->update ('questions', $data);


    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode(array($db->count));
}