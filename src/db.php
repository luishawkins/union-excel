<?php

namespace SRC;

use PDO;

class DB {
    public function __construct() {
        $this->db = new PDO("mysql:host=localhost;dbname=bd_todo","root","");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function exec($query, $data = [], $debug = false) {
        if ($debug) {
            foreach ($data as $name => $value)
                $query = str_replace($name, "'$value'", $query);
            die('<pre>'.print_r($query, true).'</pre>');
        }

        $query = $this->db->prepare($query);

        foreach ($data as $name => $value)
            $query->bindValue($name, $value);

        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_files() {
        $query = $this->db->prepare("SELECT * FROM archivo;");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_archivo_by_id($id) {
        $query = $this->db->prepare("SELECT * FROM archivo WHERE id = :id;");

        $query->bindParam(":id", $id);

        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    public function get_table_by_archivo_id($archivo_id) {
        $query = $this->db->prepare("SELECT * FROM unificado WHERE archivo_id = :archivo_id;");

        $query->bindParam(":archivo_id", $archivo_id);

        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create_file($name) {
        $query = $this->db->prepare("INSERT INTO archivo( `nombre`, `fecha_creacion` ) VALUE ( :name , NOW() );");

        $query->bindParam(":name", $name);

        $query->execute();
        return $this->db->lastInsertId();
    }

    public function update_row_by_id($id, $data) {
        $query = $this->db->prepare("
            UPDATE unificado SET
                `codigo`    = :codigo,
                `nombre`    = :nombre,
                `apellido`  = :apellido,
                `dane_mpio` = :dane_mpio,
                `dane_dpto` = :dane_dpto,
                `semestre`  = :semestre,
                `estrato`   = :estrato,
                `promedio`  = :promedio,
                `cod_programa`  = :cod_programa,
                `programa`  = :programa,
                `archivo_id`    = :archivo_id
            WHERE
                id = :id
        ");

        $query->bindParam(":id",           $id);
        $query->bindParam(":codigo",       $data['codigo']);
        $query->bindParam(":nombre",       $data['nombre']);
        $query->bindParam(":apellido",     $data['apellido']);
        $query->bindParam(":dane_mpio",    $data['dane_mpio']);
        $query->bindParam(":dane_dpto",    $data['dane_dpto']);
        $query->bindParam(":semestre",     $data['semestre']);
        $query->bindParam(":estrato",      $data['estrato']);
        $query->bindParam(":promedio",     $data['promedio']);
        $query->bindParam(":cod_programa", $data['cod_programa']);
        $query->bindParam(":programa",     $data['programa']);
        $query->bindParam(":archivo_id",   $data['archivo_id']);

        $query->execute();
        return $this->db->lastInsertId();
    }

    public function insert_row($data) {
        $query = $this->db->prepare("
            INSERT INTO unificado(
                `codigo`,
                `nombre`,
                `apellido`,
                `dane_mpio`,
                `dane_dpto`,
                `semestre`,
                `estrato`,
                `promedio`,
                `cod_programa`,
                `programa`,
                `archivo_id`
            ) VALUE (
                :codigo,
                :nombre,
                :apellido,
                :dane_mpio,
                :dane_dpto,
                :semestre,
                :estrato,
                :promedio,
                :cod_programa,
                :programa,
                :archivo_id
            );
        ");

        $query->bindParam(":codigo",       $data['codigo']);
        $query->bindParam(":nombre",       $data['nombre']);
        $query->bindParam(":apellido",     $data['apellido']);
        $query->bindParam(":dane_mpio",    $data['dane_mpio']);
        $query->bindParam(":dane_dpto",    $data['dane_dpto']);
        $query->bindParam(":semestre",     $data['semestre']);
        $query->bindParam(":estrato",      $data['estrato']);
        $query->bindParam(":promedio",     $data['promedio']);
        $query->bindParam(":cod_programa", $data['cod_programa']);
        $query->bindParam(":programa",     $data['programa']);
        $query->bindParam(":archivo_id",   $data['archivo_id']);

        $query->execute();
        return $this->db->lastInsertId();
    }

    public function delete_row_by_id($id) {
        $query = $this->db->prepare("DELETE FROM unificado WHERE id = :id;");

        $query->bindParam(":id", $id);

        $query->execute();
        return $this->db->lastInsertId();
    }
}