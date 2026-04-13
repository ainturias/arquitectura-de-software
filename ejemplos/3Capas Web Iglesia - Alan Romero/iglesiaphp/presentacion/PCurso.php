<?php
require_once 'VistaBase.php';
require_once '../negocio/NCurso.php';
require_once '../negocio/NMiembro.php';

class PCurso extends VistaBase
{
    private NCurso $negocioCurso;
    private NMiembro $negocioMiembro;

    public function __construct()
    {
        $this->negocioCurso = new NCurso();
        $this->negocioMiembro = new NMiembro();
    }

    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
            $nombre = trim($_POST['nombre'] ?? '');
            $fechaInicio = $_POST['fecha_inicio'] ?? '';
            $fechaFin = $_POST['fecha_fin'] ?? '';
            $idMiembro = isset($_POST['id_miembro']) ? (int)$_POST['id_miembro'] : null;
            $calificacion = isset($_POST['calificacion']) ? floatval($_POST['calificacion']) : null;
            $estado = trim($_POST['estado'] ?? '');

            switch ($accion) {
                case 'crear':
                    $this->crear($nombre, $fechaInicio, $fechaFin);
                    break;
                case 'editar':
                    if ($id !== null) $this->editar($id, $nombre, $fechaInicio, $fechaFin);
                    break;
                case 'eliminar':
                    if ($id !== null) $this->eliminar($id);
                    break;
                case 'asignar':
                    if ($id !== null && $idMiembro !== null) {
                        $this->asignarMiembro($id, $idMiembro);
                    }
                    break;
                case 'quitar':
                    if ($id !== null && $idMiembro !== null) {
                        $this->quitarMiembro($id, $idMiembro);
                    }
                    break;
                case 'calificar':
                    if ($id !== null && $idMiembro !== null && is_numeric($calificacion)) {
                        $this->calificarMiembro($id, $idMiembro, $calificacion);
                    }
                    break;
            }
        }
    }

    private function crear(string $nombre, string $fechaInicio, string $fechaFin): void
    {
        echo $this->negocioCurso->crear($nombre, $fechaInicio, $fechaFin)
            ? "<p class='alert alert-success'>Curso creado.</p>"
            : "<p class='alert alert-danger'>Error al crear curso.</p>";
    }

    private function editar(int $id, string $nombre, string $fechaInicio, string $fechaFin): void
    {
        echo $this->negocioCurso->editar($id, $nombre, $fechaInicio, $fechaFin)
            ? "<p class='alert alert-success'>Curso editado.</p>"
            : "<p class='alert alert-danger'>Error al editar curso.</p>";
    }

    private function eliminar(int $id): void
    {
        echo $this->negocioCurso->eliminar($id)
            ? "<p class='alert alert-success'>Curso eliminado.</p>"
            : "<p class='alert alert-danger'>Error al eliminar curso.</p>";
    }

    private function asignarMiembro(int $idCurso, int $idMiembro): void
    {
        echo $this->negocioCurso->asignarMiembro($idCurso, $idMiembro)
            ? "<p class='alert alert-success'>Miembro inscrito al curso.</p>"
            : "<p class='alert alert-warning'>Ya está inscrito o el curso finalizó.</p>";
    }

    private function quitarMiembro(int $idCurso, int $idMiembro): void
    {
        echo $this->negocioCurso->quitarMiembro($idCurso, $idMiembro)
            ? "<p class='alert alert-success'>Miembro eliminado del curso.</p>"
            : "<p class='alert alert-danger'>No se pudo eliminar.</p>";
    }

    private function calificarMiembro(int $idCurso, int $idMiembro, float $calificacion): void
    {
        echo $this->negocioCurso->calificarMiembro($idCurso, $idMiembro, $calificacion)
            ? "<p class='alert alert-success'>Miembro calificado.</p>"
            : "<p class='alert alert-danger'>Error al calificar.</p>";
    }

    private function listarCursos(): array
    {
        return $this->negocioCurso->getCursos();
    }

    private function listarMiembros(): array
    {
        return $this->negocioMiembro->getMiembros();
    }

    private function getMiembrosDelCurso(int $idCurso): array
    {
        return $this->negocioCurso->getMiembrosAsignados($idCurso);
    }

    public function mostrarVista(): void
    {
        $this->renderInicio("Gestión de Cursos");
        $cursos = $this->listarCursos();
        $miembros = $this->listarMiembros();

        $idSeleccionado = $_POST['id'] ?? '';
        $nombreSeleccionado = $_POST['nombre'] ?? '';
        $fechaInicio = $_POST['fecha_inicio'] ?? '';
        $fechaFin = $_POST['fecha_fin'] ?? '';
?>

        <h2>Gestionar Cursos</h2>
        <!-- Formulario de Cursos -->
        <form method="POST" class="mb-3">
            <div class="row g-3">
                <div class="col-md-1"><input name="id" class="form-control" placeholder="ID" value="<?= $idSeleccionado ?>"></div>
                <div class="col-md-4"><input name="nombre" class="form-control" placeholder="Nombre" value="<?= $nombreSeleccionado ?>"></div>
                <div class="col-md-3"><input type="date" name="fecha_inicio" class="form-control" title="Fecha de inicio del curso" value="<?= $fechaInicio ?>"></div>
                <div class="col-md-3"><input type="date" name="fecha_fin" class="form-control" title="Fecha final del curso" value="<?= $fechaFin ?>"></div>
            </div>
            <div class="mt-3">
                <button name="accion" value="crear" class="btn btn-success">Crear</button>
                <button name="accion" value="editar" class="btn btn-warning">Editar</button>
                <button name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
            </div>
        </form>

        <div class="row">
            <!-- Tabla de Cursos -->
            <div class="col-md-6">
                <h4>Listado de Cursos</h4>
                <?php if (!empty($cursos)): ?>
                    <table name="listaCursos" class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Final</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cursos as $c): ?>
                                <tr>
                                    <td><?= $c['id'] ?></td>
                                    <td><?= htmlspecialchars($c['nombre']) ?></td>
                                    <td><?= $c['fecha_inicio'] ?></td>
                                    <td><?= $c['fecha_fin'] ?></td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <input type="hidden" name="nombre" value="<?= htmlspecialchars($c['nombre']) ?>">
                                            <input type="hidden" name="fecha_inicio" value="<?= $c['fecha_inicio'] ?>">
                                            <input type="hidden" name="fecha_fin" value="<?= $c['fecha_fin'] ?>">
                                            <button name="accion" value="seleccionar" class="btn btn-sm btn-info">Seleccionar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No hay cursos registrados aún.</p>
                <?php endif; ?>
            </div>

            <!-- Miembros del Curso -->
            <div class="col-md-6">
                <?php if ($idSeleccionado): ?>
                    <h4>Miembros del Curso <?= htmlspecialchars($nombreSeleccionado) ?></h4>
                    <?php if (!empty($this->getMiembrosDelCurso($idSeleccionado))): ?>
                        <table name="listaMiembrosCurso" class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Calificación</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->getMiembrosDelCurso($idSeleccionado) as $m): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($m['nombre']) ?></td>
                                        <td><?= $m['calificacion'] ?? '-' ?></td>
                                        <td><?= htmlspecialchars($m['estado'] ?? '-') ?></td>
                                        <td>
                                            <!-- Formulario de Calificación y Quitar -->
                                            <form method="POST" class="d-flex align-items-center gap-2">
                                                <input type="hidden" name="id" value="<?= $idSeleccionado ?>">
                                                <input type="hidden" name="id_miembro" value="<?= $m['id'] ?>">
                                                <input name="calificacion" class="form-control form-control-sm" placeholder="Nota" type="number" step="0.01" min="0" max="100" style="width: 80px;">
                                                <button name="accion" value="calificar" class="btn btn-sm btn-primary">Calificar</button>
                                                <button name="accion" value="quitar" class="btn btn-sm btn-danger">Quitar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">No hay miembros en este curso aún.</p>
                    <?php endif; ?>

                    <!-- Formulario de asignación -->
                    <h5>Asignar miembro</h5>
                    <form method="POST" class="row g-2">
                        <input type="hidden" name="id" value="<?= $idSeleccionado ?>">
                        <div class="col-md-8">
                            <select name="id_miembro" class="form-select" required>
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($miembros as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button name="accion" value="asignar" class="btn btn-primary w-100">Inscribir</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="text-muted">Seleccione un curso para ver sus miembros.</p>
                <?php endif; ?>
            </div>
        </div>

<?php
        $this->renderFin();
    }
}

$vista = new PCurso();
$vista->procesarFormulario();
$vista->mostrarVista();
