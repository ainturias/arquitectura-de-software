<?php

require_once 'VistaBase.php';
require_once '../negocio/NAula.php';

class PAula extends VistaBase
{
    private NAula $negocioAula;

    /** Estado para el panel derecho */
    private ?string $qrImageData = null;     // data URI del PNG
    private ?array  $aulaSel     = null;     // ['id'=>..., 'codigo'=>..., 'qr_code'=>...]

    public function __construct() {
        $this->negocioAula = new NAula();
    }

    public function procesarFormulario(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $accion = $_POST['accion'] ?? '';
        $id     = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
        $codigo = trim($_POST['codigo'] ?? '');
        $qr     = trim($_POST['qr_code'] ?? '');

        switch ($accion) {
            case 'crear':
                $this->crear($codigo);
                break;

            case 'editar':
                if ($id !== null) {
                    $this->editar($id, $codigo, $qr);
                }
                break;

            case 'eliminar':
                if ($id !== null) {
                    $this->eliminar($id);
                }
                break;

            case 'seleccionar':
                if ($id !== null) {
                    // Guardamos selección para mostrar en el panel derecho
                    $this->aulaSel = ['id'=>$id, 'codigo'=>$codigo, 'qr_code'=>$qr];
                }
                break;

            case 'ver_qr':        // muestra/genera QR en el panel derecho
            case 'generar_qr':    // alias
                if ($id !== null) {
                    $this->aulaSel = ['id'=>$id, 'codigo'=>$codigo, 'qr_code'=>$qr];
                    $this->generar($id);   // <- función tipo crear(...)
                }
                break;
        }
    }

    private function crear(string $codigo): void {
        if ($codigo === '') {
            echo "<p class='alert alert-warning'>El campo Código es obligatorio.</p>";
            return;
        }
        if ($this->negocioAula->crear($codigo)) {
            echo "<p class='alert alert-success'>Aula creada y QR generado exitosamente.</p>";
        } else {
            echo "<p class='alert alert-danger'>Error al crear aula. El código ya existe.</p>";
        }
    }

    private function editar(int $id, string $codigo, ?string $qr_code): void {
        if ($codigo === '') {
            echo "<p class='alert alert-warning'>El campo Código es obligatorio.</p>";
            return;
        }
        if ($this->negocioAula->editar($id, $codigo, $qr_code)) {
            echo "<p class='alert alert-success'>Aula editada exitosamente.</p>";
        } else {
            echo "<p class='alert alert-danger'>Error al editar aula.</p>";
        }
    }

    private function eliminar(int $id): void {
        if ($this->negocioAula->eliminar($id)) {
            echo "<p class='alert alert-success'>Aula eliminada exitosamente.</p>";
        } else {
            echo "<p class='alert alert-danger'>No se pudo eliminar.</p>";
        }
    }

    /** 
     * Genera/obtiene la imagen del QR (data URI) para mostrar al lado derecho.
     * Similar en estilo a crear(...).
     */
    private function generar(int $id_aula): void {
        $img = $this->negocioAula->generarQrCodeImagen($id_aula);
        if ($img) {
            $this->qrImageData = $img;
            // echo "<p class='alert alert-info'>QR generado.</p>";
        } else {
            echo "<p class='alert alert-danger'>No se pudo generar el QR.</p>";
        }
    }

    private function listarAulas(): array {
        return $this->negocioAula->listar();
    }

    public function mostrarVista(): void {
        $this->renderInicio("Gestión de Aulas");
        $aulas = $this->listarAulas();

        // Si se viene de un POST seleccionar/ver_qr ya tenemos $this->aulaSel y/o $this->qrImageData
        $idSel     = $this->aulaSel['id']     ?? '';
        $codigoSel = $this->aulaSel['codigo'] ?? '';
        $qrSel     = $this->aulaSel['qr_code']?? '';
?>
<style>
@media print {
    body * { visibility: hidden; }
    #printable-area, #printable-area * { visibility: visible; }
    #printable-area {
        position: absolute; left: 0; top: 0; width: 100%; height: 100%;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
    }
}
 .btn-eq {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 40px;          /* alto uniforme */
    padding: 0 14px;       /* mismo padding */
    font-weight: 500;
    line-height: 1;        /* evita que crezca por texto/emojis */
  }
  .card-footer .btn-eq { min-width: 140px; }  /* opcional: ancho mínimo */

</style>

<div class="container mt-4">
    <h2>Gestionar Aulas</h2>

