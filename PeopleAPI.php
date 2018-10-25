<?php

require_once "PeopleDB.php"; 

class PeopleAPI {


    public function API(){
        header('Content-Type: application/JSON');                
        $method = $_SERVER['REQUEST_METHOD']; 

        switch ($method) {
            case 'GET'://consulta
                $this->getPeoples();
                break;     
            case 'POST'://inserta
                $this->savePeople();
                break;                
            case 'PUT'://actualiza
                $this->updatePeople();
                break;
            case 'DELETE'://elimina
                $this->deletePeople();
                break;
            default://metodo NO soportado
                echo 'METODO NO SOPORTADO';
            break;
        }
    }

    function getPeoples(){             
        if($_GET['action']=='peoples'){         
            $db = new PeopleDB();
            if(isset($_GET['id'])){//muestra 1 solo registro si es que existiera ID     
                $response = $db->getPeople($_GET['id']);                
                echo json_encode($response,JSON_PRETTY_PRINT);
            }else{ //muestra todos los registros                    
             $response = $db->getPeoples();              
             echo json_encode($response,JSON_PRETTY_PRINT);
         }
     }else{
      $this->response(400);
  }       
}

function savePeople(){
    if($_GET['action']=='peoples'){
            //Decodifica un string de JSON
        $obj = json_decode( file_get_contents('php://input'));
        $objArr = (array)$obj;

            // FORMA MASIVA
            /*if (empty($objArr)){
                $this->response(422,"error","Nothing to add. Check json");                           
            }else{
                foreach ($objArr as $key => $value){
                    foreach($value as $clave2 => $valor2){
                        $people = new PeopleDB();
                        $people->insert( $valor2 );
                        $this->response(200,"success","new record added");
                    }
                }
            }S            
            //BODY
            [
                {
                    "id": "4",
                    "name": "Aquiles brinco"
                },
                {
                    "id": "5",
                    "name": "Aquiles brinco"
                }
            ]
            */

            //FORMA INDIVIDUAL
            if (empty($objArr)){
                $this->response(422,"error","Nothing to add. Check json");                           
            }else if(isset($obj->name)){
                $people = new PeopleDB();
                $people->insert( $obj->name );
                $this->response(200,"success","new record added");
            }else{
                $this->response(422,"error","The property is not defined");
            }

        }else{               
            $this->response(400);
        }
    }

    function updatePeople() {
        if( isset($_GET['action']) && isset($_GET['id']) ){
            if($_GET['action']=='peoples'){
                $obj = json_decode( file_get_contents('php://input') );   
                $objArr = (array)$obj;
                if (empty($objArr)){                        
                    $this->response(422,"error","Nothing to add. Check json");                        
                }else if(isset($obj->name)){
                    $db = new PeopleDB();
                    $db->update($_GET['id'], $obj->name);
                    $this->response(200,"success","Record updated");                             
                }else{
                    $this->response(422,"error","The property is not defined");                        
                }     
                exit;
            }
        }
        $this->response(400);
    }

    function deletePeople(){
        if( isset($_GET['action']) && isset($_GET['id']) ){
            if($_GET['action']=='peoples'){                     
                $db = new PeopleDB();
                $db->delete($_GET['id']);
                $this->response(200,"success","Record deleted");                   
                exit;
            }
        }
        echo 'das';
        $this->response(400);
    }

    function response($code=200, $status="", $message="") {
        http_response_code($code);
        if( !empty($status) && !empty($message) ){
            $response = array("status" => $status ,"message"=>$message);  
            echo json_encode($response,JSON_PRETTY_PRINT);    
        }            
    }   

}//end class
