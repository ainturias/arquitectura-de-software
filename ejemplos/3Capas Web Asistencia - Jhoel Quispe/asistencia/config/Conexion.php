<?php
class Conexion {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = new PDO("pgsql:host=localhost;port=5432;dbname=asistencia_db2", "postgres", "1666");
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection(): PDO {
        return $this->conexion;
    }
}
