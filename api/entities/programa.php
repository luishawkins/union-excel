<?php

namespace SRC\Entities;

class Programa extends \SRC\DB {
    public function get_by_name($nombre) {
        $query = $this->exec("
            SELECT
                *
            FROM programa
            WHERE nombre LIKE :nombre;
        ", [
            ":nombre" => $nombre
        ]);

        return $query ? $query[0]['id'] : false;
    }

    public function create($nombre) {
        $this->exec("
            INSERT INTO programa (
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