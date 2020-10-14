<?php

namespace SRC\Entities;

class Departamento extends \SRC\DB {
    public function get_by_name($nombre) {
        $query = $this->exec("
            SELECT
                *
            FROM departamento
            WHERE nombre LIKE :nombre;
        ", [
            ":nombre" => $nombre
        ]);

        return $query ? $query[0]['id'] : false;
    }

    public function create($nombre) {
        $this->exec("
            INSERT INTO departamento (
                `nombre`
            ) VALUE (
                :nombre
            );
        ", [
            ":nombre" => $nombre
        ]);

        return $this->db->lastInsertId();
    }
}