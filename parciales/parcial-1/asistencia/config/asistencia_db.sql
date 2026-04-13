-- =============================================
-- BASE DE DATOS: asistencia_db
-- Sistema de Control de Asistencia Universitaria
-- Arquitectura: 3 Capas
-- =============================================

-- MATERIA (Caso de Uso Básico)
CREATE TABLE materia (
  id_materia    SERIAL       PRIMARY KEY,
  sigla         VARCHAR(20)  NOT NULL UNIQUE,
  nombre_materia VARCHAR(120) NOT NULL
);

-- AULA (Caso de Uso Básico)
CREATE TABLE aula (
  id_aula  SERIAL      PRIMARY KEY,
  codigo   VARCHAR(40) NOT NULL UNIQUE,
  qr_code  TEXT
);

-- ESTUDIANTE (Caso de Uso Básico)
CREATE TABLE estudiante (
  id_estudiante SERIAL      PRIMARY KEY,
  nombre        VARCHAR(80) NOT NULL,
  apellido      VARCHAR(80) NOT NULL,
  registro      VARCHAR(40) NOT NULL UNIQUE
);

-- GRUPO (Caso de Uso Complejo - FK a materia)
CREATE TABLE grupo (
  id_grupo    SERIAL      PRIMARY KEY,
  id_materia  INT         NOT NULL,
  nombre      VARCHAR(80) NOT NULL,
  CONSTRAINT grupo_unico_por_materia UNIQUE (id_materia, nombre),
  FOREIGN KEY (id_materia) REFERENCES materia(id_materia) ON DELETE RESTRICT
);

-- HORARIO (Caso de Uso Complejo - FK a aula y grupo)
CREATE TABLE horario (
  id_horario  SERIAL   PRIMARY KEY,
  id_aula     INT      NOT NULL,
  id_grupo    INT      NOT NULL,
  dia_semana  SMALLINT NOT NULL,
  hora_inicio TIME     NOT NULL,
  hora_fin    TIME     NOT NULL,
  FOREIGN KEY (id_aula)  REFERENCES aula(id_aula)   ON DELETE RESTRICT,
  FOREIGN KEY (id_grupo) REFERENCES grupo(id_grupo)  ON DELETE CASCADE
);

-- INSCRIPCION (Parte del Caso de Uso Transaccional)
CREATE TABLE inscripcion (
  id_inscripcion    SERIAL PRIMARY KEY,
  id_estudiante     INT    NOT NULL,
  id_grupo          INT    NOT NULL,
  fecha_inscripcion DATE   NOT NULL DEFAULT CURRENT_DATE,
  CONSTRAINT inscripcion_unica UNIQUE (id_estudiante, id_grupo),
  FOREIGN KEY (id_estudiante) REFERENCES estudiante(id_estudiante) ON DELETE CASCADE,
  FOREIGN KEY (id_grupo)      REFERENCES grupo(id_grupo)           ON DELETE CASCADE
);

-- ASISTENCIA (Parte del Caso de Uso Transaccional)
CREATE TABLE asistencia (
  id_asistencia SERIAL      PRIMARY KEY,
  id_estudiante INT         NOT NULL,
  id_horario    INT         NOT NULL,
  fecha_hora    TIMESTAMP   NOT NULL DEFAULT NOW(),
  estado        VARCHAR(15) NOT NULL DEFAULT 'presente',
  FOREIGN KEY (id_estudiante) REFERENCES estudiante(id_estudiante) ON DELETE CASCADE,
  FOREIGN KEY (id_horario)    REFERENCES horario(id_horario)       ON DELETE CASCADE
);

CREATE UNIQUE INDEX uq_asistencia_dia
  ON asistencia (id_estudiante, id_horario, (DATE(fecha_hora)));
