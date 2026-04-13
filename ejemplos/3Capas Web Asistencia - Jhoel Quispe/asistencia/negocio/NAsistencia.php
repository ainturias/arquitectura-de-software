<?php
require_once __DIR__ . '/../datos/DAsistencia.php';

class NAsistencia {
    private DAsistencia $datosAsistencia;

    public function __construct() {
        $this->datosAsistencia = new DAsistencia();
    }



    /**
     * Obtiene los datos de la clase para mostrar en el formulario.
     * (Ahora es más simple, ya no necesita cargar la lista de estudiantes)
     */
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

    /**
     * Valida y registra la asistencia usando el número de registro.
     */
    public function marcarAsistencia(string $registro, int $id_aula): string {
        $registro = trim($registro);


        // 1. Verificar clase activa
        $claseActual = $this->buscarClaseActualPorAula($id_aula);
        if (!$claseActual) {
            return 'El tiempo para marcar asistencia ha terminado.';
        }
        
        $id_horario = $claseActual['id_horario'];
        $id_grupo = $claseActual['id_grupo'];

        // 2. Verificar estudiante inscrito
        $id_estudiante = $this->buscarEstudiantePorRegistroYGrupo($registro, $id_grupo);
        if (!$id_estudiante) {
            return 'Número de registro no encontrado o no corresponde a un estudiante inscrito en esta clase.';
        }

        $this->datosAsistencia->setIdEstudiante($id_estudiante);
        $this->datosAsistencia->setIdHorario($id_horario);   
        // 3. Intentamos registrar la asistencia
        if ($this->datosAsistencia->registrarAsistencia()) {
            return '¡Asistencia registrada con éxito!';
        } else {
            return 'Ya has registrado tu asistencia para esta clase hoy.';
        }
    }

    public function buscarClaseActualPorAula(int $id_aula): ?array {
        return $this->datosAsistencia->buscarClaseActualPorAula($id_aula);
    }

    public function buscarEstudiantePorRegistroYGrupo(string $registro, int $id_grupo): ?int {
        return $this->datosAsistencia->buscarEstudiantePorRegistroYGrupo($registro, $id_grupo);
    }

}