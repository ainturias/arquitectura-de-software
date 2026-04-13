<?php
// File: presentacion/PEstudiante.php
require_once 'VistaBase.php';
require_once __DIR__ . '/../negocio/NEstudiante.php';

class PEstudiante extends VistaBase
{
    private NEstudiante $negocioEstudiante;

    public function __construct()
    {
        $this->negocioEstudiante = new NEstudiante();
    }

    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $accion = $_POST['accion'] ?? '';


        $id        = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
        $nombre    = trim($_POST['nombre'] ?? '');
        $apellido  = trim($_POST['apellido'] ?? '');
        $correo    = trim($_POST['correo'] ?? '');
        $registro  = trim($_POST['registro'] ?? '');
        $password  = trim($_POST['password'] ?? '');

        // filtros asistencia
        $fecha_ini = $_POST['fecha_ini'] ?? null;
        $fecha_fin = $_POST['fecha_fin'] ?? null;

        switch ($accion) {
            case 'crear':
                    $this->crear($nombre, $apellido, $correo, $registro, $password);
                    break;

            case 'editar':
                     if ($id !== null) {
                        $this->editar($id, $nombre, $apellido, $correo, $registro, $password);
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

            // case 'seleccionar':
            // case 'filtrar_asistencia':
                // la vista leerá $_POST para cargar asistencia
                break;
        }
    }


    private function crear(string $nombre, string $apellido, string $correo, string $registro, string $password): void
    {
        if ($nombre && $apellido && $correo && $password) {
            if ($this->negocioEstudiante->crear($nombre, $apellido, $correo, $registro, $password)) {
                echo "<p class='alert alert-success'>Estudiante creado exitosamente.</p>";
            } else {
                echo "<p class='alert alert-danger'>Error al crear estudiante. El correo ya existe.</p>";
            }
        } else {
            echo "<p class='alert alert-warning'>Todos los campos obligatorios deben ser llenados.</p>";
        }
    }

    private function editar(int $id, string $nombre, string $apellido, string $correo, string $registro, string $password): void
    {
        if ($nombre && $apellido && $correo) {
            if ($this->negocioEstudiante->editar($id, $nombre, $apellido, $correo, $registro, $password)) {
                echo "<p class='alert alert-success'>Estudiante editado exitosamente.</p>";
            } else {
                echo "<p class='alert alert-danger'>Error al editar estudiante.</p>";
            }
        } else {
            echo "<p class='alert alert-warning'>Todos los campos obligatorios deben ser llenados para editar.</p>";
        }
    }

    private function eliminar(int $id): void
    {
        if ($this->negocioEstudiante->eliminar($id)) {
            echo "<p class='alert alert-success'>Estudiante eliminado exitosamente.</p>";
        } else {
            echo "<p class='alert alert-danger'>Error al eliminar estudiante.</p>";
        }
    }



    private function listarEstudiantes(): array
    {
        return $this->negocioEstudiante->listar(); // estudiantes
    }

    private function listarAsistencia(int $id_estudiante, ?string $fini, ?string $ffin): array
    {
        return $this->negocioEstudiante->listarAsistencia($id_estudiante, $fini, $ffin);
    }

