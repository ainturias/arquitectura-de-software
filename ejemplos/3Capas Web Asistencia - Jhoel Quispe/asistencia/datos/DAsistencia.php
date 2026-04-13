<?php
require_once __DIR__ . '/../config/Conexion.php';

class DAsistencia {
    private PDO $con;
    private ?int $id_estudiante = null;
    private ?int $id_horario = null;

    public function __construct() {
        $conexion = new Conexion();
        $this->con = $conexion->getConnection();
    }


        // --- Setters ---
    public function setIdEstudiante(int $id_estudiante): void {
        $this->id_estudiante = $id_estudiante;
    }

    public function setIdHorario(string $id_horario): void {
        $this->id_horario = $id_horario;
    }

    /**
     * Busca la clase activa en un aula y devuelve sus detalles.
     * (Esta función no cambia)
     */
    public function buscarClaseActualPorAula(int $id_aula): ?array {
        $sql = "SELECT 
                    h.id_horario,
                    h.id_grupo,
                    a.codigo as codigo_aula,
                    m.nombre_materia
                FROM horario h
                JOIN aula a ON h.id_aula = a.id_aula
                JOIN grupo g ON h.id_grupo = g.id_grupo
                JOIN materia m ON g.id_materia = m.id_materia
                WHERE h.id_aula = ? 
                  AND h.dia_semana = EXTRACT(ISODOW FROM CURRENT_DATE)
                  AND CURRENT_TIME BETWEEN h.hora_inicio AND h.hora_fin";
        
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$id_aula]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    /**
     * FUNCIÓN MODIFICADA: Busca un estudiante por su número de registro
     * Y verifica que esté inscrito en el grupo de la clase actual.
     * Devuelve el ID del estudiante si todo es correcto.
     */
    public function buscarEstudiantePorRegistroYGrupo(string $registro, int $id_grupo): ?int {
        $sql = "SELECT u.id_usuario
                FROM usuario u
                JOIN inscripcion i ON u.id_usuario = i.id_estudiante
                WHERE u.registro = ? AND i.id_grupo = ?";

        $stmt = $this->con->prepare($sql);
        $stmt->execute([$registro, $id_grupo]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ? (int)$resultado['id_usuario'] : null;
    }

    /**
     * Inserta el registro final de asistencia en la base de datos.
     * (Esta función no cambia)
     */
    public function registrarAsistencia(): bool {
        $sql = "INSERT INTO asistencia (id_estudiante, id_horario) VALUES (?, ?)";
        $stmt = $this->con->prepare($sql);
        

        try {
            return $stmt->execute([$this->id_estudiante, $this->id_horario]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23505') {  // unique_violation
                return false;
            }
            throw $e; // otros errores, sí re-lanzamos
        }
 
    }


 public function listarPorEstudiante(int $id_estudiante, ?string $fecha_ini, ?string $fecha_fin): array
    {
        $params = [$id_estudiante];
        $filtro = "";

        if (!empty($fecha_ini)) {
            $filtro .= " AND DATE(a.fecha_hora) >= ? ";
            $params[] = $fecha_ini;
        }
        if (!empty($fecha_fin)) {
            $filtro .= " AND DATE(a.fecha_hora) <= ? ";
            $params[] = $fecha_fin;
        }

        $sql = "SELECT 
                    a.id_asistencia,
                    to_char(a.fecha_hora::date, 'YYYY-MM-DD') AS fecha,
                    to_char(a.fecha_hora::time, 'HH24:MI')    AS hora,
                    a.estado_asistencia,
                    (m.sigla || ' - ' || m.nombre_materia || ' / ' || g.nombre) AS grupo_label,
                    au.codigo AS aula,
                    to_char(h.hora_inicio, 'HH24:MI') AS hora_inicio,
                    to_char(h.hora_fin, 'HH24:MI')    AS hora_fin
                FROM asistencia a
                JOIN horario h ON h.id_horario = a.id_horario
                JOIN grupo   g ON g.id_grupo   = h.id_grupo
                JOIN materia m ON m.id_materia = g.id_materia
                JOIN aula    au ON au.id_aula  = h.id_aula
                WHERE a.id_estudiante = ?
                $filtro
                ORDER BY a.fecha_hora DESC";

        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }








}