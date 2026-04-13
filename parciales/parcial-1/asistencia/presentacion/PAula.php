<?php
// Gestión de Aulas - Capa de Presentación
require_once 'VistaBase.php';
require_once '../negocio/NAula.php';

class PAula extends VistaBase {
    private NAula $negocioAula;

    public function __construct() {
        $this->negocioAula = new NAula();
    }

    // Procesa las acciones del formulario (crear, editar, eliminar)
    public function procesarFormulario(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
            $codigo = trim($_POST['codigo'] ?? '');

            switch ($accion) {
                case 'crear':
                    if ($codigo) {
                        echo $this->negocioAula->crear($codigo)
                            ? "<p class='alert alert-success'>Aula creada exitosamente.</p>"
                            : "<p class='alert alert-danger'>Error: el código ya existe.</p>";
                    } else {
                        echo "<p class='alert alert-warning'>El código es obligatorio.</p>";
                    }
                    break;
                case 'editar':
                    if ($id !== null && $codigo) {
                        echo $this->negocioAula->editar($id, $codigo)
                            ? "<p class='alert alert-success'>Aula editada exitosamente.</p>"
                            : "<p class='alert alert-danger'>Error al editar aula.</p>";
                    }
                    break;
                case 'eliminar':
                    if ($id !== null) {
                        echo $this->negocioAula->eliminar($id)
                            ? "<p class='alert alert-success'>Aula eliminada exitosamente.</p>"
                            : "<p class='alert alert-danger'>Error al eliminar aula.</p>";
                    }
                    break;
            }
        }
    }

    // Muestra toda la vista: formulario + tabla + QR al costado
    public function mostrarVista(): void {
        $this->renderInicio("Gestión de Aulas");
        $aulas = $this->negocioAula->listar();

        // Si se pidió ver el QR de un aula, generamos la imagen
        $qrImagen = null;
        $qrAulaId = null;
        $qrAulaCodigo = '';
        if (isset($_GET['qr']) && is_numeric($_GET['qr'])) {
            $qrAulaId = (int)$_GET['qr'];
            $qrImagen = $this->negocioAula->generarQrCodeImagen($qrAulaId);
            // Buscamos el código del aula para mostrar en el QR
            foreach ($aulas as $a) {
                if ($a['id_aula'] == $qrAulaId) {
                    $qrAulaCodigo = $a['codigo'];
                    break;
                }
            }
        }
?>
        <!-- CSS solo para impresión: oculta todo menos el QR -->
        <style>
            @media print {
                body * { visibility: hidden; }
                #qr-print, #qr-print * { visibility: visible; }
                #qr-print {
                    position: absolute; left: 0; top: 0;
                    width: 100%; text-align: center; padding-top: 40px;
                }
            }
        </style>

        <h2>Gestionar Aulas</h2>

        <!-- Formulario para crear/editar/eliminar aulas -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input name="id" id="inputId" class="form-control" placeholder="ID" readonly>
                        </div>
                        <div class="col-md-9">
                            <input name="codigo" id="inputCodigo" class="form-control" placeholder="Código del Aula (ej: 690-A)" maxlength="40" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button name="accion" value="crear" class="btn btn-success">Crear</button>
                        <button name="accion" value="editar" class="btn btn-warning">Editar</button>
                        <button name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla y QR lado a lado -->
        <div class="row">
            <!-- Listado de aulas (izquierda) -->
            <div class="<?= $qrImagen ? 'col-md-7' : 'col-12' ?>">
                <div class="card">
                    <div class="card-header"><strong>Listado de Aulas</strong></div>
                    <div class="card-body p-0">
                        <?php if (!empty($aulas)): ?>
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr><th>ID</th><th>Código</th><th>Acciones</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($aulas as $a): ?>
                                        <tr class="<?= ($qrAulaId == $a['id_aula']) ? 'table-active' : '' ?>">
                                            <td><?= htmlspecialchars($a['id_aula']) ?></td>
                                            <td><?= htmlspecialchars($a['codigo']) ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick='seleccionar(this)'
                                                    data-id="<?= $a['id_aula'] ?>"
                                                    data-codigo="<?= htmlspecialchars($a['codigo']) ?>">
                                                    Seleccionar
                                                </button>
                                                <a href="PAula.php?qr=<?= $a['id_aula'] ?>" class="btn btn-sm btn-outline-info">Ver QR</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted p-3">No hay aulas registradas.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php // Si se generó un QR, lo mostramos al costado derecho ?>
            <?php if ($qrImagen): ?>
                <div class="col-md-5">
                    <div class="card border-primary shadow-sm" id="qr-print">
                        <div class="card-header text-white" style="background: linear-gradient(135deg, #1a237e, #283593);">
                            <strong>📷 QR - <?= htmlspecialchars($qrAulaCodigo) ?></strong>
                        </div>
                        <div class="card-body text-center bg-white p-4">
                            <img src="<?= $qrImagen ?>" alt="QR Code" class="img-fluid border p-2 bg-light shadow-sm" style="max-width: 220px; border-radius: 10px;">
                            <p class="mt-3 fw-bold mb-1">Aula: <?= htmlspecialchars($qrAulaCodigo) ?></p>
                            <small class="text-muted">Escanea para marcar asistencia</small>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-secondary" onclick="window.print()">🖨️ Imprimir QR</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <script>
            // Llena el formulario con los datos del aula seleccionada
            function seleccionar(btn) {
                document.getElementById('inputId').value = btn.dataset.id;
                document.getElementById('inputCodigo').value = btn.dataset.codigo;
            }
        </script>
<?php
        $this->renderFin();
    }
}

// Ejecuta la vista
$vista = new PAula();
$vista->procesarFormulario();
$vista->mostrarVista();
