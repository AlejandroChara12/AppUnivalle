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
  if($postjson['aksi']=="preguntar"){

    $query = mysqli_query($mysqli, "INSERT INTO preguntas_1 SET
      id_pregunta = '$postjson[id_pregunta]',
      texto_Pregunta = '$postjson[texto_Pregunta]',
      id_usuario = '$postjson[id_usuario]',
      nombre_asignatura = '$postjson[nombre_asignatura]'
    ");

    if($query) $result = json_encode(array('success'=>true));
    else $result = json_encode(array('success'=>false, 'msg'=>'Error, por favor prueba otra vez'));

    echo $result;
  }

  elseif($postjson['aksi']=='obtenerPreguntas'){
  	$data = array();
  	$query = mysqli_query($mysqli, "SELECT * FROM preguntas_1 WHERE nombre_asignatura = '$postjson[nombre_asignatura]'
      ORDER BY id_pregunta DESC");

  	while($row = mysqli_fetch_array($query)){

      $query2 = mysqli_query($mysqli, "SELECT nombre_usuario FROM usuario_1 WHERE id_usuario = '$row[id_usuario]'");

      $row2 = mysqli_fetch_array($query2);

  		$data[] = array(
        'id_pregunta' => $row['id_pregunta'],
        'texto_Pregunta' => $row['texto_Pregunta'],
        'id_usuario' => $row['id_usuario'],
        'nombre_asignatura' => $row['nombre_asignatura'],
        'nombre_usuario' => $row2['nombre_usuario'],
  		);
  	}

  	if($query) $result = json_encode(array('success'=>true, 'result'=>$data));
  	else $result = json_encode(array('success'=>false));

  	echo $result;

  }


  elseif($postjson['aksi']=='maxIdPreg'){

    $data = array();
    $query = mysqli_query($mysqli, "SELECT * FROM preguntas_1");

    $check = mysqli_num_rows($query);

    if($check > 0){

        $query2 = mysqli_query($mysqli, "SELECT MAX(id_pregunta) FROM preguntas_1");

        $row2 = mysqli_fetch_array($query2);

        $row3 = $row2['MAX(id_pregunta)'];

        $result = json_encode(array('success'=>true, 'result'=>$row3));

        echo $result;

    }else{

      $result = json_encode(array('success'=>false, 'msg'=>'Aun no hay preguntas'));
      echo $result;
    }

  }

  //****************************************
  //En adelante consultas de respuestas
  //****************************************

?>
