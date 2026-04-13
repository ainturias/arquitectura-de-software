<?php
// Capa de Negocio - Horario (valida choques de horario)
require_once __DIR__ . '/../datos/DHorario.php';

class NHorario {
    private DHorario $datosHorario;

    public function __construct() {
        $this->datosHorario = new DHorario();
    }

    public function crear(int $id_aula, int $id_grupo, int $dia, string $hora_inicio, string $hora_fin): string {
        if ($this->datosHorario->existeChoqueAula($id_aula, $dia, $hora_inicio, $hora_fin)) {
            return "El aula ya está ocupada en ese horario.";
        }
        $this->datosHorario->setIdAula($id_aula);
        $this->datosHorario->setIdGrupo($id_grupo);
        $this->datosHorario->setDiaSemana($dia);
        $this->datosHorario->setHoraInicio($hora_inicio);
        $this->datosHorario->setHoraFin($hora_fin);
        return $this->datosHorario->crear() ? "ok" : "Error al crear horario.";
    }

    public function editar(int $id, int $id_aula, int $id_grupo, int $dia, string $hora_inicio, string $hora_fin): string {
        if ($this->datosHorario->existeChoqueAula($id_aula, $dia, $hora_inicio, $hora_fin, $id)) {
            return "El aula ya está ocupada en ese horario.";
        }
        $this->datosHorario->setId($id);
        $this->datosHorario->setIdAula($id_aula);
        $this->datosHorario->setIdGrupo($id_grupo);
        $this->datosHorario->setDiaSemana($dia);
        $this->datosHorario->setHoraInicio($hora_inicio);
        $this->datosHorario->setHoraFin($hora_fin);
        return $this->datosHorario->editar() ? "ok" : "Error al editar horario.";
    }

    public function eliminar(int $id): bool {
        $this->datosHorario->setId($id);
        return $this->datosHorario->eliminar();
    }

    public function listar(): array {
        return $this->datosHorario->listar();
    }
}
