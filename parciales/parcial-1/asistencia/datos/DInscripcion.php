<?php
// Capa de Datos - Inscripción Cabecera (CU Transaccional)
// Maneja la tabla cabecera que agrupa los detalles de inscripción
require_once __DIR__ . '/../config/Conexion.php';

class DInscripcion
{
    private PDO $con;

    private ?int $id_inscripcion = null;
    private ?int $id_grupo = null;
    private ?string $fecha = null;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }

    public function setIdInscripcion(int $id): void
    {
        $this->id_inscripcion = $id;
    }

    public function setIdGrupo(int $id): void
    {
        $this->id_grupo = $id;
    }

    public function setFecha(string $fecha): void
    {
        $this->fecha = $fecha;
    }

    public function getIdInscripcion(): ?int
    {
        return $this->id_inscripcion;
    }

    // Crea la cabecera de inscripción y guarda el ID generado
    public function crearInscripcion(): bool
    {
        $sql = "INSERT INTO inscripcion (id_grupo) VALUES (?) RETURNING id_inscripcion";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$this->id_grupo]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $this->id_inscripcion = (int) $result['id_inscripcion'];
            return true;
        }
        return false;
    }
}
