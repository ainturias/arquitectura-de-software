<?php
// Capa de Datos - Asistencia (CU Transaccional)
// Maneja el registro de asistencia del estudiante vía QR
require_once __DIR__ . '/../config/Conexion.php';

class DAsistencia {
    private PDO $con;
    private ?int $id_estudiante = null;
    private ?int $id_horario = null;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    public function setIdEstudiante(int $id): void { $this->id_estudiante = $id; }
    public function setIdHorario(int $id): void { $this->id_horario = $id; }

    // Busca los datos del horario para mostrar en el formulario de asistencia
    public function buscarHorario(int $id_horario): ?array {
        $sql = "SELECT h.id_horario, h.id_grupo, a.codigo AS codigo_aula,
                       m.nombre_materia, g.nombre AS grupo_nombre
                FROM horario h
                JOIN aula    a ON h.id_aula  = a.id_aula
                JOIN grupo   g ON h.id_grupo = g.id_grupo
                JOIN materia m ON g.id_materia = m.id_materia
                WHERE h.id_horario = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$id_horario]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Verifica que el estudiante esté inscrito en el grupo del horario
    public function buscarEstudiantePorRegistroYGrupo(string $registro, int $id_grupo): ?int {
        $sql = "SELECT e.id_estudiante
                FROM estudiante e
                JOIN inscripcion i ON e.id_estudiante = i.id_estudiante
                WHERE e.registro = ? AND i.id_grupo = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$registro, $id_grupo]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? (int)$resultado['id_estudiante'] : null;
    }

    // Inserta el registro de asistencia en la base de datos
    public function registrarAsistencia(): bool {
        $sql = "INSERT INTO asistencia (id_estudiante, id_horario) VALUES (?, ?)";
        $stmt = $this->con->prepare($sql);
        try {
            return $stmt->execute([$this->id_estudiante, $this->id_horario]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23505') {
                return false;
            }
            throw $e;
        }
    }

    // Lista las asistencias registradas para un grupo, con filtro de fecha
    public function listarPorGrupo(int $id_grupo, ?string $fecha): array {
        $params = [$id_grupo];
        $filtroFecha = "";
        if (!empty($fecha)) {
            $filtroFecha = " AND DATE(a.fecha_hora) = ?";
            $params[] = $fecha;
        }

        $sql = "SELECT e.registro,
                       (e.apellido || ' ' || e.nombre) AS estudiante,
                       to_char(a.fecha_hora, 'YYYY-MM-DD') AS fecha,
                       to_char(a.fecha_hora, 'HH24:MI') AS hora,
                       a.estado,
                       au.codigo AS aula,
                       (h.hora_inicio || ' - ' || h.hora_fin) AS horario_rango
                FROM asistencia a
                JOIN estudiante e ON e.id_estudiante = a.id_estudiante
                JOIN horario h ON h.id_horario = a.id_horario
                JOIN aula au ON au.id_aula = h.id_aula
                WHERE h.id_grupo = ?
                $filtroFecha
                ORDER BY a.fecha_hora DESC";
        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