    <!-- Formulario superior (crear/editar/eliminar) -->
    <form method="POST" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label for="inputId" class="form-label">ID</label>
                <input name="id" id="inputId" class="form-control" placeholder="ID" value="<?= htmlspecialchars($idSel) ?>" readonly>
            </div>
            <div class="col-md-4">
                <label for="inputCodigo" class="form-label">Número de aula</label>
                <input name="codigo" id="inputCodigo" class="form-control" placeholder="Ej: 690-A" maxlength="40" required value="<?= htmlspecialchars($codigoSel) ?>">
            </div>
            <div class="col-md-4">
                <label for="inputQr" class="form-label">Identificador QR</label>
                <input name="qr_code" id="inputQr" class="form-control" placeholder="(Autogenerado)" value="<?= htmlspecialchars($qrSel) ?>" readonly>
            </div>
        </div>
        <div class="mt-3 d-flex gap-2">
            <button name="accion" value="crear" class="btn btn-success">Crear Nueva Aula</button>
            <button name="accion" value="editar" class="btn btn-warning">Guardar Cambios</button>
            <button name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
        </div>
    </form>

    <div class="row">
        <!-- IZQUIERDA: listado -->
        <div class="col-md-7">
            <h4>Listado de Aulas</h4>
            <table name="listaAulas" class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Número</th>
                        <th>Identificador QR</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($aulas as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['id_aula']) ?></td>
                        <td><?= htmlspecialchars($a['codigo']) ?></td>
                        <td><?= htmlspecialchars($a['qr_code'] ?? '') ?></td>
                        <td class="d-flex gap-1">
                            <!-- Seleccionar (carga la info en el formulario y panel derecho) -->
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= (int)$a['id_aula'] ?>">
                                <input type="hidden" name="codigo" value="<?= htmlspecialchars($a['codigo']) ?>">
                                <input type="hidden" name="qr_code" value="<?= htmlspecialchars($a['qr_code'] ?? '') ?>">
                                <button name="accion" value="seleccionar" class="btn btn-sm btn-info">Seleccionar</button>
                            </form>

                            <!-- Ver QR (genera y muestra en panel derecho) -->
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= (int)$a['id_aula'] ?>">
                                <input type="hidden" name="codigo" value="<?= htmlspecialchars($a['codigo']) ?>">
                                <input type="hidden" name="qr_code" value="<?= htmlspecialchars($a['qr_code'] ?? '') ?>">
                                <button name="accion" value="ver_qr" class="btn btn-sm btn-secondary">Ver QR 📲</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- DERECHA: panel de QR -->
        <div class="col-md-5">
            <h4>QR del Aula</h4>
            <?php if ($this->aulaSel): ?>
                <div class="card">
                    <div class="card-body" id="printable-area">
                        <h5 class="card-title">Aula: <?= htmlspecialchars($codigoSel) ?></h5>

                        <?php if ($this->qrImageData): ?>
                            <img src="<?= $this->qrImageData ?>" class="img-fluid my-3" alt="QR aula <?= htmlspecialchars($codigoSel) ?>" style="max-width: 320px;">
                        <?php else: ?>
                            <p class="text-muted">Pulsa <strong>Ver QR</strong> en la tabla para generar y mostrar el código.</p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer d-flex gap-2">
                    <form method="POST" class="m-0">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($idSel) ?>">
                        <input type="hidden" name="codigo" value="<?= htmlspecialchars($codigoSel) ?>">
                        <input type="hidden" name="qr_code" value="<?= htmlspecialchars($qrSel) ?>">
                        <button name="accion" value="ver_qr" class="btn btn-outline-primary btn-eq">Regenerar QR</button>
                    </form>

                    <?php if ($this->qrImageData): ?>
                        <a class="btn btn-outline-success btn-eq"
                        href="<?= $this->qrImageData ?>"
                        download="QR_Aula_<?= htmlspecialchars($codigoSel) ?>.png">Descargar PNG</a>

                        <button type="button" class="btn btn-primary btn-eq" onclick="window.print();">Imprimir</button>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-success btn-eq" disabled>Descargar PNG</button>
                        <button type="button" class="btn btn-primary btn-eq" disabled>Imprimir</button>
                    <?php endif; ?>
                    </div>

                </div>
            <?php else: ?>
                <p class="text-muted">Selecciona un aula de la lista para ver su QR aquí.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
        $this->renderFin();
    }
}

// --- Ejecución ---
$vista = new PAula();
$vista->procesarFormulario();
$vista->mostrarVista();
