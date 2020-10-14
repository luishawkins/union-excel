<?php

namespace SRC\Entities;

class Archivo extends \SRC\DB {
    public function get_all() {
        return $this->exec("SELECT * FROM archivo;");
    }

    public function get_by_id($id) {
        return $this->exec("
            SELECT
                *
            FROM archivo
            WHERE id LIKE :id;
        ", [
            ":id" => $id
        ]);
    }

    public function get_by_name($name) {
        return $this->exec("
            SELECT
                *
            FROM archivo
            WHERE nombre LIKE :name;
        ", [
            ":name" => $name
        ]);
    }

    public function create($name) {
        $this->exec("
            INSERT INTO archivo (
                `nombre`,
                `fecha_creacion`
            ) VALUE (
                :name,
                NOW()
            );
        ", [
            ":name" => $name
        ]);

        return $this->db->lastInsertId();
    }
    
    public function delete_by_id($id) {
        return $this->exec("
            DELETE FROM archivo WHERE id = :id;
        ", [
            ":id" => $id,
        ]);
    }
}