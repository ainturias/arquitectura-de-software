<?php
// File: presentacion/PMateria.php
require_once 'VistaBase.php';
require_once '../negocio/NMateria.php';

class PMateria extends VistaBase
{
    private NMateria $negocioMateria;

    public function __construct()
    {
        $this->negocioMateria = new NMateria();
    }

    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;

            $sigla = trim($_POST['sigla'] ?? '');
            $nombreMateria = trim($_POST['nombre_materia'] ?? '');
            $nivel = trim($_POST['nivel'] ?? '');

            switch ($accion) {
                case 'crear':
                    $this->crear($sigla, $nombreMateria, $nivel);
                    break;
                case 'editar':
                    if ($id !== null) {
                        $this->editar($id, $sigla, $nombreMateria, $nivel);
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

    private function crear(string $sigla, string $nombre_materia, string $nivel): void
    {
        if ($sigla && $nombre_materia && $nivel) {
            if ($this->negocioMateria->crear($sigla, $nombre_materia, $nivel)) {
                echo "<p class='alert alert-success'>Materia creada exitosamente.</p>";
            } else {
                echo "<p class='alert alert-danger'>Error al crear materia. La <b>sigla</b> ya existe.</p>";
            }
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios.</p>";
        }
    }

    private function editar(int $id, string $sigla, string $nombre_materia, string $nivel): void
    {
        if ($sigla && $nombre_materia && $nivel) {
            if ($this->negocioMateria->editar($id, $sigla, $nombre_materia, $nivel)) {
                echo "<p class='alert alert-success'>Materia editada exitosamente.</p>";
            } else {
                echo "<p class='alert alert-danger'>Error al editar materia.</p>";
            }
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios para editar.</p>";
        }
    }

    private function eliminar(int $id): void
    {
        if ($this->negocioMateria->eliminar($id)) {
            echo "<p class='alert alert-success'>Materia eliminada exitosamente.</p>";
        } else {
            echo "<p class='alert alert-danger'>Error al eliminar materia.</p>";
        }
    }

    private function listarMaterias(): array
    {
        // Mantengo el mismo patrón que en PMiembro (sin buscador aquí)
        return $this->negocioMateria->listar();
    }

    public function mostrarVista(): void
    {
        $this->renderInicio("Gestión de Materias");
        $materias = $this->listarMaterias();
?>
        <h2>Gestionar Materias</h2>
        <form method="POST" class="mb-4">
            <div class="row g-3">
                <div class="col-md-2">
                    <input name="id" id="inputId" class="form-control" placeholder="ID">
                </div>
                <div class="col-md-2">
                    <input name="sigla" id="inputSigla" class="form-control" placeholder="Sigla" maxlength="20" required>
                </div>
                <div class="col-md-5">
                    <input name="nombre_materia" id="inputNombre" class="form-control" placeholder="Nombre de la materia" maxlength="120" required>
                </div>
                <div class="col-md-3">
                    <input name="nivel" id="inputNivel" class="form-control" placeholder="Nivel" maxlength="20" required>
                </div>
            </div>
            <div class="mt-3">
                <button name="accion" value="crear" class="btn btn-success">Crear</button>
                <button name="accion" value="editar" class="btn btn-warning">Editar</button>
                <button name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
            </div>
        </form>

        <h4>Listado de Materias</h4>
        <?php if (!empty($materias)): ?>
            <table name="listaMaterias" class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Sigla</th>
                        <th>Nombre</th>
                        <th>Nivel</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materias as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['id_materia'] ?? $m['id'] ?? '') ?></td>
                            <td><?= htmlspecialchars($m['sigla'] ?? '') ?></td>
                            <td><?= htmlspecialchars($m['nombre_materia'] ?? '') ?></td>
                            <td><?= htmlspecialchars($m['nivel'] ?? '') ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick='seleccionarMateria(this)'
                                    data-id="<?= htmlspecialchars($m['id_materia'] ?? $m['id'] ?? '') ?>"
                                    data-sigla="<?= htmlspecialchars($m['sigla'] ?? '') ?>"
                                    data-nombre="<?= htmlspecialchars($m['nombre_materia'] ?? '') ?>"
                                    data-nivel="<?= htmlspecialchars($m['nivel'] ?? '') ?>">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No hay materias registradas aún.</p>
        <?php endif; ?>

        <script>
            function seleccionarMateria(btn) {
                const fila = btn.dataset;
                document.getElementById('inputId').value = fila.id || '';
                document.getElementById('inputSigla').value = fila.sigla || '';
                document.getElementById('inputNombre').value = fila.nombre || '';
                document.getElementById('inputNivel').value = fila.nivel || '';
            }
        </script>
<?php
        $this->renderFin();
    }
}

$vista = new PMateria();
$vista->procesarFormulario();
$vista->mostrarVista();
