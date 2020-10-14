<?php

require '../src/PHPExcel/Classes/PHPExcel.php';
include '../src/db.php';
use SRC\DB;

foreach (glob('entities/*.php') as $file)
    include($file);   


$ajax = isset($_POST['ajax']) ? $_POST['ajax'] : '';
switch ($ajax) {
    case 'get_files':
        $archivo = new \SRC\Entities\Archivo();
        die(json_encode($archivo->get_all()));
        break;

    case 'get_by_id':
        $archivo = new \SRC\Entities\Archivo();
        $data['archivo'] = $archivo->get_by_id($_POST['id']);

        $estudiante = new \SRC\Entities\Estudiante();
        $data['table'] = $estudiante->get_by_archivo_id($_POST['id']);
        $data['histograma'] = $estudiante->histograma($_POST['id']);
        die(json_encode($data));
        break;

    case 'create_file':
        $archivo = new \SRC\Entities\Archivo();
        die($archivo->create($_POST['name']));
        break;

    case 'delete_archivo':
        $archivo = new \SRC\Entities\Archivo();
        die($archivo->delete_by_id($_POST['id']));
        break;
}