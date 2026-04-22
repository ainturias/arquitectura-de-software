<?php
// Carga la librería del QR y configura la zona horaria
require_once __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('America/La_Paz');

// Clase base que todas las vistas heredan para tener el mismo diseño
class VistaBase {

    // Renderiza la parte superior del HTML (navbar + estilos)
    public function renderInicio($titulo = "Asistencia") {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $titulo ?></title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { background-color: #f0f2f5; }
                .navbar { box-shadow: 0 2px 4px rgba(0,0,0,.1); }
                .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.12); border-radius: 8px; }
                .table { border-radius: 8px; overflow: hidden; }
                .btn { border-radius: 6px; }
            </style>
        </head>
        <body>
        <!-- Barra de navegación principal -->
        <nav class="navbar navbar-expand-lg" style="background: linear-gradient(135deg, #1a237e, #283593);">
            <div class="container-fluid">
                <a class="navbar-brand text-white fw-bold" href="../index.php">📋 Asistencia UAGRM</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        style="border-color: rgba(255,255,255,.5);">
                    <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link text-white-50" href="PMateria.php">Materia</a></li>
                        <li class="nav-item"><a class="nav-link text-white-50" href="PAula.php">Aula</a></li>
                        <li class="nav-item"><a class="nav-link text-white-50" href="PEstudiante.php">Estudiante</a></li>
                        <li class="nav-item"><a class="nav-link text-white-50" href="PGrupo.php">Grupo</a></li>
                        <li class="nav-item"><a class="nav-link text-white-50" href="PHorario.php">Horario</a></li>
                        <li class="nav-item"><a class="nav-link text-white-50" href="PInscripcion.php">Inscripción</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container mt-4">
        <?php
    }

    // Renderiza la parte inferior del HTML
    public function renderFin() {
        ?>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }

    //-------------------------------------- VISTA ESTUDIANTE ------------------------------------------------------

    // Renderiza la parte superior del HTML SIN navbar (para Estudiantes)
    public function renderInicioLimpio($titulo = "Asistencia") {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $titulo ?></title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { background-color: #f0f2f5; }
            </style>
        </head>
        <body class="d-flex align-items-center py-4">
        <?php
    }

    // Renderiza la parte inferior del HTML SIN el div container
    public function renderFinLimpio() {
        ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }
}
