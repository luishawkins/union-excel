<?php
// IMPORTAR LA RUTA
require '../src/PHPExcel/Classes/PHPExcel.php';

if (!$_FILES['excel'])
    die('Error');

include ('../src/db.php');

foreach (glob('../api/entities/*.php') as $file)
    include($file);   

$estudiante = new \SRC\Entities\Estudiante();
$programa = new \SRC\Entities\Programa();
$departamento = new \SRC\Entities\Departamento();
$municipio = new \SRC\Entities\Municipio();

$archivos = $_FILES['excel']['tmp_name'];
//cargar nuestra hoja de excel
$excel = PHPExcel_IOFactory::load($archivos);
// cargar la hoja de calculo que se quiere
$excel->setActiveSheetIndex(0);
// obtener el numero de filas del archivo
$numerofila = $excel->setActiveSheetIndex(0)->getHighestRow();

$data = [];

for ($i = 2; $i <= $numerofila; $i++) {
    $codigo = $excel->setActiveSheetIndex(0)->getcell('A' . $i)->getCalculatedValue();
    if (!$codigo)
        continue;

    $nombre = $excel->setActiveSheetIndex(0)->getcell('B' . $i)->getCalculatedValue();
    $apellido = $excel->setActiveSheetIndex(0)->getcell('C' . $i)->getCalculatedValue();
    $dane_mpio = $excel->setActiveSheetIndex(0)->getcell('D' . $i)->getCalculatedValue();
    $dane_dpto = $excel->setActiveSheetIndex(0)->getcell('E' . $i)->getCalculatedValue();
    $semestre = $excel->setActiveSheetIndex(0)->getcell('F' . $i)->getCalculatedValue();
    $estrato = $excel->setActiveSheetIndex(0)->getcell('G' . $i)->getCalculatedValue();
    $promedio = $excel->setActiveSheetIndex(0)->getcell('H' . $i)->getCalculatedValue();
    $cod_programa = $excel->setActiveSheetIndex(0)->getcell('I' . $i)->getCalculatedValue();
    $programa_e = $excel->setActiveSheetIndex(0)->getcell('J' . $i)->getCalculatedValue();

    $data[$i]['codigo'] = $codigo;
    $data[$i]['nombre'] = $nombre;
    $data[$i]['apellido'] = $apellido;
    $data[$i]['semestre'] = $semestre;
    $data[$i]['estrato'] = $estrato;
    $data[$i]['promedio'] = $promedio;
    $data[$i]['cod_programa'] = $cod_programa;
    $data[$i]['programa'] = $programa_e;

    if (!$departamento_id = $departamento->get_by_name($dane_dpto))
        $departamento_id = $departamento->create($dane_dpto);

    if (!$municipio_id = $municipio->get_by_name($dane_mpio))
        $municipio_id = $municipio->create($dane_mpio, $departamento_id);

    if (!$programa_id = $programa->get_by_name($programa_e))
        $programa_id = $programa->create($programa_e);

    $data[$i]['id'] = $estudiante->create([
        "archivo_id" => $_GET['archivo_id'],
        "codigo" => $codigo,
        "nombre" => $nombre,
        "apellido" => $apellido,
        "semestre" => $semestre,
        "estrato" => $estrato,
        "promedio" => $promedio,
        "dane_mpio" => $dane_mpio,
        "dane_dpto" => $dane_dpto,
        "programa_id" => $programa_id,
        "programa" => $programa_e,
        "municipio_id" => $municipio_id
    ]);
}

ob_clean();
die(json_encode($data));