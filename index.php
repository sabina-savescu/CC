<?php


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
        
        if (!empty($url_pieces[3])) {
            $field = $url_pieces[2];
            $value = $url_pieces[3];
            $response = $cats->search($field, $value);
            
        } 
        else if (empty($url_pieces[3])&&!empty($url_pieces[2])){ 
            http_response_code(404); //Not Found
            return;
            //$response = '{"status":"Not Found"}';
        }
        else {
            $response = $cats->get_all();
        }
        if(empty($response)){
            http_response_code(204); //No Content
            return;
            
        }
        header("Content-Type: application/json");
        $response=json_encode($response);
        echo $response;
        
        break;

    case 'POST':
        $body = json_decode(file_get_contents("php://input"), true);
        if (!$body) {
            http_response_code(400); //Bad Request
            return;
            //trigger_error("Invalid json",E_USER_WARNING); 
        }
       
       foreach($body as $resource){
        $cat = new Cat();
        foreach ($resource as $key => $value)     
            if (!array_key_exists($key, $cat)){
                http_response_code(400); //Bad Request
                return;
                //trigger_error("Invalid JSON",E_USER_WARNING);
            }
        
        $cat->set($resource);  
        $cats->add($cat);
    }
        http_response_code(201); //Created
        return;

    case 'PUT':
        $body = json_decode(file_get_contents("php://input"), true);
        if (!$body) {
            http_response_code(400); //Bad Request
            return;
            //trigger_error("Invalid json",E_USER_WARNING); 
        }
        if (!empty($url_pieces[2])) {
            $id = $url_pieces[2];
        
        foreach($body as $resource){
            $cat = new Cat();
            foreach ($resource as $key => $value)
                if (!array_key_exists($key, $cat)){
                    http_response_code(400); //Bad Request
                    return;
            //trigger_error("Invalid JSON",E_USER_WARNING);
            }
        $cat->id = $id;
        $cat->set($resource);
        $cats->update($cat);
        }
    }
        else 
        
        http_response_code(200); //status OK
        return;

    case 'DELETE':
        $id;
        if (!empty($url_pieces[2])) {
            $id = $url_pieces[2];
            $cats->delete($id);
        }
        else $cats->delete_all();
        
        http_response_code(200); //status OK
        break;

    default:
        http_response_code(405); //Method Not Allowed
}


?>

