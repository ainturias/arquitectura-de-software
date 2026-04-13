<?php
require_once __DIR__ . '/../config/Conexion.php';

class DCurso {
    private ?int $id = null;
    private string $nombre;
    private string $fecha_inicio;
    private string $fecha_fin;
    private PDO $con;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function setFechaInicio(string $fechaInicio): void {
        $this->fecha_inicio = $fechaInicio;
    }

    public function setFechaFin(string $fechaFin): void {
        $this->fecha_fin = $fechaFin;
    }

    // Operaciones
    public function crear(): bool {
        $sql = "INSERT INTO curso (nombre, fecha_inicio, fecha_fin) VALUES (?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->nombre, $this->fecha_inicio, $this->fecha_fin]);
    }

    public function editar(): bool {
        $sql = "UPDATE curso SET nombre = ?, fecha_inicio = ?, fecha_fin = ? WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->nombre, $this->fecha_inicio, $this->fecha_fin, $this->id]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(): bool {
        $sql = "DELETE FROM curso WHERE id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->rowCount() > 0;
    }

    public function getCursos(): array {
        $sql = "SELECT * FROM curso ORDER BY id ASC";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
