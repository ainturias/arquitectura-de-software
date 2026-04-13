<?php
require_once __DIR__ . '/../datos/DMatrimonio.php';

class NMatrimonio {
    private DMatrimonio $datosMatrimonio;

    public function __construct() {
        $this->datosMatrimonio = new DMatrimonio();
    }

    public function crear(string $fecha, string $lugar, int $idEsposo, int $idEsposa): bool {
        if (!$this->miembrosEstanBautizados($idEsposo, $idEsposa)) {
            return false;
        }
        if ($this->existeMatrimonioParaMiembro($idEsposo) || $this->existeMatrimonioParaMiembro($idEsposa)) {
            return false;
        }
    
        $this->datosMatrimonio->setFecha($fecha);
        $this->datosMatrimonio->setLugar($lugar);
        $this->datosMatrimonio->setIdEsposo($idEsposo);
        $this->datosMatrimonio->setIdEsposa($idEsposa);
        return $this->datosMatrimonio->crear();
    }
    
    public function editar(int $id, string $fecha, string $lugar, int $idEsposo, int $idEsposa): bool {
        if (!$this->miembrosEstanBautizados($idEsposo, $idEsposa)) {
            return false;
        }
        if ($this->esMiembroCasadoEnOtro($idEsposo, $id) || $this->esMiembroCasadoEnOtro($idEsposa, $id)) {
            return false;
        }
    
        $this->datosMatrimonio->setId($id);
        $this->datosMatrimonio->setFecha($fecha);
        $this->datosMatrimonio->setLugar($lugar);
        $this->datosMatrimonio->setIdEsposo($idEsposo);
        $this->datosMatrimonio->setIdEsposa($idEsposa);
        return $this->datosMatrimonio->editar();
    }

    public function eliminar(int $id): bool {
        $this->datosMatrimonio->setId($id);
        return $this->datosMatrimonio->eliminar();
    }

    public function getMatrimonios(): array {
        return $this->datosMatrimonio->getMatrimonios();
    }

    private function miembrosEstanBautizados(int $idEsposo, int $idEsposa): bool {
        return $this->datosMatrimonio->miembroEstaBautizado($idEsposo) &&
               $this->datosMatrimonio->miembroEstaBautizado($idEsposa);
    }    

    private function existeMatrimonioParaMiembro(int $idMiembro): bool {
        return $this->datosMatrimonio->existeMatrimonioParaMiembro($idMiembro);
    }

    private function esMiembroCasadoEnOtro(int $idMiembro, int $idMatrimonioActual): bool {
        return $this->datosMatrimonio->existeMatrimonioParaMiembroDistinto($idMiembro, $idMatrimonioActual);
    }
}
