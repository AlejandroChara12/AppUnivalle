<?php

  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Credentials: true");
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
  header("Content-Type: application/json; charset=utf-8");

  include "library/config.php";

  $postjson = json_decode(file_get_contents('php://input'), true);

  //**************
  //Consulta para traer las asignaturas
  //**************

  if($postjson['aksi']=='getAsig'){
  	$data = array();
  	$query = mysqli_query($mysqli, "SELECT * FROM asignatura_1 WHERE semestre_asignatura = '$postjson[semestre]'
      AND facultad_asignatura = '$postjson[facultad]'
      ORDER BY id_asignatura DESC");

  	while($row = mysqli_fetch_array($query)){

  		$data[] = array(
        'id_asignatura' => $row['id_asignatura'],
        'nombre_asignatura' => $row['nombre_asignatura'],
        'informacion_asignatura' => $row['informacion_asignatura'],
        'creditos_asignatura' => $row['creditos_asignatura'],
        'semestre_asignatura' => $row['semestre_asignatura'],
        'prerequisitos_asignatura' => $row['prerequisitos_asignatura'],
        'facultad_asignatura' => $row['facultad_asignatura'],

  		);
  	}

  	if($query) $result = json_encode(array('success'=>true, 'result'=>$data));
  	else $result = json_encode(array('success'=>false));

  	echo $result;

  }

?>
