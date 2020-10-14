<?php

namespace SRC\Entities;

class Municipio extends \SRC\DB {
    public function get_by_name($nombre) {
        $query = $this->exec("
            SELECT
                *
            FROM municipio
            WHERE nombre LIKE :nombre
        ", [
            ":nombre" => $nombre,
        ]);

        return $query ? $query[0]['id'] : false;
    }

    public function create($nombre, $departamento_id) {
        $this->exec("
            INSERT INTO municipio (
                `nombre`,
                `departamento_id`
            ) VALUE (
                :nombre,
                :departamento_id
            );
        ", [
            ":nombre" => $nombre,
            ":departamento_id" => $$departamento_id
        ]);

        return $this->db->lastInsertId();
    }
}