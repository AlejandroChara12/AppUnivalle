<?php

  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Credentials: true");
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
  header("Content-Type: application/json; charset=utf-8");

  include "library/config.php";

  $postjson = json_decode(file_get_contents('php://input'), true);

  //Codigo funcionando
  //**************
  // NO MODIFICAR
  //**************

  //************************************************
  //Funcion de consulta para registrar un nuevo usuario
  //************************************************
  if($postjson['aksi']=="register"){

    $query = mysqli_query($mysqli, "SELECT * FROM usuario_1 WHERE correo_usuario = '$postjson[correo_usuario]'");
    $check = mysqli_num_rows($query);
    if($check > 0){

        $result = json_encode(array('success'=>false, 'msg'=>'El correo ya esta en uso'));
        echo $result;

    }else{

      //Para encryptar la contraseña en la BD
      $password = $postjson['contrasena_usuario'];

      $pass_encrypt = password_hash($password, PASSWORD_DEFAULT);

      $query = mysqli_query($mysqli, "INSERT INTO usuario_1 SET
        nombre_usuario = '$postjson[nombre_usuario]',
        correo_usuario = '$postjson[correo_usuario]',
        contrasena_usuario = '$postjson[contrasena_usuario]',
        estado_cuenta   = 'Y'
      ");

      if($query) $result = json_encode(array('success'=>true));
      else $result = json_encode(array('success'=>false, 'msg'=>'Error, por favor prueba otra vez'));

      echo $result;

    }
  }

  //************************************************
  //Funcion de consulta para Inicio de sesion
  //************************************************
  elseif($postjson['aksi']=="login"){

    $query = mysqli_query($mysqli, "SELECT * FROM usuario_1 WHERE correo_usuario = '$postjson[correo_usuario]'");
    $check = mysqli_num_rows($query);

    if($check > 0){
      //if(password_verify($postjson['contrasena_usuario'], $data['contrasena_usuario'])){
      while($row = mysqli_fetch_array($query))
      {
          $correct = password_verify($postjson['contrasena_usuario'], $row['contrasena_usuario']);

          if(($postjson['contrasena_usuario'] == $row['contrasena_usuario']))
          {
              //if($row['estado_cuenta'] == 'Y'){
                $result = json_encode(array('success'=>true, 'result'=>$row));
              //}else{
              //  $result = json_encode(array('success'=>false, 'msg'=>'Cuenta Inactiva'));
              //}
          }
          else
          {
            $result = json_encode(array('success'=>false, 'msg'=>'Los datos de usuario estan incorrectos'));
          }
      }
    }else{
      $result = json_encode(array('success'=>false, 'msg'=>'Cuenta no registrada'));
    }

    echo $result;
  }

  //************************************************
  //Funcion de consulta para actualizar datos
  //************************************************
  elseif($postjson['aksi']=='actualizar'){

    $query = mysqli_query($mysqli, "SELECT * FROM usuario_1 WHERE id_usuario = '$postjson[idusuario]'");

    $check = mysqli_num_rows($query);

    if($check > 0){
      while($row = mysqli_fetch_array($query))
      {
          $correct = password_verify($postjson['contrasena_usuario'], $row["contrasena_usuario"]);

          if(!($correct == true))
          {
              $query2 = mysqli_query($mysqli, "UPDATE usuario_1 SET
              nombre_usuario = '$postjson[nombre_usuario]'");
              $query3 = mysqli_query($mysqli, "SELECT * FROM usuario_1 WHERE id_usuario = '$postjson[idusuario]'");
              $row2 = mysqli_fetch_array($query3);
              $result = json_encode(array('success'=>true, 'msg'=>'Datos actualizados', 'result'=>$row2));
          }
          else
          {
            $result = json_encode(array('success'=>false, 'msg'=>'la contraseña es incorrecta'));
          }
      }
    }else{
      $result = json_encode(array('success'=>false, 'msg'=>'Ha ocurrido un error'));
    }
  	echo $result;

  }


  //**********************************************
  //Proceso de consulta para recuperar la contraseña de usuario
  //**********************************************
  elseif($postjson['aksi']=='recuperarContraseña'){

    $query = mysqli_query($mysqli, "SELECT * FROM usuario_1 WHERE correo_usuario = '$postjson[correo_usuario]'");
    $check = mysqli_num_rows($query);

    if($check > 0){
      //if(password_verify($postjson['contrasena_usuario'], $data['contrasena_usuario'])){
      while($row = mysqli_fetch_array($query))
      {
          $idRecuperar = $row['id_usuario'] + '61000';
          $codigoGenerado = mt_rand(1000,9999) + $row['id_usuario'];
          //$codigoGenerado = $codigoGenerado + $row['id_usuario'];

          $query2 = mysqli_query($mysqli, "SELECT * FROM recuperar_contrasena WHERE id_usuario = '$row[id_usuario]'");
          $check2 = mysqli_num_rows($query2);

          if($check2 > 0){

            while($row2 = mysqli_fetch_array($query2))
            {

                $query3 = mysqli_query($mysqli, "UPDATE recuperar_contrasena SET
                codigo_generado = '$codigoGenerado'
                WHERE id_usuario = '$row[id_usuario]'
                ");
                $result = json_encode(array('success'=>true, 'msg'=>'Correo enviado', 'result'=>$codigoGenerado));

            }
          }
          else{

              $query3 = mysqli_query($mysqli, "INSERT INTO recuperar_contrasena SET
              id_recuperar = '$idRecuperar',
              id_usuario = '$row[id_usuario]',
              codigo_generado = '$codigoGenerado'
              ");
              $result = json_encode(array('success'=>true, 'msg'=>'Correo enviado', 'result'=>$codigoGenerado));
          }
      }

    }else{
      $result = json_encode(array('success'=>false, 'msg'=>'Correo no registrado'));
    }

    echo $result;
  }

  //************************************************
  //Proceso para cambiar la contraseña
  //************************************************
  elseif($postjson['aksi']=='cambiarContrasena'){
    $query = mysqli_query($mysqli, "SELECT * FROM usuario_1 WHERE correo_usuario = '$postjson[correoRecuperar]'");
    $check = mysqli_num_rows($query);

    if($check > 0){

      while($row = mysqli_fetch_array($query))
      {
        $query2 = mysqli_query($mysqli, "SELECT * FROM recuperar_contrasena WHERE id_usuario = '$row[id_usuario]'");
        $check2 = mysqli_num_rows($query2);

        if($check2 > 0){
          while($row2 = mysqli_fetch_array($query2))
          {
            if($postjson['codigoGenerado'] == $row2['codigo_generado']) {

              $query3 = mysqli_query($mysqli, "UPDATE usuario_1 SET
              contrasena_usuario = '$postjson[contrasenaRecuperar]'
              WHERE correo_usuario = '$postjson[correoRecuperar]'");

              $query3 = mysqli_query($mysqli, "UPDATE recuperar_contrasena SET
              codigo_generado = '0'
              WHERE id_usuario = '$row[id_usuario]'");

              $result = json_encode(array('success'=>true, 'msg'=>'Contraseña actualizada'));
            }
            else{
              $result = json_encode(array('success'=>false, 'msg'=>'El codigo de recuperacion no es valido'));
            }
          }
        }
        else{
            $result = json_encode(array('success'=>true, 'msg'=>'El usuario es invalido'));
        }
      }
    }
    else{
      $result = json_encode(array('success'=>false, 'msg'=>'Correo no registrado'));
    }

    echo $result;
  }

  //Obtenar los datos de todos los usuarios
  elseif($postjson['aksi']=='getUser'){
  	$data = array();
  	$query = mysqli_query($mysqli, "SELECT * FROM usuario_1 ORDER BY id_usuario DESC LIMIT $postjson[start],$postjson[limit]");

  	while($row = mysqli_fetch_array($query)){

  		$data[] = array(
  			'idusuario' => $row['id_usuario'],
  			'nombre_usuario' => $row['nombre_usuario'],
  			'correo_usuario' => $row['correo_usuario'],

  		);
  	}

  	if($query) $result = json_encode(array('success'=>true, 'result'=>$data));
  	else $result = json_encode(array('success'=>false));

  	echo $result;

  }

?>
