<?php
// Gestión de Grupos - Capa de Presentación
require_once 'VistaBase.php';
require_once '../negocio/NGrupo.php';
require_once '../negocio/NMateria.php';

class PGrupo extends VistaBase {
    private NGrupo $negocioGrupo;
    private NMateria $negocioMateria;

    public function __construct() {
        $this->negocioGrupo = new NGrupo();
        $this->negocioMateria = new NMateria();
    }

    // Procesa las acciones del formulario
    public function procesarFormulario(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
            $id_materia = isset($_POST['id_materia']) && is_numeric($_POST['id_materia']) ? (int)$_POST['id_materia'] : null;
            $nombre = trim($_POST['nombre'] ?? '');

            switch ($accion) {
                case 'crear':
                    if ($id_materia && $nombre) {
                        echo $this->negocioGrupo->crear($id_materia, $nombre)
                            ? "<p class='alert alert-success'>Grupo creado exitosamente.</p>"
                            : "<p class='alert alert-danger'>Error: ya existe ese grupo en la materia.</p>";
                    } else {
                        echo "<p class='alert alert-warning'>Todos los campos son obligatorios.</p>";
                    }
                    break;
                case 'editar':
                    if ($id !== null && $id_materia && $nombre) {
                        echo $this->negocioGrupo->editar($id, $id_materia, $nombre)
                            ? "<p class='alert alert-success'>Grupo editado exitosamente.</p>"
                            : "<p class='alert alert-danger'>Error al editar grupo.</p>";
                    }
                    break;
                case 'eliminar':
                    if ($id !== null) {
                        echo $this->negocioGrupo->eliminar($id)
                            ? "<p class='alert alert-success'>Grupo eliminado exitosamente.</p>"
                            : "<p class='alert alert-danger'>Error al eliminar grupo.</p>";
                    }
                    break;
            }
        }
    }

    // Muestra la vista completa
    public function mostrarVista(): void {
        $this->renderInicio("Gestión de Grupos");
        $grupos = $this->negocioGrupo->listar();
        // Se necesitan las materias para el select del formulario
        $materias = $this->negocioMateria->listar();
?>
        <h2>Gestionar Grupos</h2>

        <!-- Formulario -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <input name="id" id="inputId" class="form-control" placeholder="ID" readonly>
                        </div>
                        <div class="col-md-5">
                            <select name="id_materia" id="selectMateria" class="form-select" required>
                                <option value="">-- Seleccionar Materia --</option>
                                <?php foreach ($materias as $m): ?>
                                    <option value="<?= $m['id_materia'] ?>">
                                        <?= htmlspecialchars($m['sigla'] . ' - ' . $m['nombre_materia']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input name="nombre" id="inputNombre" class="form-control" placeholder="Nombre del Grupo (ej: SA, SB)" maxlength="80" required>
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

        <!-- Tabla de grupos -->
        <div class="card">
            <div class="card-header"><strong>Listado de Grupos</strong></div>
            <div class="card-body p-0">
                <?php if (!empty($grupos)): ?>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Materia</th><th>Grupo</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grupos as $g): ?>
                                <tr>
                                    <td><?= htmlspecialchars($g['id_grupo']) ?></td>
                                    <td><?= htmlspecialchars($g['materia']) ?></td>
                                    <td><?= htmlspecialchars($g['nombre']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick='seleccionar(this)'
                                            data-id="<?= $g['id_grupo'] ?>"
                                            data-materia="<?= $g['id_materia'] ?>"
                                            data-nombre="<?= htmlspecialchars($g['nombre']) ?>">
                                            Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted p-3">No hay grupos registrados.</p>
                <?php endif; ?>
            </div>
        </div>

        <script>
            // Llena el formulario al seleccionar un grupo
            function seleccionar(btn) {
                document.getElementById('inputId').value = btn.dataset.id;
                document.getElementById('selectMateria').value = btn.dataset.materia;
                document.getElementById('inputNombre').value = btn.dataset.nombre;
            }
        </script>
<?php
        $this->renderFin();
    }
}

// Ejecuta la vista
$vista = new PGrupo();
$vista->procesarFormulario();
$vista->mostrarVista();
