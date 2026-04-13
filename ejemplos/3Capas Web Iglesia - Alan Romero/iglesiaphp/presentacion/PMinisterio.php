<?php
require_once 'VistaBase.php';
require_once '../negocio/NMinisterio.php';
require_once '../negocio/NMiembro.php';

class PMinisterio extends VistaBase
{
    private NMinisterio $negocioMinisterio;
    private NMiembro $negocioMiembro;

    public function __construct()
    {
        $this->negocioMinisterio = new NMinisterio();
        $this->negocioMiembro = new NMiembro();
    }

    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
            $descripcion = trim($_POST['descripcion'] ?? '');
            $idMiembro = isset($_POST['id_miembro']) ? (int)$_POST['id_miembro'] : null;
            $fechaUnion = $_POST['fecha_union'] ?? '';

            switch ($accion) {
                case 'crear':
                    $this->crear($descripcion);
                    break;
                case 'editar':
                    if ($id !== null) $this->editar($id, $descripcion);
                    break;
                case 'eliminar':
                    if ($id !== null) $this->eliminar($id);
                    break;
                case 'asignar':
                    if ($id !== null && $idMiembro !== null && $fechaUnion) {
                        $this->asignarMiembro($id, $idMiembro, $fechaUnion);
                    }
                    break;
                case 'quitar':
                    if ($id !== null && $idMiembro !== null) {
                        $this->quitarMiembro($id, $idMiembro);
                    }
                    break;
            }
        }
    }

    private function crear(string $descripcion): void
    {
        echo $this->negocioMinisterio->crear($descripcion)
            ? "<p class='alert alert-success'>Ministerio creado.</p>"
            : "<p class='alert alert-danger'>Error al crear ministerio.</p>";
    }

    private function editar(int $id, string $descripcion): void
    {
        echo $this->negocioMinisterio->editar($id, $descripcion)
            ? "<p class='alert alert-success'>Ministerio editado.</p>"
            : "<p class='alert alert-danger'>Error al editar ministerio.</p>";
    }

    private function eliminar(int $id): void
    {
        echo $this->negocioMinisterio->eliminar($id)
            ? "<p class='alert alert-success'>Ministerio eliminado.</p>"
            : "<p class='alert alert-danger'>Error al eliminar ministerio.</p>";
    }

    private function asignarMiembro(int $idMinisterio, int $idMiembro, string $fechaUnion): void
    {
        echo $this->negocioMinisterio->asignarMiembro($idMinisterio, $idMiembro, $fechaUnion)
            ? "<p class='alert alert-success'>Miembro asignado.</p>"
            : "<p class='alert alert-warning'>Ya está asignado.</p>";
    }

    private function quitarMiembro(int $idMinisterio, int $idMiembro): void
    {
        echo $this->negocioMinisterio->quitarMiembro($idMinisterio, $idMiembro)
            ? "<p class='alert alert-success'>Asignación eliminada.</p>"
            : "<p class='alert alert-danger'>Error al quitar asignación.</p>";
    }

    private function listarMinisterios(): array
    {
        return $this->negocioMinisterio->getMinisterios();
    }

    private function listarMiembros(): array
    {
        return $this->negocioMiembro->getMiembros();
    }

    private function getMiembrosDeMinisterio(int $idMinisterio): array
    {
        return $this->negocioMinisterio->getMiembrosAsignados($idMinisterio);
    }

    public function mostrarVista(): void
    {
        $this->renderInicio("Gestión de Ministerios");
        $ministerios = $this->listarMinisterios();
        $miembros = $this->listarMiembros();

        // Cargar selección si se envió el formulario
        $idSeleccionado = $_POST['id'] ?? '';
        $descripcionSeleccionada = $_POST['descripcion'] ?? '';
?>

        <!-- Formulario de Ministerios -->
        <h2>Gestionar Ministerios</h2>
        <form method="POST" class="mb-3">
            <div class="row g-3">
                <div class="col-md-1"><input name="id" id="inputId" class="form-control" placeholder="ID" value="<?= $idSeleccionado ?>"></div>
                <div class="col-md-6"><input name="descripcion" id="inputDescripcion" class="form-control" placeholder="Descripción" value="<?= $descripcionSeleccionada ?>"></div>
            </div>
            <div class="mt-3">
                <button name="accion" value="crear" class="btn btn-success">Crear</button>
                <button name="accion" value="editar" class="btn btn-warning">Editar</button>
                <button name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
            </div>
        </form>

        <div class="row">
            <!-- Tabla de Ministerios -->
            <div class="col-md-6">
                <h4>Listado de Ministerios</h4>
                <?php if (!empty($ministerios)): ?>
                    <table name="listaMinisterios" class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ministerios as $m): ?>
                                <tr>
                                    <td><?= $m['id'] ?></td>
                                    <td><?= htmlspecialchars($m['descripcion']) ?></td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                            <input type="hidden" name="descripcion" value="<?= htmlspecialchars($m['descripcion']) ?>">
                                            <button name="accion" value="seleccionar" class="btn btn-sm btn-info">Seleccionar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No hay ministerios registrados aún.</p>
                <?php endif; ?>
            </div>

            <!-- Tabla de Miembros asignados -->
            <div class="col-md-6">
                <?php if ($idSeleccionado): ?>
                    <h4>Miembros del Ministerio <?= $descripcionSeleccionada ?></h4>
                    <?php if (!empty($this->getMiembrosDeMinisterio($idSeleccionado))): ?>
                        <table name="listaMiembrosMinisterio" class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Fecha Unión</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->getMiembrosDeMinisterio($idSeleccionado) as $mi): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($mi['nombre']) ?></td>
                                        <td><?= $mi['fecha_union'] ?></td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="id" value="<?= $idSeleccionado ?>">
                                                <input type="hidden" name="id_miembro" value="<?= $mi['id'] ?>">
                                                <button name="accion" value="quitar" class="btn btn-sm btn-danger">Quitar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">No hay miembros en este ministerio aún.</p>
                    <?php endif; ?>

                    <!-- Formulario para asignar nuevo miembro al ministerio -->
                    <h5>Asignar nuevo miembro</h5>
                    <form method="POST" class="row g-2">
                        <input type="hidden" name="id" value="<?= $idSeleccionado ?>">
                        <div class="col-md-6">
                            <select name="id_miembro" class="form-select" required>
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($miembros as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="date" name="fecha_union" class="form-control" title="Fecha de ingreso" required>
                        </div>
                        <div class="col-md-2">
                            <button name="accion" value="asignar" class="btn btn-primary w-100">Asignar</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="text-muted">Seleccione un ministerio para ver sus miembros y asignar nuevos.</p>
                <?php endif; ?>
            </div>
        </div>

<?php
        $this->renderFin();
    }
}

$vista = new PMinisterio();
$vista->procesarFormulario();
$vista->mostrarVista();
