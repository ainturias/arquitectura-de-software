<?php
require_once __DIR__ . '/../config/Conexion.php';

class DMatrimonio {
    private ?int $id = null;
    private string $fecha;
    private string $lugar;
    private int $id_esposo;
    private int $id_esposa;

    private PDO $con;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setFecha(string $fecha): void {
        $this->fecha = $fecha;
    }

    public function setLugar(string $lugar): void {
        $this->lugar = $lugar;
    }

    public function setIdEsposo(int $idEsposo): void {
        $this->id_esposo = $idEsposo;
    }

    public function setIdEsposa(int $idEsposa): void {
        $this->id_esposa = $idEsposa;
    }

    // Crear
    public function crear(): bool {
        $sql = "INSERT INTO matrimonio (fecha, lugar, id_esposo, id_esposa) VALUES (?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->fecha, $this->lugar, $this->id_esposo, $this->id_esposa]);
    }

    // Editar
    public function editar(): bool {
        $sql = "UPDATE matrimonio SET fecha = ?, lugar = ?, id_esposo = ?, id_esposa = ? WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->fecha, $this->lugar, $this->id_esposo, $this->id_esposa, $this->id]);
        return $stmt->rowCount() > 0;
    }

    // Eliminar
    public function eliminar(): bool {
        $sql = "DELETE FROM matrimonio WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->rowCount() > 0;
    }

    // Obtener lista de matrimonios
    public function getMatrimonios(): array {
        $sql = "SELECT m.id, m.fecha, m.lugar,
                       e.id AS id_esposo, e.nombre AS nombre_esposo,
                       s.id AS id_esposa, s.nombre AS nombre_esposa
                FROM matrimonio m
                JOIN miembro e ON m.id_esposo = e.id
                JOIN miembro s ON m.id_esposa = s.id
                ORDER BY m.id ASC";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function miembroEstaBautizado(int $idMiembro): bool {
        $sql = "SELECT COUNT(*) FROM bautismo WHERE id_miembro = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$idMiembro]);
        return $stmt->fetchColumn() > 0;
    }

    // Validación: ¿el miembro ya tiene un matrimonio?
    public function existeMatrimonioParaMiembro(int $idMiembro): bool {
        $sql = "SELECT COUNT(*) FROM matrimonio
                WHERE id_esposo = ? OR id_esposa = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$idMiembro, $idMiembro]);
        return $stmt->fetchColumn() > 0;
    }

    // Validación para edición: ¿el miembro está en otro matrimonio distinto?
    public function existeMatrimonioParaMiembroDistinto(int $idMiembro, int $idActual): bool {
        $sql = "SELECT COUNT(*) FROM matrimonio
                WHERE (id_esposo = :id OR id_esposa = :id)
                AND id <> :actual";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([
            ':id' => $idMiembro,
            ':actual' => $idActual
        ]);
        return $stmt->fetchColumn() > 0;
    }
}
