# Análisis de Impacto de la Mejora - Sprint 5

##  Propósito del Documento
El propósito de este documento es para prever, medir y documentar las repercusiones técnicas y operativas que conllevan las modificaciones solicitadas durante el proceso de revisión externa. Mediante este análisis, el equipo busca identificar de manera anticipada las dependencias de código, previendo qué pantallas o funciones actuales podrían verse afectadas por los nuevos cambios. Este entregable sirve como una guía de planeación y prevención de riesgos para el equipo, asegurando que las observaciones del docente se integren de forma ordenada, protegiendo el trabajo que ya funciona en el sistema y evitando modificaciones improvisadas de último momento.
---

##  Cuestionario de Evaluación de Impacto


1. **¿Qué parte del sistema se modificará?**
   Se modificará la pantalla de inicio actual y se creará un nuevo módulo/plantilla exclusivo para la administración del sistema.

2. **¿Qué requisito se fortalece o ajusta?**
   
Se ajustan y expanden los requisitos **RF-3** (Acceso a contenido por módulos), **RF-4** (Ejercicios prácticos), **RF-5** (Persistencia de progreso) y se robustece el **RNF-4** (Seguridad básica).
3. **¿Qué pantalla se verá afectada?**
   La pantalla de inicio  y se añadirá la nueva vista del Panel de Administración.

4. **¿Qué lógica o proceso se ajustará?**
   El proceso de autenticación para validar los roles de usuario  y la lógica de renderizado del inicio para segmentar las lecciones por asignaturas de la carrera.
5. **¿La base de datos requiere cambio?**
   Sí, se requiere evaluar la estructura de los usuarios para incluir un atributo de "rol" y verificar si las tablas de las lecciones permiten modificaciones dinámicas desde el backend.

6. **¿Se necesita agregar, modificar o consultar información?**
   Se necesita consultar el progreso global de los usuarios en la base de datos y permitir la modificación de los registros de las lecciones.

7. **¿Qué riesgo técnico existe?**
   Pruebas de penetración básica/roles (intentar entrar al panel sin iniciar sesión), pruebas de actualización de contenido y verificación visual de la organización por materias.

8. **¿Qué pruebas deberá realizar QA?**
   Pruebas de penetración básica/roles (intentar entrar al panel sin iniciar sesión), pruebas de actualización de contenido y verificación visual de la organización por materias.
   

9. **¿Qué puede romperse si el cambio se implementa mal?**
   Podría inhabilitarse el inicio de sesión global (**RF-2**) o bloquearse el acceso de los usuarios a los módulos de aprendizaje existentes (**RF-3**).

10. **¿Cómo se comprobará que la mejora sí quedó implementada?**
    Iniciando sesión con una cuenta de administrador, modificando una lección de prueba desde el panel y confirmando que los cambios se reflejen de inmediato en la vista del usuario común.

---

## Tabla de Impacto de la Mejora


| Área afectada | Impacto identificado | Acción requerida |
| :--- | :--- | :--- |
| **Requisitos** | Expansión del alcance de la gestión de módulos y control de usuarios (RF-3, RF-5, RNF-4). | Actualizar la matriz de trazabilidad de requisitos del proyecto. |
| **Interfaz** | Creación del panel administrativo y reestructuración del menú de inicio por asignaturas. | Diseñar los prototipos de la nueva interfaz manteniendo la coherencia visual del sistema. |
| **Lógica** | Ajuste en los permisos del sistema para identificar si quien entra es un usuario común o un administrador. |Programar las condiciones que permiten o bloquean el acceso a las pantallas según el tipo de usuario. |
| **Base de datos** | Actualización de esquemas para soportar roles y permisos de edición. | Modificar la colección o tablas en el gestor de bases de datos para incluir los nuevos campos. |
| **Pruebas** | Necesidad de validar la seguridad de las rutas y la persistencia de datos modificados. | Diseñar el plan de pruebas de QA enfocado en control de accesos y consistencia de datos. |
| **Documentación** | Modificación del flujo de navegación general del sistema. | Actualizar el mapa del sitio y registrar los cambios técnicos dentro de este documento. |