<?php
require_once __DIR__ . '/../config/Conexion.php';

class DMiembroCurso {
    private ?int $id_curso = null;
    private ?int $id_miembro = null;
    private ?float $calificacion = null;
    private ?string $estado = null;
    private PDO $con;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    // Setters
    public function setIdCurso(int $id): void {
        $this->id_curso = $id;
    }

    public function setIdMiembro(int $id): void {
        $this->id_miembro = $id;
    }

    public function setCalificacion(float $nota): void {
        $this->calificacion = $nota;
    }

    public function setEstado(string $estado): void {
        $this->estado = $estado;
    }

    // Operaciones
    public function asignar(): bool {
        $sql = "INSERT INTO miembro_curso (id_curso, id_miembro, estado) VALUES (?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->id_curso, $this->id_miembro, $this->estado]);
    }

    public function quitar(): bool {
        $sql = "DELETE FROM miembro_curso WHERE id_curso = ? AND id_miembro = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->id_curso, $this->id_miembro]);
    }

    public function calificar(): bool {
        $sql = "UPDATE miembro_curso SET calificacion = ?, estado = ? 
                WHERE id_curso = ? AND id_miembro = ?";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->calificacion, $this->estado, $this->id_curso, $this->id_miembro]);
    }

    public function getMiembrosPorCurso(): array {
        $sql = "SELECT m.id, m.nombre, mc.calificacion, mc.estado
                FROM miembro m
                JOIN miembro_curso mc ON m.id = mc.id_miembro
                WHERE mc.id_curso = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_curso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeAsignacion(): bool {
        $sql = "SELECT COUNT(*) FROM miembro_curso WHERE id_curso = ? AND id_miembro = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_curso, $this->id_miembro]);
        return $stmt->fetchColumn() > 0;
    }
}
