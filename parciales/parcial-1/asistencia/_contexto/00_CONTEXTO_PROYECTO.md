# 📋 CONTEXTO DEL PROYECTO - Control de Asistencia Universitaria

## Materia
- Arquitectura de Software - Ingeniería en Sistemas
- 1er Parcial - Período 1-2026

## Requisitos del Ingeniero
- ✅ 6 Casos de Uso mínimo
- ✅ Obligatorio: 1 CU Transaccional + 1 CU Complejo
- ✅ Los otros 4 pueden ser Básicos o de cualquier tipo
- ✅ Sin frameworks (PHP puro)
- ✅ Arquitectura: 3 CAPAS (Presentación, Negocio, Datos)
- ✅ Web

## Definiciones del Ingeniero
- **CU Básico**: Tabla sin FK (tabla independiente)
- **CU Complejo**: Tabla con FK de otra tabla
- **CU Transaccional**: Relación de composición entre tablas O relación muchos a muchos

## Mis 6 Casos de Uso
| # | Caso de Uso            | Tipo          | Tabla(s)              |
|---|------------------------|---------------|-----------------------|
| 1 | Registrar Materia      | Básico        | materia               |
| 2 | Registrar Aula         | Básico        | aula                  |
| 3 | Registrar Estudiante   | Básico        | estudiante            |
| 4 | Registrar Grupo        | Complejo      | grupo (FK→materia)    |
| 5 | Registrar Horario      | Complejo      | horario (FK→aula,grupo)|
| 6 | Gestionar Asistencia   | Transaccional | inscripcion + asistencia |

## Tablas de la BD (6 tablas)
1. materia (sin FK)
2. aula (sin FK)
3. estudiante (sin FK)
4. grupo (FK → materia)
5. horario (FK → aula, grupo)
6. inscripcion (FK → estudiante, grupo)
7. asistencia (FK → estudiante, horario)

> NOTA: inscripcion y asistencia se gestionan JUNTAS como un solo CU Transaccional

## Tecnología
- PHP puro + PostgreSQL + Bootstrap 5 (CDN) + endroid/qr-code

## Ruta del proyecto
```
C:\Users\aintu\Desktop\Arqui\1er Parcial\asistencia\
```

## Referencia base
- Proyecto de Jhoel Quispe (3Capas Web Asistencia) en carpeta EJEMPLOS
