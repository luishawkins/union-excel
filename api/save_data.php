<?php

// if (!$_POST['codigo'])
//     header("location:javascript://history.go(-1)");

use SRC\DB;
require ('../src/db.php');

foreach (glob('../api/entities/*.php') as $file)
    include($file);   

$estudiante = new \SRC\Entities\Estudiante();
$departamento = new \SRC\Entities\Departamento();
$municipio = new \SRC\Entities\Municipio();

if ($_POST['deletes']) {
    foreach (explode(',', $_POST['deletes']) as $row_id)
        $estudiante->delete_by_id($row_id);
}

for ($i=0; $i<count($_POST['codigo']); $i++) {
    if (!$departamento_id = $departamento->get_by_name($_POST['dane_dpto'][$i]))
        $departamento_id = $departamento->create($_POST['dane_dpto'][$i]);
    
    if (!$municipio_id = $municipio->get_by_name($_POST['dane_mpio'][$i]))
        $municipio_id = $municipio->create($_POST['dane_mpio'][$i], $departamento_id);

    if ($_POST['id'][$i]) $estudiante->update_by_id($_POST['id'][$i], [
        "archivo_id" => $_POST['archivo'],
        "codigo" => $_POST['codigo'][$i],
        "nombre" => $_POST['nombre'][$i],
        "apellido" => $_POST['apellido'][$i],
        "dane_mpio" => $_POST['dane_mpio'][$i],
        "dane_dpto" => $_POST['dane_dpto'][$i],
        "semestre" => $_POST['semestre'][$i],
        "estrato" => $_POST['estrato'][$i],
        "promedio" => $_POST['promedio'][$i],
        "programa_id" => $_POST['cod_programa'][$i],
        "municipio_id" => $municipio_id,
        "programa" => $_POST['programa'][$i],
    ]);
    else $estudiante->create([
        "archivo_id" => $_POST['archivo'],
        "codigo" => $_POST['codigo'][$i],
        "nombre" => $_POST['nombre'][$i],
        "apellido" => $_POST['apellido'][$i],
        "dane_mpio" => $_POST['dane_mpio'][$i],
        "dane_dpto" => $_POST['dane_dpto'][$i],
        "semestre" => $_POST['semestre'][$i],
        "estrato" => $_POST['estrato'][$i],
        "promedio" => $_POST['promedio'][$i],
        "programa_id" => $_POST['cod_programa'][$i],
        "municipio_id" => $municipio_id,
        "programa" => $_POST['programa'][$i],
    ]);
}

header("location:javascript://history.go(-1)");