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
header("Content-Type: application/json");
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
        $response=json_encode($response);
        echo $response;
        
        break;

    case 'POST':
    $response=Array();
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
        array_push($response,$resource);
        

    }
        $response=json_encode($response);
        echo $response;
        http_response_code(201); //Created
        return;

    case 'PUT':
        $body = json_decode(file_get_contents("php://input"), true);
        if (!$body) {
            http_response_code(400); //Bad Request
            return;
            //trigger_error("Invalid json",E_USER_WARNING); 
        }
        if (!empty($url_pieces[3])) {
            $field = $url_pieces[2];
            $value = $url_pieces[3];
            $response = $cats->search($field, $value);
            if(empty($response)){
                http_response_code(404); //No Content
                return;
                
            }
            
        
        foreach($body as $resource){
            $cat = new Cat();
            foreach ($resource as $key => $valu)
                if (!array_key_exists($key, $cat)){
                    http_response_code(400); //Bad Request
                    return;
            //trigger_error("Invalid JSON",E_USER_WARNING);
                }
            
            if ($field=='id'){
                $cat->id = $value; 
            }else if ($field=='breed')
                $cat->breed = $value;
            
            $cat->set($resource);
            $cats->update($cat,$field,$value);
        }
        }
 
        else if (empty($url_pieces[3])&&!empty($url_pieces[2])){ 
            http_response_code(404); //Not Found
            return;
            //$response = '{"status":"Not Found"}';
        }
        else 
        
        http_response_code(200); //status OK
        return;

    case 'DELETE':
        if (!empty($url_pieces[3])) {
            $field = $url_pieces[2];
            $value = $url_pieces[3];
            $response = $cats->search($field, $value);
            if(empty($response)){
                http_response_code(404); //No Content
                return;
                
            }
            $cats->delete($field,$value);
            
            
        } 
        else if (empty($url_pieces[3])&&!empty($url_pieces[2])){ 
            http_response_code(404); //Not Found
            return;
            //$response = '{"status":"Not Found"}';
        }
    
        else $cats->delete_all();
        
        http_response_code(204); //No Content
        break;

    default:
        http_response_code(405); //Method Not Allowed
}


?>

