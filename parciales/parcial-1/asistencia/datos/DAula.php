<?php
require_once __DIR__ . '/../config/Conexion.php';

class DAula
{
    private ?int $id_aula = null;
    private string $codigo;

    private PDO $con;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    public function setId(int $id): void
    {
        $this->id_aula = $id;
    }
    public function setCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    // Inserta un aula nueva en la base de datos
    public function crear(): bool
    {
        $sql = "INSERT INTO aula (codigo) VALUES (?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->codigo]);
    }

    // Actualiza el código de un aula existente
    public function editar(): bool
    {
        $sql = "UPDATE aula SET codigo=? WHERE id_aula=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->codigo, $this->id_aula]);
        return $stmt->rowCount() > 0;
    }

    // Elimina un aula por su id
    public function eliminar(): bool
    {
        $sql = "DELETE FROM aula WHERE id_aula=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_aula]);
        return $stmt->rowCount() > 0;
    }

    // Devuelve todas las aulas ordenadas por id
    public function listar(): array
    {
        $sql = "SELECT * FROM aula ORDER BY id_aula ASC";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verifica si ya existe un aula con ese código
    public function existeCodigo(string $codigo): bool
    {
        $sql = "SELECT COUNT(*) FROM aula WHERE codigo=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$codigo]);
        return $stmt->fetchColumn() > 0;
    }
}
