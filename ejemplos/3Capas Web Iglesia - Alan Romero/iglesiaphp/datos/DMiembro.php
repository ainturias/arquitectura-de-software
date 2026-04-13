<?php
require_once __DIR__ . '/../config/Conexion.php';

class DMiembro {
    private ?int $id = null;
    private string $ci;
    private string $nombre;
    private string $fecha_nacimiento;
    private string $sexo;
    private string $telefono;

    private PDO $con;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setCi(string $ci): void {
        $this->ci = $ci;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function setFechaNacimiento(string $fecha): void {
        $this->fecha_nacimiento = $fecha;
    }

    public function setSexo(string $sexo): void {
        $this->sexo = $sexo;
    }

    public function setTelefono(string $telefono): void {
        $this->telefono = $telefono;
    }

    public function crear(): bool {
        $sql = "INSERT INTO miembro (ci, nombre, fecha_nacimiento, sexo, telefono) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->ci, $this->nombre, $this->fecha_nacimiento, $this->sexo, $this->telefono]);
    }

    public function editar(): bool {
        $sql = "UPDATE miembro SET ci=?, nombre=?, fecha_nacimiento=?, sexo=?, telefono=? WHERE id=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([
            $this->ci, $this->nombre, $this->fecha_nacimiento, $this->sexo, $this->telefono, $this->id
        ]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(): bool {
        $sql = "DELETE FROM miembro WHERE id=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->rowCount() > 0;
    }

    public function getMiembros(): array {
        $sql = "SELECT * FROM miembro ORDER BY id ASC";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeCi(string $ci): bool {
        $sql = "SELECT COUNT(*) FROM miembro WHERE ci=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$ci]);
        return $stmt->fetchColumn() > 0;
    }

    public function getMiembrosHombres(): array {
        $sql = "SELECT * FROM miembro WHERE sexo='M' ORDER BY id ASC";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMiembrosMujeres(): array {
        $sql = "SELECT * FROM miembro WHERE sexo='F' ORDER BY id ASC";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
