<?php
require_once __DIR__ . '/../datos/DGrupo.php';
require_once __DIR__ . '/../datos/DMateria.php';
require_once __DIR__ . '/../datos/DInscripcion.php';

class NGrupo {
    private DGrupo $datosGrupo;
    private DInscripcion $datosInscripcion;

    public function __construct() {
        $this->datosGrupo = new DGrupo();
        $this->datosInscripcion = new DInscripcion();
    }

    public function crear(int $id_materia, string $nombre): bool {
        // Validaciones de negocio
        if ($id_materia <= 0 || $nombre === '') {
            return false;
        }

        if ($this->existeGrupoEnMateria($id_materia, $nombre)) {   //!revisar
            return false; // (id_materia, nombre) ya existe
        }

        $this->datosGrupo->setIdMateria($id_materia);
        $this->datosGrupo->setNombre($nombre);
        return $this->datosGrupo->crear();
    }

    public function editar(int $id, int $id_materia, string $nombre): bool {
        if ($id <= 0 || $id_materia <= 0 || $nombre === '') {
            return false;
        }
        // Evita colisión con otro grupo distinto
        if ($this->existeGrupoEnMateriaDistinto($id_materia, $nombre, $id)) {
            return false;
        }

        $this->datosGrupo->setId($id);
        $this->datosGrupo->setIdMateria($id_materia);
        $this->datosGrupo->setNombre($nombre);
        return $this->datosGrupo->editar();
    }

    public function eliminar(int $id): bool {
        $this->datosGrupo->setId($id);
        return $this->datosGrupo->eliminar();
    }

    public function listar(): array
    {
        return $this->datosGrupo->listar();
    }


    public function listarEstudiantesDeGrupo(int $id_grupo): array
    {
        if ($id_grupo <= 0) return [];
        $this->datosInscripcion->setIdGrupo($id_grupo);
        return $this->datosInscripcion->listarEstudiantesDeGrupo();
    }

    public function listarEstudiantesNoAsignados(int $id_grupo): array
    {
        if ($id_grupo <= 0) return [];
        $this->datosInscripcion->setIdGrupo($id_grupo);
        return $this->datosInscripcion->listarEstudiantesNoAsignados();
    }

    public function asignarEstudiante(int $id_grupo, int $id_estudiante, ?string $fecha_inscripcion = null): bool
    {
        if ($id_grupo <= 0 || $id_estudiante <= 0) return false;

        $this->datosInscripcion->setIdGrupo($id_grupo);
        $this->datosInscripcion->setIdEstudiante($id_estudiante);

        // evitar duplicado por UNIQUE (id_estudiante, id_grupo)
        if ($this->datosInscripcion->existeAsignacion()) return false;

        $this->datosInscripcion->setFechaInscripcion($fecha_inscripcion);
        return $this->datosInscripcion->asignarEstudiante();
    }

    public function quitarEstudiante(int $id_grupo, int $id_estudiante): bool
    {
        if ($id_grupo <= 0 || $id_estudiante <= 0) return false;

        $this->datosInscripcion->setIdGrupo($id_grupo);
        $this->datosInscripcion->setIdEstudiante($id_estudiante);
        return $this->datosInscripcion->quitarEstudiante();
    }


    private function existeGrupoEnMateria(int $id_materia, string $nombre): bool {
        return $this->datosGrupo->existeGrupoEnMateria($id_materia, $nombre);
    }

    private function existeGrupoEnMateriaDistinto(int $id_materia, string $nombre, int $id_grupo_actual): bool {
        return $this->datosGrupo->existeGrupoEnMateriaDistinto($id_materia, $nombre, $id_grupo_actual);
    }
}



















    // public function listarEstudiantes(): array
    // {
    //     return $this->datosGrupo->listarEstudiantes();
    // }