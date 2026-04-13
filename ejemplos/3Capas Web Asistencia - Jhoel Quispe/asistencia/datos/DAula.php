<?php
require_once __DIR__ . '/../config/Conexion.php';

class DAula {
    private ?int $id_aula = null;
    private string $codigo;
    private ?string $qr_code = null;

    private PDO $con;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    // --- Setters ---
    public function setId(int $id): void {
        $this->id_aula = $id;
    }

    public function setCodigo(string $codigo): void {
        $this->codigo = $codigo;
    }

    public function setQrCode(?string $qr_code): void {
        $this->qr_code = $qr_code;
    }

    // En la clase DAula
    public function crear(): int|false {
        $sql = "INSERT INTO aula (codigo, qr_code) VALUES (?, ?)";
        $stmt = $this->con->prepare($sql);
        
        if ($stmt->execute([$this->codigo, $this->qr_code])) {
            return (int)$this->con->lastInsertId(); // Devolvemos el nuevo ID
        }
        return false; // O false si falló
    }

    public function editar(): bool {
        $sql = "UPDATE aula SET codigo=?, qr_code=? WHERE id_aula=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->codigo, $this->qr_code, $this->id_aula]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(): bool {
        $sql = "DELETE FROM aula WHERE id_aula=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_aula]);
        return $stmt->rowCount() > 0;
    }

    public function listar(): array {
        $sql = "SELECT * FROM aula ORDER BY id_aula ASC";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeCodigo(string $codigo): bool {
        $sql = "SELECT COUNT(*) FROM aula WHERE codigo=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$codigo]);
        return $stmt->fetchColumn() > 0;
    }





}
