<?php
// Al inicio de tu script principal o en un archivo de configuración
require_once __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('America/La_Paz');
class VistaBase {
    public function renderInicio($titulo = "Asistencia") {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $titulo ?></title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            
        <nav name="barraNavegacion" class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="../index.php">Asistencia</a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="PMateria.php">Materia</a></li>
                        <li class="nav-item"><a class="nav-link" href="PAula.php">Aula</a></li>
                        <li class="nav-item"><a class="nav-link" href="PEstudiante.php">Estudiante</a></li>
                        <li class="nav-item"><a class="nav-link" href="PGrupo.php">Grupo</a></li>
                        <li class="nav-item"><a class="nav-link" href="PHorario.php">Horario</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container mt-4">
        <?php
    }

    public function renderFin() {
        ?>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }
}   