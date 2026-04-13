-- USUARIO
CREATE TABLE usuario (
  id_usuario    SERIAL       PRIMARY KEY,
  nombre        VARCHAR(80)  NOT NULL,
  apellido      VARCHAR(80)  NOT NULL,
  correo        VARCHAR(160) NOT NULL UNIQUE,
  registro      VARCHAR(40),
  tipo_usuario  VARCHAR(20)  NOT NULL,     -- 'estudiante' | 'docente' | 'admin'
  password      TEXT         NOT NULL
);

---

-- MATERIA
CREATE TABLE materia (
  id_materia      SERIAL       PRIMARY KEY,
  sigla           VARCHAR(20)  NOT NULL UNIQUE,
  nombre_materia  VARCHAR(120) NOT NULL,
  nivel           VARCHAR(20)  NOT NULL
);

---

-- GRUPO (N..1 con MATERIA)
CREATE TABLE grupo (
  id_grupo    SERIAL      PRIMARY KEY,
  id_materia  INT         NOT NULL,
  nombre      VARCHAR(80) NOT NULL,
  CONSTRAINT grupo_unico_por_materia UNIQUE (id_materia, nombre),
  FOREIGN KEY (id_materia) REFERENCES materia(id_materia) ON DELETE RESTRICT
);

---

-- AULA
CREATE TABLE aula (
  id_aula SERIAL      PRIMARY KEY,
  codigo  VARCHAR(40) NOT NULL UNIQUE,
  qr_code TEXT
);

---

-- HORARIO (N..1 con AULA y N..1 con GRUPO)
CREATE TABLE horario (
  id_horario  SERIAL   PRIMARY KEY,
  id_aula     INT      NOT NULL,
  id_grupo    INT      NOT NULL,
  dia_semana  SMALLINT NOT NULL,   -- 1..7
  hora_inicio TIME     NOT NULL,
  hora_fin    TIME     NOT NULL,
  FOREIGN KEY (id_aula)  REFERENCES aula(id_aula)   ON DELETE RESTRICT,
  FOREIGN KEY (id_grupo) REFERENCES grupo(id_grupo) ON DELETE CASCADE
);

---

-- INSCRIPCION (Estudiante -> Grupo)
CREATE TABLE inscripcion (
  id_estudiante     INT    NOT NULL,
  id_grupo          INT    NOT NULL,
  fecha_inscripcion DATE   NOT NULL DEFAULT CURRENT_DATE,
  CONSTRAINT inscripcion_unica UNIQUE (id_estudiante, id_grupo),
  PRIMARY KEY (id_estudiante, id_grupo)
  FOREIGN KEY (id_estudiante) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_grupo)      REFERENCES grupo(id_grupo)     ON DELETE CASCADE
);

---

-- ASISTENCIA (Estudiante marca en un horario)
CREATE TABLE asistencia (
  id_asistencia     SERIAL      PRIMARY KEY,
  id_estudiante     INT         NOT NULL,
  id_horario        INT         NOT NULL,
  fecha_hora        TIMESTAMP   NOT NULL DEFAULT NOW(),
  estado_asistencia VARCHAR(15) NOT NULL DEFAULT 'presente', -- presente/ausente/tarde...
  FOREIGN KEY (id_estudiante) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_horario)    REFERENCES horario(id_horario) ON DELETE CASCADE
);


CREATE UNIQUE INDEX uq_asistencia_est_horario_dia
  ON asistencia (id_estudiante, id_horario, (DATE(fecha_hora)));