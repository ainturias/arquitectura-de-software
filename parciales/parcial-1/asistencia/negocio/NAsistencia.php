<?php
// Capa de Negocio - Asistencia (CU Complejo)
// Maneja el proceso de marcar asistencia cuando el estudiante escanea el QR
require_once __DIR__ . '/../datos/DAsistencia.php';

class NAsistencia
{
    private DAsistencia $datosAsistencia;

    public function __construct()
    {
        $this->datosAsistencia = new DAsistencia();
    }

    // Obtiene los datos del horario para mostrar en el formulario del estudiante
    public function obtenerDatosParaFormulario(int $id_horario): array
    {
        $horario = $this->datosAsistencia->buscarHorario($id_horario);
        if (!$horario) {
            return ['error' => 'No se encontró el horario asociado a este QR.'];
        }
        return [
            'error' => null,
            'datos_clase' => [
                'id_horario' => $horario['id_horario'],
                'id_grupo' => $horario['id_grupo'],
                'codigo_aula' => $horario['codigo_aula'],
                'nombre_materia' => $horario['nombre_materia'],
                'grupo_nombre' => $horario['grupo_nombre'],
                'hora_actual' => date('H:i:s')
            ]
        ];
    }

    // Marca la asistencia del estudiante en un horario
    public function marcarAsistencia(string $registro, int $id_horario): string
    {
        $registro = trim($registro);

        // 1. Buscamos el horario para saber a qué grupo pertenece
        $horario = $this->datosAsistencia->buscarHorario($id_horario);
        if (!$horario) {
            return 'No se encontró la información del horario.';
        }

        $id_grupo = $horario['id_grupo'];

        // 2. Verificamos que el estudiante esté inscrito en ese grupo
        $id_estudiante = $this->datosAsistencia->buscarEstudiantePorRegistroYGrupo($registro, $id_grupo);
        if (!$id_estudiante) {
            return 'Registro no encontrado o no estás inscrito en esta clase.';
        }

        // 3. Registramos la asistencia
        $this->datosAsistencia->setIdEstudiante($id_estudiante);
        $this->datosAsistencia->setIdHorario($id_horario);

        $seRegistro = $this->datosAsistencia->registrarAsistencia();
        if ($seRegistro) {
            return '¡Asistencia registrada con éxito!';
        } else {
            return 'Ya registraste tu asistencia para esta clase hoy.';
        }
    }

    // Reporte: lista asistencias de un grupo con filtro de fecha
    public function listarPorGrupo(int $id_grupo, ?string $fecha): array
    {
        return $this->datosAsistencia->listarPorGrupo($id_grupo, $fecha);
    }
}
