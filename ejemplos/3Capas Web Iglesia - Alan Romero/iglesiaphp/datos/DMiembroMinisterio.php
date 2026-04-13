<?php
require_once __DIR__ . '/../config/Conexion.php';

class DMiembroMinisterio {
    private ?int $id_ministerio = null;
    private ?int $id_miembro = null;
    private string $fecha_union = '';
    private PDO $con;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    // Setters
    public function setIdMinisterio(int $idMinisterio): void {
        $this->id_ministerio = $idMinisterio;
    }

    public function setIdMiembro(int $idMiembro): void {
        $this->id_miembro = $idMiembro;
    }

    public function setFechaUnion(string $fecha): void {
        $this->fecha_union = $fecha;
    }

    // Operaciones

    public function asignar(): bool {
        $sql = "INSERT INTO miembro_ministerio (id_ministerio, id_miembro, fecha_union) VALUES (?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_ministerio, $this->id_miembro, $this->fecha_union]);
        return $stmt->rowCount() > 0;
    }

    public function quitar(): bool {
        $sql = "DELETE FROM miembro_ministerio WHERE id_ministerio = ? AND id_miembro = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_ministerio, $this->id_miembro]);
        return $stmt->rowCount() > 0;
    }

    public function getMiembrosPorMinisterio(): array {
        $sql = "SELECT m.id, m.nombre, m.ci, m.fecha_nacimiento, m.sexo, m.telefono, mm.fecha_union
                FROM miembro m
                JOIN miembro_ministerio mm ON m.id = mm.id_miembro
                WHERE mm.id_ministerio = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_ministerio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeAsignacion(): bool {
        $sql = "SELECT COUNT(*) FROM miembro_ministerio WHERE id_ministerio = ? AND id_miembro = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_ministerio, $this->id_miembro]);
        return $stmt->fetchColumn() > 0;
    }
}
