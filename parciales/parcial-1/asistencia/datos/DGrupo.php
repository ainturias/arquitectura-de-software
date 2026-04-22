<?php
// Capa de Datos - Grupo (usa FK de materia)
require_once __DIR__ . '/../config/Conexion.php';

class DGrupo
{
    private PDO $con;

    private ?int $id_grupo = null;
    private int $id_materia;
    private string $nombre;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    // Setters
    public function setId(int $id): void
    {
        $this->id_grupo = $id;
    }
    public function setIdMateria(int $id_materia): void
    {
        $this->id_materia = $id_materia;
    }
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    // Funciones CRUD
    public function crear(): bool
    {
        $sql = "INSERT INTO grupo (id_materia, nombre) VALUES (?, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$this->id_materia, $this->nombre]);
    }

    public function editar(): bool
    {
        $sql = "UPDATE grupo SET id_materia=?, nombre=? WHERE id_grupo=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_materia, $this->nombre, $this->id_grupo]);
        return $stmt->rowCount() > 0;
    }

    public function eliminar(): bool
    {
        $sql = "DELETE FROM grupo WHERE id_grupo=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_grupo]);
        return $stmt->rowCount() > 0;
    }

    public function listar(): array
    {
        $sql = "SELECT g.id_grupo, g.id_materia, g.nombre,
                       (m.sigla || ' - ' || m.nombre_materia) AS materia
                FROM grupo g
                JOIN materia m ON m.id_materia = g.id_materia
                ORDER BY m.sigla, g.nombre";
        $stmt = $this->con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeGrupoEnMateria(int $id_materia, string $nombre): bool
    {
        $sql = "SELECT COUNT(*) FROM grupo WHERE id_materia=? AND nombre=?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$id_materia, $nombre]);
        return $stmt->fetchColumn() > 0;
    }
}
