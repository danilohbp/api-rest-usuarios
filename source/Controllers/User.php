<?php

namespace Source\Controllers;

use Source\Models\User;
use Source\Models\Validations;


require "../../vendor/autoload.php";
require "../Config.php";


switch($_SERVER['REQUEST_METHOD']){
    case "POST":
        $data = json_decode(file_get_contents("php://input"), false);
        if(!$data){
            header("HTTP/1.1 400 Bad Request!");
            echo json_encode(array("response" => "Nenhum dado encontrado"));
            exit;
        }

        $errors = array();

        if(!Validations::validationString($data->first_name)){
            array_push($errors, "Nome Inválido!");
        }
        if(!Validations::validationString($data->last_name)){
            array_push($errors, "Sobrenome Inválido");
        }
        if(!Validations::validationEmail($data->email)){
            array_push($errors, "Email Inválido");
        }


        if (count($errors) > 0){
            header("HTTP/1.1 400 Bad Request!");
            echo json_encode(array("response" => "Há campos inválidos no formulário", 
            "fields" => $errors));   
            exit;         
        }

        $user = new User();
        $user -> first_name = $data->first_name;
        $user ->last_name = $data->last_name;
        $user->email = $data->email;
        $user ->save();
        

        if($user->fail()){
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("response" => $user->fail()->getMessage()));
            exit;
        }

    break;
    case "GET":
        header("HTTP/1.1 200 OK");
        $users = new User();
        if($users->find()->Count()>0){
            $return = array();
            foreach ($users->find()->fetch(true) as $user){
                array_push($return, $user->data());
            }
            $t = json_encode(array("response" => $return));
            dd(json_decode($t));
        }
        else{
            echo json_encode(array("response" => "Não foi encontrado usuarios!!!"));
        }
    break;    
    case "PUT":
        $userid = filter_input(INPUT_GET, "id");
        
        if(!$userid){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response" => "id não informado"));
            exit;
        }
        $data = json_decode(file_get_contents("php://input"), false);
        if(!$data){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response" => "Dado não informado"));
            exit;
        }

        $errors = array();
        if(!Validations::validationInteger($userid)){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response" => "Id inválido, deve ser um valor inteiro positivo!"));
            exit;
        }
        if(!Validations::validationString($data->first_name)){
            array_push($errors, "Nome Inválido!");
        }
        if(!Validations::validationString($data->last_name)){
            array_push($errors, "Sobrenome Inválido");
        }
        if(!Validations::validationEmail($data->email)){
            array_push($errors, "Email Inválido");
        }
        
        $errors = array();

        if(Count($errors)>0){
            header("HTTP/1.1 400 Bad Request!");
            echo json_encode(array("response" => "Há campos inválidos no formulário", 
            "fields" => $errors));   
            exit;         
        }

        $user = new User();
        $user = $user->findById($userid);
        if(!$user){
            header("HTTP/1.1 200 Created");
            echo json_encode(array("response"=>"Nenhum usuário foi encontrado!"));
            exit;
        }
        
        $user -> first_name = $data->first_name;
        $user ->last_name = $data->last_name;
        $user->email = $data->email;
        $user->save();
        
        if($user->fail()){
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("response" => $user->fail()->getMessage()));
            exit;
        }
        header("HTTP/1.1 200 Created");
        echo json_encode(array("response"=>"Usuário atualizado com sucesso!"));
        
    break;
    case "DELETE":
        $userid = filter_input(INPUT_GET, "id");

        if(!$userid){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response" => "id não informado"));
            exit;
        }

        if(!Validations::validationInteger($userid)){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response" => "Id inválido, deve ser um valor inteiro positivo!"));
            exit;
        }
        
        $user = new User();
        $user = $user->findById($userid);
        if(!$user){
            header("HTTP/1.1 200 Created");
            echo json_encode(array("response"=>"Nenhum usuário foi encontrado!"));
            exit;
        }
        $user -> destroy();
        
        if($user->fail()){
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("response" => $user->fail()->getMessage()));
            exit;
        }
        header("HTTP/1.1 200 Created");
        echo json_encode(array("response"=>"Usuário removido com sucesso!"));
    break;
    default:
        header("HTTP/1.1 401 Unautorized");
        echo json_encode(array("response" => "Método não previsto"));
    break;

    
}
