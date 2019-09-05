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

  //Funcion de consulta para registrar un nuevo usuario
  if($postjson['aksi']=="responder"){

    $query = mysqli_query($mysqli, "INSERT INTO respuestas_1 SET
      id_respuesta = '$postjson[id_respuesta]',
      texto_respuesta = '$postjson[texto_respuesta]',
      id_pregunta = '$postjson[id_pregunta]',
      id_usuario = '$postjson[id_usuario]',
      nombre_asignatura = '$postjson[nombre_asignatura]',
      num_gracias = '$postjson[num_gracias]'
    ");

    if($query) $result = json_encode(array('success'=>true));
    else $result = json_encode(array('success'=>false, 'msg'=>'Error, por favor prueba otra vez'));

    echo $result;
  }

  elseif($postjson['aksi']=='obtenerRespuestas'){
  	$data = array();
  	$query = mysqli_query($mysqli, "SELECT * FROM respuestas_1
      WHERE nombre_asignatura = '$postjson[nombre_asignatura]'
      AND id_pregunta = '$postjson[id_pregunta]'
      ORDER BY id_respuesta DESC");

  	while($row = mysqli_fetch_array($query)){

      $query2 = mysqli_query($mysqli, "SELECT nombre_usuario FROM usuario_1 WHERE id_usuario = '$row[id_usuario]'");

      $row2 = mysqli_fetch_array($query2);

  		$data[] = array(
        'id_respuesta' => $row['id_respuesta'],
        'texto_respuesta' => $row['texto_respuesta'],
        'id_pregunta' => $row['id_pregunta'],
        'id_usuario' => $row['id_usuario'],
        'nombre_asignatura' => $row['nombre_asignatura'],
        'num_gracias' => $row['num_gracias'],
        'nombre_usuario' => $row2['nombre_usuario'],

  		);
  	}

  	if($query) $result = json_encode(array('success'=>true, 'result'=>$data));
  	else $result = json_encode(array('success'=>false));

  	echo $result;

  }

  elseif($postjson['aksi']=='maxIdResp'){

    $data = array();
    $query = mysqli_query($mysqli, "SELECT * FROM respuestas_1");

    $check = mysqli_num_rows($query);

    if($check > 0){

        $query2 = mysqli_query($mysqli, "SELECT MAX(id_respuesta) FROM respuestas_1");

        $row2 = mysqli_fetch_array($query2);

        $row3 = $row2['MAX(id_respuesta)'];

        $result = json_encode(array('success'=>true, 'result'=>$row3));

        echo $result;

    }else{

      $result = json_encode(array('success'=>false, 'msg'=>'Aun no hay respuestas'));
      echo $result;
    }

  }

  elseif($postjson['aksi']=='darGracias'){

    $data = array();
    $query = mysqli_query($mysqli, "SELECT * FROM respuestas_1 WHERE id_respuesta = '$postjson[id_respuesta]'");

    $check = mysqli_num_rows($query);

    if($check > 0){

      while($row = mysqli_fetch_array($query)){
        $rowG = $row['num_gracias'];
      }
      $rowAG = $rowG + 1;

      $query = mysqli_query($mysqli, "UPDATE respuestas_1 SET
      num_gracias = '$rowAG'
      WHERE id_respuesta = '$postjson[id_respuesta]'");

      $result = json_encode(array('success'=>true, 'msg'=>'Agradecer exitoso'));

      echo $result;

    }else{

      $result = json_encode(array('success'=>false, 'msg'=>'No se pudo agradecer'));
      echo $result;
    }
  }

?>
