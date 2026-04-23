# Especificaciones del Sistema: Criterios de Aceptación

Este documento detalla las reglas de negocio y los escenarios de prueba para las funcionalidades principales del sistema.

---

## 1. Registro de Usuario
*El sistema debe permitir la creación de nuevas cuentas garantizando la integridad de los datos.*

| Escenario | Dado que... | Cuando... | Entonces... |
| :--- | :--- | :--- | :--- |
| **Exitoso** | El usuario está en la página de registro | Ingresa nombre, correo y clave válidos | El sistema crea la cuenta  |
| **Correo Inválido** | El usuario está en el formulario | El formato del correo es incorrecto | Muestra error "Correo inválido"  |
| **Clave Débil** | El usuario ingresa una contraseña | Tiene menos de 8 caracteres | Muestra error y bloquea el registro  |
| **Duplicado** | El correo ya está registrado | Intenta registrarse nuevamente | Muestra mensaje "Usuario ya existe" |

> ###  Detalle de Campos Obligatorios
> ```gherkin
> Scenario: Validación de campos vacíos
>   Given el usuario deja campos vacíos
>   When intenta enviar el formulario
>   Then el sistema resalta los campos obligatorios
>   And no permite el registro
> ```

---

## 2. Gestión de Sesiones (Login/Logout)
*Control de acceso y seguridad de la identidad del usuario.*

### Inicio de Sesión
- [x] **Login Exitoso:** Autenticación correcta con credenciales válidas.
- [ ] **Credenciales Incorrectas:** Bloqueo de acceso con mensaje de error.
- [ ] **Campos Vacíos:** Validación visual de campos requeridos.
- [ ] **Sesión Expirada:** Cierre automático tras **30 minutos** de inactividad.

### Cierre de Sesión
- **Logout:** Al cerrar sesión, se redirige inmediatamente al Login.
- **Protección de URL:** No se permite el acceso a rutas protegidas sin un token activo.

---

## 3. Acceso por Módulos
*Navegación basada en permisos y roles de usuario.*

* **Acceso Permitido:** Solo se visualizan módulos con permisos activos (ej. "Reportes").
* **Acceso Denegado:** Bloqueo por menú y por URL directa con mensaje de "Acceso Denegado".
* **Dinámico:** Si un Admin cambia los permisos, los cambios se reflejan al re-iniciar sesión.

---

## 4. Ejercicios Prácticos
*Interacción con el contenido educativo o de entrenamiento.*

| Requisito | Descripción |
| :--- | :--- |
| **Visualización** | Muestra lista de ejercicios disponibles al entrar al módulo. |
| **Detalle** | Al seleccionar, muestra: Enunciado, Instrucciones y Recursos. |
| **Validación** | Si no hay ejercicios, muestra: *"No hay ejercicios disponibles"*. |
| **Rendimiento** | **Tiempo de carga:** Menos de 2 segundos  |

---
