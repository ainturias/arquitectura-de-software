<?php
// Capa de Datos - Horario (usa FK de aula y grupo)
require_once __DIR__ . '/../config/Conexion.php';

class DHorario {
    private ?int $id_horario = null;
    private int $id_aula;
    private int $id_grupo;
    private int $dia_semana;
    private string $hora_inicio;
    private string $hora_fin;

    private PDO $con;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    public function setId(int $id): void { $this->id_horario = $id; }
    public function setIdAula(int $id_aula): void { $this->id_aula = $id_aula; }
    public function setIdGrupo(int $id_grupo): void { $this->id_grupo = $id_grupo; }
    public function setDiaSemana(int $dia): void { $this->dia_semana = $dia; }
    public function setHoraInicio(string $hora): void { $this->hora_inicio = $hora; }
    public function setHoraFin(string $hora): void { $this->hora_fin = $hora; }

    public function crear(): bool {
        $sql = "INSERT INTO horario (id_aula, id_grupo, dia_semana, hora_inicio, hora_fin)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([
            $this->id_aula, $this->id_grupo, $this->dia_semana,
            $this->hora_inicio, $this->hora_fin
        ]);
    }

    public function editar(): bool {
        $sql = "UPDATE horario SET id_aula=?, id_grupo=?, dia_semana=?, hora_inicio=?, hora_fin=?
                WHERE id_horario=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([
            $this->id_aula, $this->id_grupo, $this->dia_semana,
            $this->hora_inicio, $this->hora_fin, $this->id_horario
        ]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(): bool {
        $sql = "DELETE FROM horario WHERE id_horario=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_horario]);
        return $stmt->rowCount() > 0;
    }

    public function listar(): array {
        $sql = "SELECT h.id_horario, h.id_aula, a.codigo AS aula_codigo,
                       h.id_grupo, g.nombre AS grupo_nombre,
                       m.sigla, m.nombre_materia,
                       h.dia_semana, h.hora_inicio, h.hora_fin
                FROM horario h
                JOIN aula    a ON h.id_aula  = a.id_aula
                JOIN grupo   g ON h.id_grupo = g.id_grupo
                JOIN materia m ON g.id_materia = m.id_materia
                ORDER BY h.dia_semana, h.hora_inicio";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verifica si hay choque de horarios en la misma aula
    public function existeChoqueAula(int $id_aula, int $dia, string $ini, string $fin, ?int $excluirId = null): bool {
        $sql = "SELECT COUNT(*) FROM horario
                WHERE id_aula = ? AND dia_semana = ?
                AND (hora_inicio < ? AND hora_fin > ?)";
        $params = [$id_aula, $dia, $fin, $ini];
        if ($excluirId !== null) {
            $sql .= " AND id_horario <> ?";
            $params[] = $excluirId;
        }
        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
