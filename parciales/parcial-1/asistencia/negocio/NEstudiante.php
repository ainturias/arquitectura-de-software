<?php
// Capa de Negocio - Estudiante
require_once __DIR__ . '/../datos/DEstudiante.php';

class NEstudiante {
    private DEstudiante $datosEstudiante;

    public function __construct() {
        $this->datosEstudiante = new DEstudiante();
    }

    public function crear(string $nombre, string $apellido, string $registro): bool {
        $yaExiste = $this->datosEstudiante->existeRegistro($registro);
        if ($yaExiste) {
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

    // Lista estudiantes no inscritos en un grupo (para el combo de PInscripcion)
    public function listarNoInscritosEnGrupo(int $id_grupo): array {
        return $this->datosEstudiante->listarNoInscritosEnGrupo($id_grupo);
    }
}
