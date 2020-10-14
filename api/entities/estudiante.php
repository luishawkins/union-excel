<?php

namespace SRC\Entities;

class Estudiante extends \SRC\DB {
    public function get_by_id($id) {
        $query = $this->exec("
            SELECT
                *
            FROM estudiante
            WHERE id LIKE :id;
        ", [
            ":id" => $id
        ]);

        return $query ? $query[0] : [];
    }

    public function get_by_archivo_id($archivo_id) {
        $query = $this->exec("
            SELECT
                e.*,
                m.nombre as dane_mpio,
                d.nombre as dane_dpto,
                p.id as cod_programa,
                p.nombre as programa,
                e.id
            FROM estudiante as e
            INNER JOIN programa as p
                ON e.programa_id = p.id
            INNER JOIN municipio as m
                ON e.municipio_id = m.id
            INNER JOIN departamento as d
                ON m.departamento_id = d.id
            WHERE archivo_id = :archivo_id
            GROUP BY e.id;
        ", [
            ":archivo_id" => $archivo_id
        ]);

        return $query ? $query : [];
    }

    public function histograma($archivo_id) {
        $query = $this->exec("
            SELECT
                COUNT(*) AS count,
                semestre
            FROM estudiante
            WHERE archivo_id = :archivo_id
            GROUP BY semestre
            ORDER BY semestre;
        ", [
            ":archivo_id" => $archivo_id
        ]);

        return $query ? $query : [];
    }

    public function update_by_id($id, $data) {
        return $this->exec("
            UPDATE estudiante SET
                `codigo`    = :codigo,
                `nombre`    = :nombre,
                `apellido`  = :apellido,
                `semestre`  = :semestre,
                `estrato`   = :estrato,
                `promedio`  = :promedio,
                `programa_id`   = :programa_id,
                `municipio_id`  = :municipio_id
            WHERE
                id = :id;
        ", [
            ":id" => $id,
            ":codigo" => $data['codigo'],
            ":nombre" => $data['nombre'],
            ":apellido" => $data['apellido'],
            ":semestre" => $data['semestre'],
            ":estrato" => $data['estrato'],
            ":promedio" => $data['promedio'],
            ":programa_id" => $data['programa_id'],
            ":municipio_id" => $data['municipio_id'],
        ]);
    }

    public function create($data) {
        $this->exec("
            INSERT INTO estudiante (
                `archivo_id`,
                `codigo`,
                `nombre`,
                `apellido`,
                `semestre`,
                `estrato`,
                `promedio`,
                `programa_id`,
                `municipio_id`
            ) VALUE (
                :archivo_id,
                :codigo,
                :nombre,
                :apellido,
                :semestre,
                :estrato,
                :promedio,
                :programa_id,
                :municipio_id
            );
        ", [
            ":archivo_id" => $data['archivo_id'],
            ":codigo" => $data['codigo'],
            ":nombre" => $data['nombre'],
            ":apellido" => $data['apellido'],
            ":semestre" => $data['semestre'],
            ":estrato" => $data['estrato'],
            ":promedio" => $data['promedio'],
            ":programa_id" => $data['programa_id'],
            ":municipio_id" => $data['municipio_id'],
        ]);

        return $this->db->lastInsertId();
    }

    public function delete_by_id($id) {
        return$this->exec("
            DELETE FROM estudiante WHERE id = :id;
        ", [
            ":id" => $id,
        ]);
    }
}