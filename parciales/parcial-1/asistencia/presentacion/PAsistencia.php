<?php
// Registro de Asistencia - Capa de Presentación
// Esta es la página a la que llega el ESTUDIANTE cuando escanea el QR
require_once 'VistaBase.php';
require_once '../negocio/NAsistencia.php';

class PAsistencia extends VistaBase {
    private NAsistencia $negocioAsistencia;

    public function __construct() {
        $this->negocioAsistencia = new NAsistencia();
    }

    // Diseño limpio para la vista del estudiante (sin navbar del docente)
    private function renderInicioLimpio(string $titulo): void {
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>$titulo</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { background-color: #f0f2f5; }
            </style>
        </head>
        <body class="d-flex align-items-center py-4">
        HTML;
    }

    private function renderFinLimpio(): void {
        echo <<<HTML
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        HTML;
    }

    // Punto de entrada de la vista
    public function mostrarVista(): void {
        $this->renderInicioLimpio("Registrar Asistencia");
        echo '<main class="container">';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarFormulario();
        } else {
            $this->mostrarFormulario();
        }

        echo '</main>';
        $this->renderFinLimpio();
    }

    // Muestra el formulario donde el estudiante ingresa su registro
    private function mostrarFormulario(): void {
        $id_aula = isset($_GET['id_aula']) && is_numeric($_GET['id_aula']) ? (int)$_GET['id_aula'] : null;

        if (!$id_aula) {
            $this->mostrarMensaje('danger', 'URL Inválida', 'El código QR no proporcionó un ID de aula válido.');
            return;
        }

        // Verificamos si hay una clase activa en esta aula ahora mismo
        $data = $this->negocioAsistencia->obtenerDatosParaFormulario($id_aula);

        if ($data['error']) {
            $this->mostrarMensaje('warning', 'Atención', $data['error']);
            return;
        }

        $clase = $data['datos_clase'];
        ?>
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm" style="border-radius: 12px;">
                    <div class="card-header text-white text-center" style="background: linear-gradient(135deg, #1a237e, #283593); border-radius: 12px 12px 0 0;">
                        <h3 class="my-2">📋 Registrar Asistencia</h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-muted">Aula</label>
                            <p class="fs-5 border-bottom pb-2"><?= htmlspecialchars($clase['codigo_aula']) ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Materia</label>
                            <p class="fs-5 border-bottom pb-2"><?= htmlspecialchars($clase['nombre_materia']) ?></p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted">Hora Actual</label>
                            <p id="reloj-actual" class="fs-5 border-bottom pb-2"><?= htmlspecialchars($clase['hora_actual']) ?></p>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="id_aula" value="<?= htmlspecialchars($id_aula) ?>">
                            <div class="mb-3">
                                <label for="registro" class="form-label fs-5"><strong>Introduce tu Registro</strong></label>
                                <input type="text" name="registro" id="registro" class="form-control form-control-lg" placeholder="Ej: 219012345" required autofocus>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success btn-lg">Registrar Asistencia</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Reloj que se actualiza cada segundo
            const relojElemento = document.getElementById('reloj-actual');
            function actualizarReloj() {
                const horaActual = new Date().toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                relojElemento.textContent = horaActual;
            }
            setInterval(actualizarReloj, 1000);
            actualizarReloj();
        </script>
        <?php
    }

    // Procesa el registro de asistencia del estudiante
    private function procesarFormulario(): void {
        $id_aula = isset($_POST['id_aula']) && is_numeric($_POST['id_aula']) ? (int)$_POST['id_aula'] : null;
        $registro = $_POST['registro'] ?? null;

        if (!$id_aula || !$registro) {
            $this->mostrarMensaje('danger', 'Error', 'Faltan datos para procesar la asistencia.');
            return;
        }

        $mensaje = $this->negocioAsistencia->marcarAsistencia($registro, $id_aula);
        $tipoAlerta = str_contains($mensaje, 'éxito') ? 'success' : 'warning';
        $this->mostrarMensaje($tipoAlerta, 'Resultado', $mensaje);
    }

    // Muestra un mensaje con formato de alerta
    private function mostrarMensaje(string $tipo, string $titulo, string $cuerpo): void {
        echo <<<HTML
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="alert alert-$tipo mt-4">
                    <h4 class="alert-heading">$titulo</h4>
                    <p>$cuerpo</p>
                </div>
                <div class="text-center">
                    <p>Puedes cerrar esta ventana.</p>
                </div>
            </div>
        </div>
        HTML;
    }
}

// Ejecuta la vista
$vista = new PAsistencia();
$vista->mostrarVista();
