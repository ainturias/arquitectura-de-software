<?php
// File: presentacion/PHorario.php
require_once 'VistaBase.php';
require_once '../negocio/NHorario.php';
require_once '../negocio/NGrupo.php';
require_once '../negocio/NAula.php';

class PHorario extends VistaBase
{
    private NHorario $negocioHorario;
    private NGrupo $negocioGrupo;
    private NAula $negocioAula;

    public function __construct()
    {
        $this->negocioHorario = new NHorario();
        $this->negocioGrupo = new NGrupo();
        $this->negocioAula  = new NAula();
    }

    public function procesarFormulario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;

            $id_aula   = isset($_POST['id_aula']) && is_numeric($_POST['id_aula']) ? (int)$_POST['id_aula'] : 0;
            $id_grupo  = isset($_POST['id_grupo']) && is_numeric($_POST['id_grupo']) ? (int)$_POST['id_grupo'] : 0;
            $dia       = isset($_POST['dia_semana']) && is_numeric($_POST['dia_semana']) ? (int)$_POST['dia_semana'] : 0;
            $inicio    = trim($_POST['hora_inicio'] ?? '');
            $fin       = trim($_POST['hora_fin'] ?? '');

            switch ($accion) {
                case 'crear':
                    $this->crear($id_aula, $id_grupo, $dia, $inicio, $fin);
                    break;
                case 'editar':
                    if ($id !== null) {
                        $this->editar($id, $id_aula, $id_grupo, $dia, $inicio, $fin);
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

    private function crear(int $id_aula, int $id_grupo, int $dia_semana, string $hora_inicio, string $hora_fin): void
    {
        if ($id_aula > 0 && $id_grupo > 0 && $dia_semana >= 1 && $dia_semana <= 7 && $hora_inicio && $hora_fin) {
            if ($this->negocioHorario->crear($id_aula, $id_grupo, $dia_semana, $hora_inicio, $hora_fin)) {
                echo "<p class='alert alert-success'>Horario creado exitosamente.</p>";
            } else {
                echo "<p class='alert alert-danger'>Error al crear horario. Revise choques de horario, rangos o datos.</p>";
            }
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios.</p>";
        }
    }

    private function editar(int $id, int $id_aula, int $id_grupo, int $dia_semana, string $hora_inicio, string $hora_fin): void
    {
        if ($id_aula > 0 && $id_grupo > 0 && $dia_semana >= 1 && $dia_semana <= 7 && $hora_inicio && $hora_fin) {
            if ($this->negocioHorario->editar($id, $id_aula, $id_grupo, $dia_semana, $hora_inicio, $hora_fin)) {
                echo "<p class='alert alert-success'>Horario editado exitosamente.</p>";
            } else {
                echo "<p class='alert alert-danger'>Error al editar horario.</p>";
            }
        } else {
            echo "<p class='alert alert-warning'>Todos los campos son obligatorios para editar.</p>";
        }
    }

    private function eliminar(int $id): void
    {
        if ($this->negocioHorario->eliminar($id)) {
            echo "<p class='alert alert-success'>Horario eliminado exitosamente.</p>";
        } else {
            echo "<p class='alert alert-danger'>Error al eliminar horario.</p>";
        }
    }

    private function listarHorarios(): array
    {
        return $this->negocioHorario->listar();
    }

    private function listarAulas(): array
    {
        return $this->negocioAula->listar();
    }

    private function listarGrupos(): array
    {
        return $this->negocioGrupo->listar();
    }

    public function mostrarVista(): void
    {
        $this->renderInicio("Gestión de Horarios");
        $horarios = $this->listarHorarios();
        $aulas    = $this->listarAulas();
        $grupos   = $this->listarGrupos();

        // Mapeo de días 1..7
        $dias = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
?>
        <h2>Gestionar Horarios</h2>
        <form method="POST" class="mb-4">
            <div class="row g-3">
                <div class="col-md-2">
                    <input name="id" id="inputId" class="form-control" placeholder="ID">
                </div>

                <div class="col-md-3">
                    <select name="id_aula" id="inputAula" class="form-select" required>
                        <option value="">Seleccione aula</option>
                        <?php foreach ($aulas as $a): 
                            $aid = htmlspecialchars($a['id_aula'] ?? '');
                            $cod = htmlspecialchars($a['codigo'] ?? '');
                        ?>
                            <option value="<?= $aid ?>"><?= $cod ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <select name="id_grupo" id="inputGrupo" class="form-select" required>
                        <option value="">Seleccione grupo</option>
                        <?php foreach ($grupos as $g):
                            $gid  = htmlspecialchars($g['id_grupo'] ?? $g['id'] ?? '');
                            $gmid = htmlspecialchars($g['id_materia'] ?? '');
                            $gname = htmlspecialchars($g['nombre'] ?? '');
                            $msig  = htmlspecialchars($g['sigla'] ?? '');
                            $mnom  = htmlspecialchars($g['nombre_materia'] ?? '');
                            $label = trim(($msig ? $msig . ' - ' : '') . $gname);
                        ?>
                            <option value="<?= $gid ?>"><?= $label ?: ('Grupo ' . $gid) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="dia_semana" id="inputDia" class="form-select" required>
                        <option value="">Día de semana</option>
                        <?php foreach ($dias as $num => $nom): ?>
                            <option value="<?= $num ?>"><?= $nom ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="time" name="hora_inicio" id="inputInicio" class="form-control" required title="Hora de inicio">
                </div>
                <div class="col-md-3">
                    <input type="time" name="hora_fin" id="inputFin" class="form-control" required title="Hora de fin">
                </div>
            </div>

            <div class="mt-3">
                <button name="accion" value="crear" class="btn btn-success">Crear</button>
                <button name="accion" value="editar" class="btn btn-warning">Editar</button>
                <button name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
            </div>
        </form>

        <h4>Listado de Horarios</h4>
        <?php if (!empty($horarios)): ?>
            <table name="listaHorarios" class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Aula</th>
                        <th>Grupo</th>
                        <th>Materia</th>
                        <th>Día</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($horarios as $h):
                    $hid  = htmlspecialchars($h['id_horario'] ?? $h['id'] ?? '');
                    $aid  = htmlspecialchars($h['id_aula'] ?? '');
                    $gid  = htmlspecialchars($h['id_grupo'] ?? '');
                    $mat  = htmlspecialchars($h['nombre_materia'] ?? '');
                    $diaN = (int)($h['dia_semana'] ?? 0);
                    $dNom = $dias[$diaN] ?? $diaN;

                    $aulaCodigo = htmlspecialchars($h['codigo'] ?? ($h['aula_codigo'] ?? ''));
                    $grupoNombre = htmlspecialchars($h['grupo_nombre'] ?? ($h['nombre'] ?? ''));
                    if (!$grupoNombre && isset($h['sigla'], $h['nombre'])) {
                        $grupoNombre = htmlspecialchars(($h['sigla'] ? $h['sigla'].' - ' : '').$h['nombre']);
                    }

                    $hin  = htmlspecialchars($h['hora_inicio'] ?? '');
                    $hfin = htmlspecialchars($h['hora_fin'] ?? '');
                ?>
                    <tr>
                        <td><?= $hid ?></td>
                        <td><?= $aulaCodigo ?: $aid ?></td>
                        <td><?= $grupoNombre ?: $gid ?></td>
                        <td><?= $mat ?: '-' ?></td>
                        <td><?= $dNom ?></td>
                        <td><?= $hin ?></td>
                        <td><?= $hfin ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary"
                                    onclick="seleccionarHorario(this)"
                                    data-id="<?= $hid ?>"
                                    data-id_aula="<?= $aid ?>"
                                    data-id_grupo="<?= $gid ?>"
                                    data-dia="<?= $diaN ?>"
                                    data-inicio="<?= $hin ?>"
                                    data-fin="<?= $hfin ?>">
                                Seleccionar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No hay horarios registrados aún.</p>
        <?php endif; ?>

        <script>
            function seleccionarHorario(btn) {
                const d = btn.dataset;
                document.getElementById('inputId').value = d.id || '';
                document.getElementById('inputAula').value = d.id_aula || '';
                document.getElementById('inputGrupo').value = d.id_grupo || '';
                document.getElementById('inputDia').value = d.dia || '';
                document.getElementById('inputInicio').value = d.inicio || '';
                document.getElementById('inputFin').value = d.fin || '';
            }
        </script>
<?php
        $this->renderFin();
    }
}

$vista = new PHorario();
$vista->procesarFormulario();
$vista->mostrarVista();
