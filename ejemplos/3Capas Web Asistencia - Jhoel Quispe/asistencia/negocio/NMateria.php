<?php
require_once __DIR__ . '/../datos/DMateria.php';

class NMateria {
    private DMateria $datosMateria;

    public function __construct() {
        $this->datosMateria = new DMateria();
    }

    public function crear(string $sigla, string $nombre_materia, string $nivel): bool {
        if ($this->existeSigla($sigla)) {
            return false; // sigla UNIQUE ya registrada
        }
        $this->datosMateria->setSigla($sigla);
        $this->datosMateria->setNombreMateria($nombre_materia);
        $this->datosMateria->setNivel($nivel);
        return $this->datosMateria->crear();
    }

    public function editar(int $id, string $sigla, string $nombre_materia, string $nivel): bool {
        $this->datosMateria->setId($id);
        $this->datosMateria->setSigla($sigla);
        $this->datosMateria->setNombreMateria($nombre_materia);
        $this->datosMateria->setNivel($nivel);
        return $this->datosMateria->editar();
    }

    public function eliminar(int $id): bool {
        $this->datosMateria->setId($id);
        return $this->datosMateria->eliminar();
    }

    public function listar(): array {
        return $this->datosMateria->listar();
    }

    private function existeSigla(string $sigla): bool {
        return $this->datosMateria->existeSigla($sigla);
    }


}
