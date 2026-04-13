<?php
require_once __DIR__ . '/../datos/DMiembro.php';

class NMiembro {
    private DMiembro $datosMiembro;

    public function __construct() {
        $this->datosMiembro = new DMiembro();
    }

    public function crear(string $ci, string $nombre, string $fechaNacimiento, string $sexo, string $telefono): bool {
        if ($this->existeCi($ci)) {
            return false; 
        }
        $this->datosMiembro->setCi($ci);
        $this->datosMiembro->setNombre($nombre);
        $this->datosMiembro->setFechaNacimiento($fechaNacimiento);
        $this->datosMiembro->setSexo($sexo);
        $this->datosMiembro->setTelefono($telefono);
        return $this->datosMiembro->crear();
    }

    public function editar(int $id, string $ci, string $nombre, string $fechaNacimiento, string $sexo, string $telefono): bool {
        $this->datosMiembro->setId($id);
        $this->datosMiembro->setCi($ci);
        $this->datosMiembro->setNombre($nombre);
        $this->datosMiembro->setFechaNacimiento($fechaNacimiento);
        $this->datosMiembro->setSexo($sexo);
        $this->datosMiembro->setTelefono($telefono);
        return $this->datosMiembro->editar();
    }

    public function eliminar(int $id): bool {
        $this->datosMiembro->setId($id);
        return $this->datosMiembro->eliminar();
    }

    public function getMiembros(): array {
        return $this->datosMiembro->getMiembros();
    }

    private function existeCi(string $ci): bool {
        return $this->datosMiembro->existeCi($ci);
    }

    public function getMiembrosHombres(): array {
        return $this->datosMiembro->getMiembrosHombres();
    }

    public function getMiembrosMujeres(): array {
        return $this->datosMiembro->getMiembrosMujeres();
    }
}
