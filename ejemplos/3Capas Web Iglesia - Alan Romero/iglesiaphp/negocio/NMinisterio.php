<?php
require_once __DIR__ . '/../datos/DMinisterio.php';
require_once __DIR__ . '/../datos/DMiembroMinisterio.php';

class NMinisterio {
    private DMinisterio $datosMinisterio;
    private DMiembroMinisterio $datosMiembroMinisterio;

    public function __construct() {
        $this->datosMinisterio = new DMinisterio();
        $this->datosMiembroMinisterio = new DMiembroMinisterio();
    }

    public function crear(string $descripcion): bool {
        $this->datosMinisterio->setDescripcion($descripcion);
        return $this->datosMinisterio->crear();
    }

    public function editar(int $id, string $descripcion): bool {
        $this->datosMinisterio->setId($id);
        $this->datosMinisterio->setDescripcion($descripcion);
        return $this->datosMinisterio->editar();
    }

    public function eliminar(int $id): bool {
        $this->datosMinisterio->setId($id);
        return $this->datosMinisterio->eliminar();
    }

    public function getMinisterios(): array {
        return $this->datosMinisterio->getMinisterios();
    }

    public function asignarMiembro(int $idMinisterio, int $idMiembro, string $fechaUnion): bool {
        // Verificar si ya está asignado
        if ($this->existeAsignacion($idMinisterio, $idMiembro)) {
            return false; // ya está asignado
        }

        $this->datosMiembroMinisterio->setIdMinisterio($idMinisterio);
        $this->datosMiembroMinisterio->setIdMiembro($idMiembro);
        $this->datosMiembroMinisterio->setFechaUnion($fechaUnion);
        return $this->datosMiembroMinisterio->asignar();
    }
    
    
    public function quitarMiembro(int $idMinisterio, int $idMiembro): bool {
        $this->datosMiembroMinisterio->setIdMinisterio($idMinisterio);
        $this->datosMiembroMinisterio->setIdMiembro($idMiembro);
        return $this->datosMiembroMinisterio->quitar();
    }
    
    public function getMiembrosAsignados(int $idMinisterio): array {
        $this->datosMiembroMinisterio->setIdMinisterio($idMinisterio);
        return $this->datosMiembroMinisterio->getMiembrosPorMinisterio();
    }

    private function existeAsignacion(int $idMinisterio, int $idMiembro): bool {
        $this->datosMiembroMinisterio->setIdMinisterio($idMinisterio);
        $this->datosMiembroMinisterio->setIdMiembro($idMiembro);
        return $this->datosMiembroMinisterio->existeAsignacion();
    }
}
