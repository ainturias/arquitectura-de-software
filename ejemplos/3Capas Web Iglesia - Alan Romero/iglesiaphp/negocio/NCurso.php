<?php
require_once __DIR__ . '/../datos/DCurso.php';
require_once __DIR__ . '/../datos/DMiembroCurso.php';

class NCurso {
    private DCurso $datosCurso;
    private DMiembroCurso $datosMiembroCurso;

    public function __construct() {
        $this->datosCurso = new DCurso();
        $this->datosMiembroCurso = new DMiembroCurso();
    }

    public function crear(string $nombre, string $fechaInicio, string $fechaFin): bool {
        if (!$this->validarFechas($fechaInicio, $fechaFin)) return false;

        $this->datosCurso->setNombre($nombre);
        $this->datosCurso->setFechaInicio($fechaInicio);
        $this->datosCurso->setFechaFin($fechaFin);
        return $this->datosCurso->crear();
    }

    public function editar(int $id, string $nombre, string $fechaInicio, string $fechaFin): bool {
        if (!$this->validarFechas($fechaInicio, $fechaFin)) return false;

        $this->datosCurso->setId($id);
        $this->datosCurso->setNombre($nombre);
        $this->datosCurso->setFechaInicio($fechaInicio);
        $this->datosCurso->setFechaFin($fechaFin);
        return $this->datosCurso->editar();
    }

    public function eliminar(int $id): bool {
        $this->datosCurso->setId($id);
        return $this->datosCurso->eliminar();
    }

    public function getCursos(): array {
        return $this->datosCurso->getCursos();
    }

    public function asignarMiembro(int $idCurso, int $idMiembro): bool {
        if ($this->existeAsignacion($idCurso, $idMiembro)) return false;

        if (!$this->cursoEstaActivo($idCurso)) return false;

        $this->datosMiembroCurso->setIdCurso($idCurso);
        $this->datosMiembroCurso->setIdMiembro($idMiembro);
        $this->datosMiembroCurso->setEstado("En proceso");
        return $this->datosMiembroCurso->asignar();
    }

    public function quitarMiembro(int $idCurso, int $idMiembro): bool {
        $this->datosMiembroCurso->setIdCurso($idCurso);
        $this->datosMiembroCurso->setIdMiembro($idMiembro);
        return $this->datosMiembroCurso->quitar();
    }

    public function calificarMiembro(int $idCurso, int $idMiembro, float $calificacion): bool {
        $this->datosMiembroCurso->setIdCurso($idCurso);
        $this->datosMiembroCurso->setIdMiembro($idMiembro);
        $this->datosMiembroCurso->setCalificacion($calificacion);
        $this->datosMiembroCurso->setEstado("Terminado");
        return $this->datosMiembroCurso->calificar();
    }

    public function getMiembrosAsignados(int $idCurso): array {
        $this->datosMiembroCurso->setIdCurso($idCurso);
        return $this->datosMiembroCurso->getMiembrosPorCurso();
    }

    private function existeAsignacion(int $idCurso, int $idMiembro): bool {
        $this->datosMiembroCurso->setIdCurso($idCurso);
        $this->datosMiembroCurso->setIdMiembro($idMiembro);
        return $this->datosMiembroCurso->existeAsignacion();
    }

    private function validarFechas(string $fechaInicio, string $fechaFin): bool {
        return strtotime($fechaInicio) <= strtotime($fechaFin);
    }

    private function cursoEstaActivo(int $idCurso): bool {
        $cursos = $this->getCursos();
        foreach ($cursos as $c) {
            if ($c['id'] == $idCurso) {
                $hoy = date('Y-m-d');
                return $hoy >= $c['fecha_inicio'] && $hoy <= $c['fecha_fin'];
            }
        }
        return false;
    }
}
