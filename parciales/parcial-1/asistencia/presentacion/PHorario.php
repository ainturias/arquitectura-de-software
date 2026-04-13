<?php
// Gestión de Horarios - Capa de Presentación
require_once 'VistaBase.php';
require_once '../negocio/NHorario.php';
require_once '../negocio/NAula.php';
require_once '../negocio/NGrupo.php';

class PHorario extends VistaBase {
    private NHorario $negocioHorario;
    private NAula $negocioAula;
    private NGrupo $negocioGrupo;

    // Nombres de los días para mostrar en la tabla
    private array $diasSemana = [
        1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
        4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
    ];

    public function __construct() {
        $this->negocioHorario = new NHorario();
        $this->negocioAula = new NAula();
        $this->negocioGrupo = new NGrupo();
    }

    // Procesa las acciones del formulario
    public function procesarFormulario(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
            $id_aula = isset($_POST['id_aula']) && is_numeric($_POST['id_aula']) ? (int)$_POST['id_aula'] : null;
            $id_grupo = isset($_POST['id_grupo']) && is_numeric($_POST['id_grupo']) ? (int)$_POST['id_grupo'] : null;
            $dia = isset($_POST['dia_semana']) && is_numeric($_POST['dia_semana']) ? (int)$_POST['dia_semana'] : null;
            $hora_inicio = $_POST['hora_inicio'] ?? '';
            $hora_fin = $_POST['hora_fin'] ?? '';

            switch ($accion) {
                case 'crear':
                    if ($id_aula && $id_grupo && $dia && $hora_inicio && $hora_fin) {
                        $resultado = $this->negocioHorario->crear($id_aula, $id_grupo, $dia, $hora_inicio, $hora_fin);
                        echo $resultado === 'ok'
                            ? "<p class='alert alert-success'>Horario creado exitosamente.</p>"
                            : "<p class='alert alert-danger'>$resultado</p>";
                    } else {
                        echo "<p class='alert alert-warning'>Todos los campos son obligatorios.</p>";
                    }
                    break;
                case 'editar':
                    if ($id !== null && $id_aula && $id_grupo && $dia && $hora_inicio && $hora_fin) {
                        $resultado = $this->negocioHorario->editar($id, $id_aula, $id_grupo, $dia, $hora_inicio, $hora_fin);
                        echo $resultado === 'ok'
                            ? "<p class='alert alert-success'>Horario editado exitosamente.</p>"
                            : "<p class='alert alert-danger'>$resultado</p>";
                    }
                    break;
                case 'eliminar':
                    if ($id !== null) {
                        echo $this->negocioHorario->eliminar($id)
                            ? "<p class='alert alert-success'>Horario eliminado exitosamente.</p>"
                            : "<p class='alert alert-danger'>Error al eliminar horario.</p>";
                    }
                    break;
            }
        }
    }

    // Muestra la vista completa
    public function mostrarVista(): void {
        $this->renderInicio("Gestión de Horarios");
        $horarios = $this->negocioHorario->listar();
        // Se necesitan aulas y grupos para los selects
        $aulas = $this->negocioAula->listar();
        $grupos = $this->negocioGrupo->listar();
?>
        <h2>Gestionar Horarios</h2>

        <!-- Formulario -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-1">
                            <input name="id" id="inputId" class="form-control" placeholder="ID" readonly>
                        </div>
                        <div class="col-md-2">
                            <select name="id_aula" id="selectAula" class="form-select" required>
                                <option value="">-- Aula --</option>
                                <?php foreach ($aulas as $a): ?>
                                    <option value="<?= $a['id_aula'] ?>"><?= htmlspecialchars($a['codigo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="id_grupo" id="selectGrupo" class="form-select" required>
                                <option value="">-- Grupo --</option>
                                <?php foreach ($grupos as $g): ?>
                                    <option value="<?= $g['id_grupo'] ?>">
                                        <?= htmlspecialchars($g['materia'] . ' / ' . $g['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="dia_semana" id="selectDia" class="form-select" required>
                                <option value="">-- Día --</option>
                                <?php foreach ($this->diasSemana as $num => $nombre): ?>
                                    <option value="<?= $num ?>"><?= $nombre ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="time" name="hora_inicio" id="inputHoraInicio" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <input type="time" name="hora_fin" id="inputHoraFin" class="form-control" required>
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

        <!-- Tabla de horarios -->
        <div class="card">
            <div class="card-header"><strong>Listado de Horarios</strong></div>
            <div class="card-body p-0">
                <?php if (!empty($horarios)): ?>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Aula</th><th>Materia</th><th>Grupo</th><th>Día</th><th>Inicio</th><th>Fin</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($horarios as $h): ?>
                                <tr>
                                    <td><?= $h['id_horario'] ?></td>
                                    <td><?= htmlspecialchars($h['aula_codigo']) ?></td>
                                    <td><?= htmlspecialchars($h['sigla'] . ' - ' . $h['nombre_materia']) ?></td>
                                    <td><?= htmlspecialchars($h['grupo_nombre']) ?></td>
                                    <td><?= $this->diasSemana[$h['dia_semana']] ?? $h['dia_semana'] ?></td>
                                    <td><?= $h['hora_inicio'] ?></td>
                                    <td><?= $h['hora_fin'] ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick='seleccionar(this)'
                                            data-id="<?= $h['id_horario'] ?>"
                                            data-aula="<?= $h['id_aula'] ?>"
                                            data-grupo="<?= $h['id_grupo'] ?>"
                                            data-dia="<?= $h['dia_semana'] ?>"
                                            data-inicio="<?= $h['hora_inicio'] ?>"
                                            data-fin="<?= $h['hora_fin'] ?>">
                                            Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted p-3">No hay horarios registrados.</p>
                <?php endif; ?>
            </div>
        </div>

        <script>
            // Llena el formulario al seleccionar un horario
            function seleccionar(btn) {
                document.getElementById('inputId').value = btn.dataset.id;
                document.getElementById('selectAula').value = btn.dataset.aula;
                document.getElementById('selectGrupo').value = btn.dataset.grupo;
                document.getElementById('selectDia').value = btn.dataset.dia;
                document.getElementById('inputHoraInicio').value = btn.dataset.inicio;
                document.getElementById('inputHoraFin').value = btn.dataset.fin;
            }
        </script>
<?php
        $this->renderFin();
    }
}

// Ejecuta la vista
$vista = new PHorario();
$vista->procesarFormulario();
$vista->mostrarVista();
