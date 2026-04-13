<?php
require_once __DIR__ . '/../datos/DHorario.php';

class NHorario {
    private DHorario $datosHorario;

    public function __construct() {
        $this->datosHorario = new DHorario();
    }

    public function crear(int $id_aula, int $id_grupo, int $dia_semana, string $hora_inicio, string $hora_fin): bool {
        if (!$this->validarEntradas($id_aula, $id_grupo, $dia_semana, $hora_inicio, $hora_fin)) {
            return false;
        }

        if ($this->hayChoqueAula($id_aula, $dia_semana, $hora_inicio, $hora_fin)) {
            return false;
        }
        if ($this->hayChoqueGrupo($id_grupo, $dia_semana, $hora_inicio, $hora_fin)) {
            return false;
        }

        $this->datosHorario->setIdAula($id_aula);
        $this->datosHorario->setIdGrupo($id_grupo);
        $this->datosHorario->setDiaSemana($dia_semana);
        $this->datosHorario->setHoraInicio($hora_inicio);
        $this->datosHorario->setHoraFin($hora_fin);
        return $this->datosHorario->crear();
    }

    public function editar(int $id_horario, int $id_aula, int $id_grupo, int $dia_semana, string $hora_inicio, string $hora_fin): bool {
        if ($id_horario <= 0) return false;
        if (!$this->validarEntradas($id_aula, $id_grupo, $dia_semana, $hora_inicio, $hora_fin)) {
            return false;
        }

        if ($this->hayChoqueAula($id_aula, $dia_semana, $hora_inicio, $hora_fin, $id_horario)) {
            return false;
        }
        if ($this->hayChoqueGrupo($id_grupo, $dia_semana, $hora_inicio, $hora_fin, $id_horario)) {
            return false;
        }

        $this->datosHorario->setId($id_horario);
        $this->datosHorario->setIdAula($id_aula);
        $this->datosHorario->setIdGrupo($id_grupo);
        $this->datosHorario->setDiaSemana($dia_semana);
        $this->datosHorario->setHoraInicio($hora_inicio);
        $this->datosHorario->setHoraFin($hora_fin);
        return $this->datosHorario->editar();
    }

    public function eliminar(int $id_horario): bool {
        $this->datosHorario->setId($id_horario);
        return $this->datosHorario->eliminar();
    }

    public function listar(): array {
        return $this->datosHorario->listar();
    }

    // ================== Validaciones internas ==================
    private function validarEntradas(int $id_aula, int $id_grupo, int $dia_semana, string $hora_inicio, string $hora_fin): bool {
        if ($id_aula <= 0 || $id_grupo <= 0) return false;
        if ($dia_semana < 1 || $dia_semana > 7) return false;
        if (!$this->validarHora($hora_inicio) || !$this->validarHora($hora_fin)) return false;
        if (!$this->rangoValido($hora_inicio, $hora_fin)) return false;
        return true;
    }

    private function validarHora(string $hora): bool {
        // Acepta HH:MM (24h). Si usas segundos, ajusta el regex.
        return (bool)preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $hora);
    }

    private function rangoValido(string $hora_inicio, string $hora_fin): bool {
        // Comparación lexicográfica funciona en formato HH:MM
        return $hora_inicio < $hora_fin;
    }


    private function hayChoqueAula(int $id_aula, int $dia_semana, string $hora_inicio, string $hora_fin, ?int $excluirId = null): bool {
        return $this->datosHorario->existeChoqueAula($id_aula, $dia_semana, $hora_inicio, $hora_fin, $excluirId);
    }

    private function hayChoqueGrupo(int $id_grupo, int $dia_semana, string $hora_inicio, string $hora_fin, ?int $excluirId = null): bool {
        return $this->datosHorario->existeChoqueGrupo($id_grupo, $dia_semana, $hora_inicio, $hora_fin, $excluirId);
    }
}
