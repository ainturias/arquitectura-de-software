<?php
// Esta es la página a la que llega el ESTUDIANTE cuando escanea el QR
require_once 'VistaBase.php';
require_once '../negocio/NAsistencia.php';

class PAsistencia extends VistaBase
{
    private NAsistencia $negocioAsistencia;

    // Atributos de estado del formulario
    private ?int $id_horario = null;
    private ?string $registro = null;
    private string $mensaje = '';
    private string $tipoAlerta = '';

    public function __construct()
    {
        $this->negocioAsistencia = new NAsistencia();
    }

    // Enrutador: captura los datos del formulario y ejecuta la acción
    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->id_horario = isset($_POST['id_horario']) && is_numeric($_POST['id_horario']) ? (int) $_POST['id_horario'] : null;
            $this->registro = $_POST['registro'] ?? null;

            $this->registrarAsistencia();
        }
    }

    // Registra la asistencia del estudiante (equivalente al "crear")
    private function registrarAsistencia(): void
    {
        if (!$this->id_horario || !$this->registro) {
            $this->mensaje = 'Faltan datos para procesar la asistencia.';
            $this->tipoAlerta = 'danger';
            return;
        }

        // Delegamos al negocio la validación y el registro
        $this->mensaje = $this->negocioAsistencia->marcarAsistencia($this->registro, $this->id_horario);
        $this->tipoAlerta = str_contains($this->mensaje, 'éxito') ? 'success' : 'warning';
    }

    // Obtiene la información del horario para mostrar en el formulario
    private function obtenerDatosClase(): ?array
    {
        $id_horario = isset($_GET['id_horario']) && is_numeric($_GET['id_horario']) ? (int) $_GET['id_horario'] : null;

        if (!$id_horario) {
            return null;
        }

        $data = $this->negocioAsistencia->obtenerDatosParaFormulario($id_horario);
        if ($data['error']) {
            return null;
        }

        $this->id_horario = $id_horario;
        return $data['datos_clase'];
    }

    // Punto de entrada de la vista
    public function mostrarVista(): void
    {
        $this->renderInicioLimpio("Registrar Asistencia");
        echo '<main class="container">';

        // Si hubo un POST, mostramos el resultado
        if ($this->mensaje) {
            $this->mostrarMensaje($this->tipoAlerta, 'Resultado', $this->mensaje);
        } else {
            // Si es GET, mostramos el formulario
            $clase = $this->obtenerDatosClase();

            if (!$clase) {
                $this->mostrarMensaje('danger', 'URL Inválida', 'El código QR no proporcionó un horario válido.');
            } else {
                $this->mostrarFormulario($clase);
            }
        }

        echo '</main>';
        $this->renderFinLimpio();
    }

    // Muestra el formulario donde el estudiante ingresa su registro
    private function mostrarFormulario(array $clase): void
    {
        ?>
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm" style="border-radius: 12px;">
                    <div class="card-header text-white text-center" style="background: linear-gradient(135deg, #1a237e, #283593); border-radius: 12px 12px 0 0;">
                        <h3 class="my-2">Registrar Asistencia</h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-muted">Materia</label>
                            <p class="fs-5 border-bottom pb-2"><?= htmlspecialchars($clase['nombre_materia']) ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Grupo</label>
                            <p class="fs-5 border-bottom pb-2"><?= htmlspecialchars($clase['grupo_nombre']) ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Aula</label>
                            <p class="fs-5 border-bottom pb-2"><?= htmlspecialchars($clase['codigo_aula']) ?></p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted">Hora Actual</label>
                            <p id="reloj-actual" class="fs-5 border-bottom pb-2"><?= htmlspecialchars($clase['hora_actual']) ?></p>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="id_horario" value="<?= htmlspecialchars($this->id_horario) ?>">
                            <div class="mb-3">
                                <label for="registro" class="form-label fs-5"><strong>Introduce tu Registro</strong></label>
                                <input type="text" name="registro" id="registro" class="form-control form-control-lg" placeholder="Ej: 219012345" autofocus>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success btn-lg">REGISTRAR ASISTENCIA</button>
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

    // Muestra un mensaje con formato de alerta
    private function mostrarMensaje(string $tipo, string $titulo, string $cuerpo): void
    {
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

// Inicialización y ejecución
$vista = new PAsistencia();
$vista->procesarFormulario();
$vista->mostrarVista();
