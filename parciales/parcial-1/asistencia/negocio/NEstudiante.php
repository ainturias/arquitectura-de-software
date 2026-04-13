<?php
// Capa de Negocio - Estudiante
require_once __DIR__ . '/../datos/DEstudiante.php';

class NEstudiante {
    private DEstudiante $datosEstudiante;

    public function __construct() {
        $this->datosEstudiante = new DEstudiante();
    }

    public function crear(string $nombre, string $apellido, string $registro): bool {
        if ($this->datosEstudiante->existeRegistro($registro)) {
            return false;
        }
        $this->datosEstudiante->setNombre($nombre);
        $this->datosEstudiante->setApellido($apellido);
        $this->datosEstudiante->setRegistro($registro);
        return $this->datosEstudiante->crear();
    }

    public function editar(int $id, string $nombre, string $apellido, string $registro): bool {
        $this->datosEstudiante->setId($id);
        $this->datosEstudiante->setNombre($nombre);
        $this->datosEstudiante->setApellido($apellido);
        $this->datosEstudiante->setRegistro($registro);
        return $this->datosEstudiante->editar();
    }

    public function eliminar(int $id): bool {
        $this->datosEstudiante->setId($id);
        return $this->datosEstudiante->eliminar();
    }

    public function listar(): array {
        return $this->datosEstudiante->listar();
    }
}
