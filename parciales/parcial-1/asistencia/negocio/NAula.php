<?php
// Capa de Negocio - Aula (genera el QR con la librería endroid)
require_once __DIR__ . '/../datos/DAula.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class NAula {
    private DAula $datosAula;

    public function __construct() {
        $this->datosAula = new DAula();
    }

    public function crear(string $codigo): bool {
        if ($this->datosAula->existeCodigo($codigo)) {
            return false;
        }
        $this->datosAula->setCodigo($codigo);
        $nuevoId = $this->datosAula->crear();
        if ($nuevoId) {
            $qrIdentifier = 'AULA_' . $nuevoId;
            return $this->editar($nuevoId, $codigo, $qrIdentifier);
        }
        return false;
    }

    public function editar(int $id, string $codigo, ?string $qr_code = null): bool {
        $this->datosAula->setId($id);
        $this->datosAula->setCodigo($codigo);
        $this->datosAula->setQrCode($qr_code);
        return $this->datosAula->editar();
    }

    public function eliminar(int $id): bool {
        $this->datosAula->setId($id);
        return $this->datosAula->eliminar();
    }

    public function listar(): array {
        return $this->datosAula->listar();
    }

    public function generarQrCodeImagen(int $id_aula): ?string {
        // URL a la que apunta el QR (el estudiante escanea y llega aquí)
        $baseUrl = "http://localhost:8000/presentacion/PAsistencia.php";
        $urlCompleta = $baseUrl . "?id_aula=" . $id_aula;

        try {
            $builder = new Builder(
                writer: new PngWriter(),
                writerOptions: [],
                validateResult: false,
                data: $urlCompleta,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10
            );

            $result = $builder->build();
            return $result->getDataUri();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}