    public function mostrarVista(): void
    {
        $this->renderInicio("Gestionar Estudiantes");
        $estudiantes = $this->listarEstudiantes();

        // Mantener selección (como en el ejemplo)
        $idSeleccionado = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
        $nombreSel      = $_POST['nombre']   ?? '';
        $apellidoSel    = $_POST['apellido'] ?? '';
        $correoSel      = $_POST['correo']   ?? '';
        $registroSel    = $_POST['registro'] ?? '';
        $passwordSel    = $_POST['password'] ?? ''; // vacío por seguridad

        $fecha_ini = $_POST['fecha_ini'] ?? '';
        $fecha_fin = $_POST['fecha_fin'] ?? '';

        $asistencia = $idSeleccionado ? $this->listarAsistencia($idSeleccionado, $fecha_ini ?: null, $fecha_fin ?: null) : [];
    ?>
    <h2>Gestionar Estudiantes</h2>
        <!-- Formulario CRUD Estudiantes -->    
        <form method="POST" class="mb-3">
            <div class="row g-3">
                <div class="col-md-2"><input name="id" id="inputId" class="form-control" placeholder="ID" value="<?= htmlspecialchars((string)$idSeleccionado) ?>"></div>
                <div class="col-md-2"><input name="nombre" id="inputNombre" class="form-control" placeholder="Nombre" value="<?= htmlspecialchars($nombreSel) ?>"></div>
                <div class="col-md-2"><input name="apellido" id="inputApellido" class="form-control" placeholder="Apellido" value="<?= htmlspecialchars($apellidoSel) ?>"></div>
                <div class="col-md-3"><input name="correo" id="inputCorreo" class="form-control" placeholder="Correo" value="<?= htmlspecialchars($correoSel) ?>"></div>
                <div class="col-md-2"><input name="registro" id="inputRegistro" class="form-control" placeholder="Registro" value="<?= htmlspecialchars($registroSel) ?>"></div>
                <div class="col-md-1"><input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" value=""></div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button name="accion" value="crear" class="btn btn-success">Crear</button>
                <button name="accion" value="editar" class="btn btn-warning">Editar</button>
                <button name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
            </div>
        </form>

        <h4>Listado de Estudiantes</h4>
        <div class="row">
            <!-- IZQUIERDA: Tabla estudiantes -->
            <div class="col-md-6">
                <?php if (!empty($estudiantes)): ?>
                <table name="listaEstudiantes" class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Correo</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($estudiantes as $e): ?>
                        <tr>
                            <td><?= (int)$e['id_usuario'] ?></td>
                            <td><?= htmlspecialchars($e['nombre']) ?></td>
                            <td><?= htmlspecialchars($e['apellido']) ?></td>
                            <td><?= htmlspecialchars($e['correo']) ?></td>
                            <td><?= htmlspecialchars($e['registro'] ?? '') ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?= (int)$e['id_usuario'] ?>">
                                    <input type="hidden" name="nombre" value="<?= htmlspecialchars($e['nombre']) ?>">
                                    <input type="hidden" name="apellido" value="<?= htmlspecialchars($e['apellido']) ?>">
                                    <input type="hidden" name="correo" value="<?= htmlspecialchars($e['correo']) ?>">
                                    <input type="hidden" name="registro" value="<?= htmlspecialchars($e['registro'] ?? '') ?>">
                                    <button name="accion" value="seleccionar" class="btn btn-sm btn-primary">Seleccionar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p class="text-muted">No hay estudiantes registrados.</p>
                <?php endif; ?>
            </div>

            <!-- DERECHA: Asistencia del estudiante seleccionado -->
            <div class="col-md-6">
                <?php if ($idSeleccionado): ?>
                    <h4>Asistencia de: <?= htmlspecialchars($apellidoSel . ' ' . $nombreSel) ?></h4>

                    <form method="POST" class="row g-2 mb-3">
                        <input type="hidden" name="id" value="<?= (int)$idSeleccionado ?>">
                        <input type="hidden" name="nombre" value="<?= htmlspecialchars($nombreSel) ?>">
                        <input type="hidden" name="apellido" value="<?= htmlspecialchars($apellidoSel) ?>">
                        <input type="hidden" name="correo" value="<?= htmlspecialchars($correoSel) ?>">
                        <input type="hidden" name="registro" value="<?= htmlspecialchars($registroSel) ?>">
                        <div class="col-md-4">
                            <label class="form-label">Desde</label>
                            <input type="date" name="fecha_ini" class="form-control" value="<?= htmlspecialchars($fecha_ini) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($fecha_fin) ?>">
                        </div>
                        <div class="col-md-4 align-self-end">
                            <button name="accion" value="filtrar_asistencia" class="btn btn-secondary w-100">Filtrar</button>
                        </div>
                    </form>

                    <?php if (!empty($asistencia)): ?>
                        <table name="listaAsistencias"  class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Estado</th>
                                    <th>Grupo</th>
                                    <th>Aula</th>
                                    <th>Horario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($asistencia as $a): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($a['fecha']) ?></td>
                                        <td><?= htmlspecialchars($a['hora']) ?></td>
                                        <td><?= htmlspecialchars($a['estado_asistencia']) ?></td>
                                        <td><?= htmlspecialchars($a['grupo_label']) ?></td>
                                        <td><?= htmlspecialchars($a['aula']) ?></td>
                                        <td><?= htmlspecialchars($a['hora_inicio'] . ' - ' . $a['hora_fin']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">Sin registros para el filtro actual.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted">Selecciona un estudiante para ver su asistencia.</p>
                <?php endif; ?>
            </div>
        </div>

<?php
        $this->renderFin();
    }
}

$vista = new PEstudiante();
$vista->procesarFormulario();
$vista->mostrarVista();
