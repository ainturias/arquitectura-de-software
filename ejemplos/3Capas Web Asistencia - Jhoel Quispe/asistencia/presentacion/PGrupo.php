<?php
// File: presentacion/PGrupo.php
require_once 'VistaBase.php';
require_once __DIR__ . '/../negocio/NGrupo.php';
require_once __DIR__ . '/../negocio/NMateria.php';


class PGrupo extends VistaBase
{
    private NGrupo $negocioGrupo;
    private NMateria $negocioMateria;

    public function __construct()
    {
        $this->negocioGrupo   = new NGrupo();
        $this->negocioMateria = new NMateria();
    }

    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id     = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;

            $id_materia = isset($_POST['id_materia']) && is_numeric($_POST['id_materia']) ? (int)$_POST['id_materia'] : 0;
            $nombre     = trim($_POST['nombre'] ?? '');

            $id_estudiante     = isset($_POST['id_estudiante']) && is_numeric($_POST['id_estudiante']) ? (int)$_POST['id_estudiante'] : null;
            $fecha_inscripcion = $_POST['fecha_inscripcion'] ?? null;

            switch ($accion) {
                case 'crear':
                    $this->crear($id_materia, $nombre);
                    break;
                case 'editar':
                    if ($id !== null) $this->editar($id, $id_materia, $nombre);
                    break;
                case 'eliminar':
                    if ($id !== null) $this->eliminar($id);
                    break;
                case 'asignar':
                    if ($id !== null && $id_estudiante !== null) {
                        $this->asignarEstudiante($id, $id_estudiante, $fecha_inscripcion ?: null);
                    }
                    break;
                case 'quitar':
                    if ($id !== null && $id_estudiante !== null) {
                        $this->quitarEstudiante($id, $id_estudiante);
                    }
                    break;
                // 'seleccionar' no requiere lógica adicional
            }
        }
    }

    private function crear(int $id_materia, string $nombre): void
    {
        if ($id_materia > 0 && $nombre) {
            echo $this->negocioGrupo->crear($id_materia, $nombre)
                ? "<p class='alert alert-success'>Grupo creado exitosamente.</p>"
                : "<p class='alert alert-danger'>Error al crear grupo. Verifique que no exista otro grupo con el mismo nombre para la materia seleccionada.</p>";
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios.</p>";
        }
    }

    private function editar(int $id, int $id_materia, string $nombre): void
    {
        if ($id_materia > 0 && $nombre) {
            echo $this->negocioGrupo->editar($id, $id_materia, $nombre)
                ? "<p class='alert alert-success'>Grupo editado exitosamente.</p>"
                : "<p class='alert alert-danger'>Error al editar grupo.</p>";
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios para editar.</p>";
        }
    }

    private function eliminar(int $id): void
    {
        echo $this->negocioGrupo->eliminar($id)
            ? "<p class='alert alert-success'>Grupo eliminado exitosamente.</p>"
            : "<p class='alert alert-danger'>Error al eliminar grupo.</p>";
    }

    private function asignarEstudiante(int $id_grupo, int $id_estudiante, ?string $fecha_inscripcion): void
    {
        echo $this->negocioGrupo->asignarEstudiante($id_grupo, $id_estudiante, $fecha_inscripcion)
            ? "<p class='alert alert-success'>Estudiante asignado.</p>"
            : "<p class='alert alert-warning'>El estudiante ya está asignado a este grupo.</p>";
    }

    private function quitarEstudiante(int $id_grupo, int $id_estudiante): void
    {
        echo $this->negocioGrupo->quitarEstudiante($id_grupo, $id_estudiante)
            ? "<p class='alert alert-success'>Asignación eliminada.</p>"
            : "<p class='alert alert-danger'>Error al quitar asignación.</p>";
    }

    // Facades
    private function listarGrupos(): array                 { return $this->negocioGrupo->listar(); }
    private function listarMaterias(): array               { return $this->negocioMateria->listar(); }
    // private function listarEstudiantes(): array            { return $this->negocioEstudiante->listar(); }
    private function listarEstudiantesNoAsignados($id_grupo): array { return $this->negocioGrupo->listarEstudiantesNoAsignados((int)$id_grupo); }
    private function listarEstudiantesDeGrupo(int $id_grupo): array  { return $this->negocioGrupo->listarEstudiantesDeGrupo($id_grupo); }

    public function mostrarVista(): void
    {
        $this->renderInicio("Gestión de Grupos");
        $grupos    = $this->listarGrupos();
        $materias  = $this->listarMaterias();

        // Mantener selección como en el ejemplo
        $idSeleccionado        = $_POST['id'] ?? '';
        $idMateriaSeleccionada = $_POST['id_materia'] ?? '';
        $nombreSeleccionado    = $_POST['nombre'] ?? '';

 

        // Para el combo: si hay grupo seleccionado, listar SOLO los no asignados a ese grupo
        $alumnos = $idSeleccionado
            ? $this->listarEstudiantesNoAsignados((int)$idSeleccionado)
            : []; // Si es falso, $alumnos será un array vacío
        ?>
        <!-- Formulario de Grupos -->
        <h2>Gestionar Grupos</h2>
        <form method="POST" class="mb-4">
            <div class="row g-3">
                <div class="col-md-2">
                    <input name="id" id="inputId" class="form-control" placeholder="ID" value="<?= htmlspecialchars($idSeleccionado) ?>">
                </div>
                <div class="col-md-5">
                    <input name="nombre" id="inputNombre" class="form-control" placeholder="Nombre del grupo (p.ej. SA)" maxlength="80" required value="<?= htmlspecialchars($nombreSeleccionado) ?>">
                </div>
                <div class="col-md-5">
                    <select name="id_materia" id="inputMateria" class="form-select" required>
                        <option value="">Seleccione materia</option>
                        <?php foreach ($materias as $mat):
                            $mid  = htmlspecialchars($mat['id_materia'] ?? $mat['id'] ?? '');
                            $msig = htmlspecialchars($mat['sigla'] ?? '');
                            $mnom = htmlspecialchars($mat['nombre_materia'] ?? '');
                            $sel  = ($idMateriaSeleccionada == $mid) ? 'selected' : '';
                        ?>
                            <option value="<?= $mid ?>" <?= $sel ?>><?= $msig . ' - ' . $mnom ?></option>
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

        <div class="row">
            <!-- Tabla de Grupos -->
            <div class="col-md-6">
                <h4>Listado de Grupos</h4>
                <?php if (!empty($grupos)): ?>
                    <table name="listaGrupos" class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Materia</th>
                                <th>Grupo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($grupos as $g): ?>
                            <tr>
                                <td><?= (int)$g['id'] ?></td>
                                <td><?= htmlspecialchars($g['materia']) ?></td>
                                <td><?= htmlspecialchars($g['nombre']) ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
                                        <input type="hidden" name="id_materia" value="<?= (int)$g['id_materia'] ?>">
                                        <input type="hidden" name="nombre" value="<?= htmlspecialchars($g['nombre']) ?>">
                                        <button name="accion" value="seleccionar" class="btn btn-sm btn-info">Seleccionar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No hay grupos registrados aún.</p>
                <?php endif; ?>
            </div>

            <!-- Estudiantes asignados + Asignación -->
            <div class="col-md-6">
                <?php if ($idSeleccionado): ?>
                    <h4>Estudiantes del Grupo <?= htmlspecialchars($nombreSeleccionado) ?></h4>
                    <?php $asignados = $this->listarEstudiantesDeGrupo((int)$idSeleccionado); ?>
                    <?php if (!empty($asignados)): ?>
                        <table name="listaEstudiantesGrupo" class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Fecha Inscripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($asignados as $es): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($es['nombre']) ?></td>
                                        <td><?= htmlspecialchars($es['correo']) ?></td>
                                        <td><?= htmlspecialchars($es['fecha_inscripcion']) ?></td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="id" value="<?= (int)$idSeleccionado ?>">
                                                <input type="hidden" name="id_estudiante" value="<?= (int)$es['id'] ?>">
                                                <button name="accion" value="quitar" class="btn btn-sm btn-danger">Quitar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">No hay estudiantes en este grupo aún.</p>
                    <?php endif; ?>

                    <h5>Asignar estudiante</h5>
                    <form method="POST" class="row g-2">
                        <input type="hidden" name="id" value="<?= (int)$idSeleccionado ?>">
                        <div class="col-md-7">
                            <select name="id_estudiante" class="form-select" required>
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($alumnos as $u): ?>
                                    <option value="<?= (int)$u['id'] ?>">
                                        <?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['correo']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="fecha_inscripcion" class="form-control" title="Fecha inscripción (opcional)">
                        </div>
                        <div class="col-md-2">
                            <button name="accion" value="asignar" class="btn btn-primary w-100">Asignar</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="text-muted">Seleccione un grupo para ver sus estudiantes y asignar nuevos.</p>
                <?php endif; ?>
            </div>
        </div>

<?php
        $this->renderFin();
    }
}

$vista = new PGrupo();
$vista->procesarFormulario();
$vista->mostrarVista();
