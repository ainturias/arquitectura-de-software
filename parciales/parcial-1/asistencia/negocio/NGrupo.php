<?php
// Capa de Negocio - Grupo
require_once __DIR__ . '/../datos/DGrupo.php';

class NGrupo {
    private DGrupo $datosGrupo;

    public function __construct() {
        $this->datosGrupo = new DGrupo();
    }

    public function crear(int $id_materia, string $nombre): bool {
        if ($this->datosGrupo->existeGrupoEnMateria($id_materia, $nombre)) {
            return false;
        }
        $this->datosGrupo->setIdMateria($id_materia);
        $this->datosGrupo->setNombre($nombre);
        return $this->datosGrupo->crear();
    }

    public function editar(int $id, int $id_materia, string $nombre): bool {
        $this->datosGrupo->setId($id);
        $this->datosGrupo->setIdMateria($id_materia);
        $this->datosGrupo->setNombre($nombre);
        return $this->datosGrupo->editar();
    }

    public function eliminar(int $id): bool {
        $this->datosGrupo->setId($id);
        return $this->datosGrupo->eliminar();
    }

    public function listar(): array {
        return $this->datosGrupo->listar();
    }
}
