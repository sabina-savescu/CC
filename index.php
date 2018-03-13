<?php

// url fain
// interfata html  
// cod de eroare
// date gata de introdus
//ce inseamna niste chestii

include("Cats.php");

function customError($errno, $errstr) {
    echo "Error: $errstr";
    die();
  }

set_error_handler("customError",E_USER_WARNING);
  
 
$verb = $_SERVER['REQUEST_METHOD'];
$url_pieces = explode('/', $_SERVER['PATH_INFO']);


if ($url_pieces[1] != 'cats') {
    http_response_code(404);
    trigger_error("Page not found",E_USER_WARNING);  
}



$cats = new Cats ();
$response;
switch ($verb) {
    case 'GET':
        $field;
        $value;
        if (!empty($url_pieces[2])) {
            $field = $url_pieces[2];
            $value = $url_pieces[3];
            $response = $cats->search($field, $value);
        } else {
            $response = $cats->get_all();
        }
        $response=json_encode($response);
        break;

    case 'POST':
        $params = json_decode(file_get_contents("php://input"), true);
        if (!$params) {
            trigger_error("Invalid json",E_USER_WARNING); 
        }
       
        $cat = new Cat();
        foreach ($params as $key => $value)
        
        if (!array_key_exists($key, $cat)){
            http_response_code(400);
            trigger_error("Invalid JSON",E_USER_WARNING);
        }
        
        $cat->set($params);
        
            
        $cats->add($cat);
        $response = '{"status":"OK"}';
        http_response_code(201);
        break;

    case 'PUT':
        $params = json_decode(file_get_contents("php://input"), true);
        if (!$params) {
            throw new Exception("response missing or invalid");
        }
        $id;
        if (!empty($url_pieces[2])) {
            $id = $url_pieces[2];
        }
        $cat = new Cat();
        $cat->id = $id;
        $cat->set($params);
        $cats->update($cat);
        $response = '{"status":"OK"}';
        http_response_code(200);
        break;

    case 'DELETE':
        $id;
        if (!empty($url_pieces[2])) {
            $id = $url_pieces[2];
            $cats->delete($id);
        }
        $response = '{"status":"OK"}';
        http_response_code(200);
        break;

    default:
        throw new Exception('Method Not Supported', 405);
}
header("Content-Type: application/json");
echo $response;
?>

