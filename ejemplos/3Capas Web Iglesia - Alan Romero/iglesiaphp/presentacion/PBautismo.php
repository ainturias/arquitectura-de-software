<?php
require_once 'VistaBase.php';
require_once '../negocio/NBautismo.php';
require_once '../negocio/NMiembro.php';

class PBautismo extends VistaBase
{
    private NBautismo $negocioBautismo;
    private NMiembro $negocioMiembro;

    public function __construct()
    {
        $this->negocioBautismo = new NBautismo();
        $this->negocioMiembro = new NMiembro();
    }

    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
            $fecha = trim($_POST['fecha'] ?? '');
            $lugar = trim($_POST['lugar'] ?? '');
            $idMiembro = isset($_POST['idMiembro']) && is_numeric($_POST['idMiembro']) ? (int)$_POST['idMiembro'] : null;

            switch ($accion) {
                case 'crear':
                    $this->crear($fecha, $lugar, $idMiembro);
                    break;
                case 'editar':
                    if ($id !== null) {
                        $this->editar($id, $fecha, $lugar, $idMiembro);
                    } else {
                        echo "<p class='alert alert-warning'>ID inválido para editar.</p>";
                    }
                    break;
                case 'eliminar':
                    if ($id !== null) {
                        $this->eliminar($id);
                    } else {
                        echo "<p class='alert alert-warning'>ID inválido para eliminar.</p>";
                    }
                    break;
            }
        }
    }

    private function crear(string $fecha, string $lugar, int $idMiembro): void
    {
        if ($fecha && $lugar && $idMiembro) {
            if ($this->negocioBautismo->crear($fecha, $lugar, $idMiembro)) {
                echo "<p class='alert alert-success'>Bautismo registrado correctamente.</p>";
            } else {
                echo "<p class='alert alert-danger'>Error al registrar bautismo. El miembro ya tiene un bautismo registrado</p>";
            }
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios.</p>";
        }
    }

    private function editar(int $id, string $fecha, string $lugar, int $idMiembro): void
    {
        if ($fecha && $lugar && $idMiembro) {
            if ($this->negocioBautismo->editar($id, $fecha, $lugar, $idMiembro)) {
                echo "<p class='alert alert-success'>Bautismo editado correctamente.</p>";
            } else {
                echo "<p class='alert alert-danger'>Error al editar bautismo.</p>";
            }
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios para editar.</p>";
        }
    }

    private function eliminar(int $id): void
    {
        if ($this->negocioBautismo->eliminar($id)) {
            echo "<p class='alert alert-success'>Bautismo eliminado correctamente.</p>";
        } else {
            echo "<p class='alert alert-danger'>Error al eliminar bautismo.</p>";
        }
    }

    private function listarBautismos(): array
    {
        return $this->negocioBautismo->getBautismos();
    }

    private function listarMiembros(): array
    {
        return $this->negocioMiembro->getMiembros();
    }

    public function mostrarVista(): void
    {
        $this->renderInicio("Gestión de Bautismos");
        $bautismos = $this->listarBautismos();
        $miembros = $this->listarMiembros();
?>
        <h2>Gestionar Bautismos</h2>
        <form method="POST" class="mb-4">
            <div class="row g-3">
                <div class="col-md-1">
                    <input name="id" id="inputId" class="form-control" placeholder="ID">
                </div>
                <div class="col-md-3">
                    <input name="fecha" id="inputFecha" type="date" class="form-control" title="Fecha de bautismo" required>
                </div>
                <div class="col-md-4">
                    <input name="lugar" id="inputLugar" class="form-control" placeholder="Lugar" required>
                </div>
                <div class="col-md-4">
                    <select name="idMiembro" id="inputMiembro" class="form-select" required>
                        <option value="">Seleccione un miembro</option>
                        <?php foreach ($miembros as $miembro): ?>
                            <option value="<?= $miembro['id'] ?>">
                                <?= htmlspecialchars($miembro['nombre']) . " (" . $miembro['ci'] . ")" ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button name="accion" value="crear" class="btn btn-success">Crear</button>
                <button name="accion" value="editar" class="btn btn-warning">Editar</button>
                <button name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
            </div>
        </form>

        <h4>Listado de Bautismos</h4>
        <?php if (!empty($bautismos)): ?>
            <table name="listaBaustismos" class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Miembro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bautismos as $b): ?>
                        <tr>
                            <td><?= $b['id'] ?></td>
                            <td><?= $b['fecha'] ?></td>
                            <td><?= htmlspecialchars($b['lugar']) ?></td>
                            <td><?= htmlspecialchars($b['nombre_miembro']) ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick="seleccionarBautismo(this)"
                                    data-id="<?= $b['id'] ?>"
                                    data-fecha="<?= $b['fecha'] ?>"
                                    data-lugar="<?= htmlspecialchars($b['lugar']) ?>"
                                    data-idmiembro="<?= $b['id_miembro'] ?>">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No hay bautismos registrados aún.</p>
        <?php endif; ?>

        <script>
            function seleccionarBautismo(btn) {
                const data = btn.dataset;
                document.getElementById('inputId').value = data.id;
                document.getElementById('inputFecha').value = data.fecha;
                document.getElementById('inputLugar').value = data.lugar;
                document.getElementById('inputMiembro').value = data.idmiembro;
            }
        </script>
<?php
        $this->renderFin();
    }
}

$vista = new PBautismo();
$vista->procesarFormulario();
$vista->mostrarVista();
