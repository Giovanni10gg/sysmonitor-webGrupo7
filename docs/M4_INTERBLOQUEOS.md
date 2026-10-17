# M4 - Simulador de Interbloqueos

## Proyecto
SysMonitor Web - Grupo 7

## 1. Descripción

El módulo M4 permite simular la asignación de recursos
a procesos, analizar estados seguros e inseguros,
evaluar solicitudes, detectar ciclos y proponer
acciones de recuperación ante interbloqueos.

Es una simulación académica desarrollada con
Laravel, PHP, JavaScript y SVG.

No bloquea, termina ni modifica procesos reales
del sistema operativo Debian.

## 2. Acceso al módulo

Ruta principal:

GET /deadlocks

Para ejecutar el proyecto desde su directorio:

    php artisan serve

Abrir en el navegador:

    http://127.0.0.1:8000/deadlocks

Es necesario que las dependencias de Laravel y
la configuración del proyecto estén preparadas.

## 3. Datos de entrada

El simulador utiliza las siguientes estructuras:

- Allocation: recursos asignados a cada proceso.
- Maximum: máximo de recursos que puede requerir.
- Available: recursos disponibles en el sistema.
- Need: recursos adicionales que puede necesitar.

La matriz Need se calcula mediante:

    Need = Maximum - Allocation

El formulario permite generar dinámicamente matrices
de procesos y tipos de recursos.

Los valores deben ser enteros no negativos.
La asignación no puede superar el máximo.

## 4. Algoritmo del banquero

Se implementa el algoritmo del banquero para comprobar
si existe una secuencia segura de finalización.

El algoritmo utiliza:

- Work: recursos disponibles durante la simulación.
- Finish: estado de finalización de los procesos.
- Need: recursos adicionales de cada proceso.
- Allocation: recursos que libera cada proceso.

Si todos los procesos pueden finalizar, el estado
es seguro y se muestra una secuencia segura.

Si no existe una secuencia segura completa, el estado
es inseguro.

Un estado inseguro no significa necesariamente
que ya exista un interbloqueo.

El simulador muestra el procedimiento paso a paso.

## 5. Evaluación de solicitudes de recursos

Un proceso puede solicitar recursos adicionales.

Para conceder una solicitud se verifica que:

1. No supere la necesidad del proceso.
2. No supere los recursos disponibles.
3. La asignación provisional mantenga un estado seguro.

Si las condiciones se cumplen, se informa que la
solicitud puede concederse.

Las asignaciones evaluadas son simuladas y no se
guardan permanentemente.

Ruta utilizada:

POST /deadlocks/request

## 6. Grafo de asignación de recursos

El grafo representa:

- Procesos mediante círculos.
- Recursos mediante rectángulos.
- Asignaciones con flechas del recurso al proceso.
- Solicitudes con flechas del proceso al recurso.

Los nodos de recursos muestran sus instancias
disponibles y totales.

El grafo se dibuja con SVG en el navegador.

## 7. Detección de ciclos

Se implementa búsqueda en profundidad (DFS)
sobre un grafo dirigido.

El detector identifica un ciclo y devuelve los
nodos que forman parte de él.

La interfaz resalta los nodos y las conexiones
del ciclo detectado.

Con recursos de una sola instancia, un ciclo
constituye una condición de interbloqueo en
el modelo clásico de asignación.

Con recursos de múltiples instancias, detectar
un ciclo no es suficiente para confirmar
un interbloqueo.

El análisis acepta solicitudes pendientes de
varios procesos simultáneamente.

Ruta utilizada:

POST /deadlocks/cycles

## 8. Recuperación simulada

El servicio de recuperación evalúa la terminación
simulada de procesos participantes en un ciclo.

Para cada alternativa:

- Se simula terminar un proceso.
- Se liberan sus recursos asignados.
- Se analiza si los demás procesos pueden continuar.
- Se registran los pasos y recursos disponibles.

Si una terminación individual permite que los
restantes finalicen, puede recomendarse.

Si no es suficiente, el simulador indica que
podrían requerirse varias acciones.

Las recomendaciones no ejecutan comandos del
sistema operativo.

La preempción de recursos es otra alternativa
teórica de recuperación, pero no se implementa
como una operación real en este módulo.

## 9. Caso de prueba: estado seguro

Procesos: 2
Recursos: 2

Allocation:

    P0: [1, 0]
    P1: [0, 1]

Maximum:

    P0: [2, 1]
    P1: [1, 2]

Available:

    [1, 1]

Need:

    P0: [1, 1]
    P1: [1, 1]

Resultado esperado:

    Estado seguro
    Secuencia: P0 -> P1

## 10. Caso de prueba: interbloqueo

Allocation:

    P0: [1, 0]
    P1: [0, 1]

Maximum:

    P0: [1, 1]
    P1: [1, 1]

Available:

    [0, 0]

Solicitudes pendientes:

    P0: [0, 1]
    P1: [1, 0]

Resultado esperado:

    Estado inseguro
    Ciclo: P0 -> R1 -> P1 -> R0 -> P0

En este ejemplo cada recurso tiene una sola
instancia y ambos procesos esperan recursos
asignados al otro.

## 11. Caso de prueba: recuperación

Utilizar el escenario anterior.

Acción recomendada en la simulación:

    Terminar P0

Recursos liberados:

    [1, 0]

Luego P1 puede continuar y finalizar.

Recursos disponibles finales:

    [1, 1]

No quedan procesos pendientes.

## 12. Pruebas automáticas

Para ejecutar todas las pruebas:

    php artisan test

Las pruebas del módulo comprueban:

- Validación de matrices.
- Cálculo de Need.
- Estados seguros e inseguros.
- Secuencia segura y pasos.
- Solicitudes concedidas y rechazadas.
- Construcción del grafo.
- Detección de ciclos.
- Recuperación simulada.
- Respuestas de las rutas Laravel.

## 13. Archivos principales

Controlador:

    app/Http/Controllers/DeadlockController.php

Servicios:

    app/Services/BankerService.php
    app/Services/ResourceGraphService.php
    app/Services/CycleDetectionService.php
    app/Services/RecoveryService.php

Interfaz:

    resources/views/deadlocks/index.blade.php

Rutas:

    routes/web.php

Pruebas:

    tests/Unit/
    tests/Feature/

## 14. Limitaciones

Este módulo no administra recursos reales del
kernel de Linux.

El detector identifica ciclos estructurales;
no todos los ciclos representan interbloqueos.

La simulación de recuperación utiliza solicitudes
pendientes y liberaciones de recursos.

No se guardan permanentemente los escenarios
introducidos por el usuario.

## 15. Apoyo de inteligencia artificial

Se utilizó ChatGPT como apoyo para explicar
algoritmos, orientar la implementación del módulo
y elaborar casos de prueba y documentación.

El funcionamiento del código se verifica mediante
pruebas automáticas y simulaciones en Debian.
