<?php
// Capa de Negocio - Materia
require_once __DIR__ . '/../datos/DMateria.php';

class NMateria {
    private DMateria $datosMateria;

    public function __construct() {
        $this->datosMateria = new DMateria();
    }

    public function crear(string $sigla, string $nombre_materia): bool {
        if ($this->datosMateria->existeSigla($sigla)) {
            return false;
        }
        $this->datosMateria->setSigla($sigla);
        $this->datosMateria->setNombreMateria($nombre_materia);
        return $this->datosMateria->crear();
    }

    public function editar(int $id, string $sigla, string $nombre_materia): bool {
        $this->datosMateria->setId($id);
        $this->datosMateria->setSigla($sigla);
        $this->datosMateria->setNombreMateria($nombre_materia);
        return $this->datosMateria->editar();
    }

    public function eliminar(int $id): bool {
        $this->datosMateria->setId($id);
        return $this->datosMateria->eliminar();
    }

    public function listar(): array {
        return $this->datosMateria->listar();
    }
}
