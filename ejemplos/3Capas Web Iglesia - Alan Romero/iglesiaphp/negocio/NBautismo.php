<?php
require_once __DIR__ . '/../datos/DBautismo.php';

class NBautismo {
    private DBautismo $datosBautismo;

    public function __construct() {
        $this->datosBautismo = new DBautismo();
    }

    public function crear(string $fecha, string $lugar, int $idMiembro): bool {
        if ($this->existeBautismo($idMiembro)) {
            return false;
        }
        $this->datosBautismo->setFecha($fecha);
        $this->datosBautismo->setLugar($lugar);
        $this->datosBautismo->setIdMiembro($idMiembro);
        return $this->datosBautismo->crear();
    }


    public function editar(int $id, string $fecha, string $lugar, int $idMiembro): bool {
        $this->datosBautismo->setId($id);
        $this->datosBautismo->setFecha($fecha);
        $this->datosBautismo->setLugar($lugar);
        $this->datosBautismo->setIdMiembro($idMiembro);
        return $this->datosBautismo->editar();
    }

    public function eliminar(int $id): bool {
        $this->datosBautismo->setId($id);
        return $this->datosBautismo->eliminar();
    }

    public function getBautismos(): array {
        return $this->datosBautismo->getBautismos();
    }

    private function existeBautismo(int $idMiembro): bool {
        return $this->datosBautismo->existeBautismoParaMiembro($idMiembro);
    }
    
}
