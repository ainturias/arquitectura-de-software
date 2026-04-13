<?php
require_once __DIR__ . '/../datos/DAula.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\Result\ConsoleResult;

class NAula {
    private DAula $datosAula;

    public function __construct() {
        $this->datosAula = new DAula();
    }

    public function crear(string $codigo): bool {
        if ($this->existeCodigo($codigo)) {
            return false; // ya existe el código (UNIQUE)
        }
        
        $this->datosAula->setCodigo($codigo);
        // Creamos el aula inicialmente sin qr_code para obtener el ID
        $nuevoId = $this->datosAula->crear();

        if ($nuevoId) {
            // Ahora que tenemos el ID, creamos el identificador y actualizamos
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

    public function eliminar(int $id_aula): bool {
        $this->datosAula->setId($id_aula);
        return $this->datosAula->eliminar();
    }

    public function listar(): array {
        return $this->datosAula->listar();
    }

    private function existeCodigo(string $codigo): bool {
        return $this->datosAula->existeCodigo($codigo);
    }



    /**
     * Nueva función para generar la imagen del QR como un string Base64.
     */
    public function generarQrCodeImagen(int $id_aula): ?string {
        // Define la URL base de tu aplicación
        $baseUrl = "http://localhost/asistencia/presentacion/PAsistencia.php";
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
 
            // Devolver la imagen como data URI (Base64) para usar en HTML
            return $result->getDataUri();

        } catch (Exception $e) {
      
            error_log($e->getMessage());
            return null;
        }

    }


}
