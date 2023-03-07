<?php
   
require_once ("_db.php");
require ("../twilio-php-main/src/Twilio/autoload.php");


if (isset($_POST['accion'])){ 
    switch ($_POST['accion']){
        //casos de registros
        
        case 'editar_registro':
            editar_registro();
            break; 

        case 'eliminar_registro';
            eliminar_registro();
            break;

        case 'acceso_user';
            acceso_user();
            break;

        case 'autenticar_cel';
            autenticar_tel();
            break;
        

		}

	}

    function editar_registro() {
        $conexion = mysqli_connect('localhost','root','admin','r_user');
		extract($_POST);
		$consulta="UPDATE user SET nombre = '$nombre', correo = '$correo', telefono = '$telefono',
		password ='$password', rol = '$rol' WHERE id = '$id' ";

		mysqli_query($conexion, $consulta);


		header('Location: ../views/user.php');

}

function eliminar_registro() {
    $conexion = mysqli_connect('localhost','root','admin','r_user');
    extract($_POST);
    $id= $_POST['id'];
    $consulta= "DELETE FROM user WHERE id= $id";

    mysqli_query($conexion, $consulta);


    header('Location: ../views/user.php');

}

function acceso_user() {
    $nombre=$_POST['nombre'];
    $password=$_POST['password'];
    // Generar un código aleatorio
    $code = rand(100000, 999999);
	$conexion = mysqli_connect('localhost','root','admin','r_user');
    $consulta = "SELECT * FROM user WHERE nombre='$nombre' AND password='$password'";
    $resultado=mysqli_query($conexion, $consulta);
    $filas=mysqli_fetch_array($resultado);
    $number_user = $filas['telefono'];
    // Configurar las credenciales de Twilio
    $sid    = "AC2386189fca8a87a163417e634c473934";
    $token  = "4ba1cf2d69f9af1ba600e38e9504d615";
    $from   = "+1 567 430 2455";
    $to     = "+57$number_user";
    
    $client = new Twilio\Rest\Client($sid, $token);

    // Enviar el mensaje de texto
    $message = $client->messages->create(
        $to,
        array(
            'from' => $from,
            'body' => 'Tu código de acceso es: ' . $code
        )
    );


    if($filas['password'] == $password){
        session_start();
        $_SESSION['nombre' ]= $nombre;
        $_SESSION['number']= $number_user;
        $_SESSION['rol']= $filas['rol'];
        $_SESSION['verification_number'] = $code;
        header('Location: ../views/autentication.php');
    } 
    else{
        header('Location: login.php');
        session_destroy();
    }
    

}

function autenticar_tel(){
    session_start(); 
    $verification_number=$_POST['verification_number'];
    
    if ($verification_number ==  $_SESSION['verification_number']) {
        
        if($_SESSION['rol'] == 1){ //admin

            header('Location: ../views/user.php');
    
        }else if($_SESSION['rol'] == 2){//lector
            header('Location: ../views/lector.php');
        }
    } else {
       
        session_destroy();
    }

}






