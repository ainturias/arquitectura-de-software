<?php
// Capa de Negocio - Inscripción (CU Transaccional)
// Maneja DInscripcion (cabecera) + DDetalleInscripcion (detalle)
// Equivalente a NFactura del ingeniero
require_once __DIR__ . '/../datos/DInscripcion.php';
require_once __DIR__ . '/../datos/DDetalleInscripcion.php';

class NInscripcion
{
    private DInscripcion $datosInscripcion;
    private DDetalleInscripcion $datosDetalle;

    public function __construct()
    {
        $this->datosInscripcion = new DInscripcion();
        $this->datosDetalle = new DDetalleInscripcion();
    }

    // Lista los estudiantes inscritos en un grupo
    public function listarEstudiantesDeGrupo(int $id_grupo): array
    {
        return $this->datosDetalle->listarEstudiantesDeGrupo($id_grupo);
    }

    // Quita a un estudiante del grupo
    public function desinscribir(int $id_estudiante, int $id_grupo): bool
    {
        return $this->datosDetalle->desinscribir($id_estudiante, $id_grupo);
    }

    // Loop de Composición: Crea cabecera + inscribe cada estudiante
    // Este es el patrón del ingeniero: NFactura.crearFactura()
    public function inscribirListaPorIds(int $id_grupo, array $ids_estudiantes): array
    {
        // 1. Crear la cabecera de inscripción
        $this->datosInscripcion->setIdGrupo($id_grupo);
        $this->datosInscripcion->crearInscripcion();
        $id_inscripcion = $this->datosInscripcion->getIdInscripcion();

        $ok = 0;
        $errores = 0;

        // 2. Foreach: inscribir cada estudiante en el detalle
        $this->datosDetalle->setIdInscripcion($id_inscripcion);
        foreach ($ids_estudiantes as $id_est) {
            $this->datosDetalle->setIdEstudiante((int) $id_est);
            $seInscribio = $this->datosDetalle->inscribir();
            if ($seInscribio) {
                $ok++;
            } else {
                $errores++;
            }
        }

        return ['ok' => $ok, 'errores' => $errores];
    }
}
