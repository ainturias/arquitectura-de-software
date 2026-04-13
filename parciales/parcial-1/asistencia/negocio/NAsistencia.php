<?php
// Capa de Negocio - Asistencia (CU Transaccional)
// Maneja la inscripcion de estudiantes y el registro de asistencia
require_once __DIR__ . '/../datos/DAsistencia.php';
require_once __DIR__ . '/../datos/DInscripcion.php';

class NAsistencia {
    private DAsistencia $datosAsistencia;
    private DInscripcion $datosInscripcion;

    public function __construct() {
        $this->datosAsistencia = new DAsistencia();
        $this->datosInscripcion = new DInscripcion();
    }

    // --- Inscripción (parte del CU Transaccional) ---
    public function listarEstudiantesDeGrupo(int $id_grupo): array {
        $this->datosInscripcion->setIdGrupo($id_grupo);
        return $this->datosInscripcion->listarEstudiantesDeGrupo();
    }

    public function listarEstudiantesNoInscritos(int $id_grupo): array {
        $this->datosInscripcion->setIdGrupo($id_grupo);
        return $this->datosInscripcion->listarEstudiantesNoInscritos();
    }

    public function inscribir(int $id_estudiante, int $id_grupo): bool {
        $this->datosInscripcion->setIdEstudiante($id_estudiante);
        $this->datosInscripcion->setIdGrupo($id_grupo);
        return $this->datosInscripcion->inscribir();
    }

    public function desinscribir(int $id_estudiante, int $id_grupo): bool {
        $this->datosInscripcion->setIdEstudiante($id_estudiante);
        $this->datosInscripcion->setIdGrupo($id_grupo);
        return $this->datosInscripcion->desinscribir();
    }

    // --- Asistencia vía QR (parte del CU Transaccional) ---
    public function obtenerDatosParaFormulario(int $id_aula): array {
        $claseActual = $this->datosAsistencia->buscarClaseActualPorAula($id_aula);
        if (!$claseActual) {
            return ['error' => 'No hay ninguna clase programada en esta aula en este momento.'];
        }
        return [
            'error' => null,
            'datos_clase' => [
                'codigo_aula' => $claseActual['codigo_aula'],
                'nombre_materia' => $claseActual['nombre_materia'],
                'hora_actual' => date('H:i:s')
            ]
        ];
    }

    public function marcarAsistencia(string $registro, int $id_aula): string {
        $registro = trim($registro);

        $claseActual = $this->datosAsistencia->buscarClaseActualPorAula($id_aula);
        if (!$claseActual) {
            return 'El tiempo para marcar asistencia ha terminado.';
        }

        $id_horario = $claseActual['id_horario'];
        $id_grupo = $claseActual['id_grupo'];

        $id_estudiante = $this->datosAsistencia->buscarEstudiantePorRegistroYGrupo($registro, $id_grupo);
        if (!$id_estudiante) {
            return 'Registro no encontrado o no estás inscrito en esta clase.';
        }

        $this->datosAsistencia->setIdEstudiante($id_estudiante);
        $this->datosAsistencia->setIdHorario($id_horario);

        if ($this->datosAsistencia->registrarAsistencia()) {
            return '¡Asistencia registrada con éxito!';
        } else {
            return 'Ya registraste tu asistencia para esta clase hoy.';
        }
    }

    // --- Reporte de asistencia ---
    public function listarPorGrupo(int $id_grupo, ?string $fecha): array {
        return $this->datosAsistencia->listarPorGrupo($id_grupo, $fecha);
    }
}
