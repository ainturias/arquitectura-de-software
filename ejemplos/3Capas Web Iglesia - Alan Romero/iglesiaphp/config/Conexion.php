<?php
class Conexion {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = new PDO("pgsql:host=localhost;port=5432;dbname=iglesiaphp", "postgres", "8554");
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection(): PDO {
        return $this->conexion;
    }
}
