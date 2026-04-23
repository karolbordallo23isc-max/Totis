Criterios claros
-Registro de usuario
El sistema debe permitir el registro de usuario
Escenario 1: Registro exitoso
•	Dado que el usuario está en la página de registro 
•	Cuando ingresa nombre, correo válido y contraseña válida 
•	Entonces el sistema crea la cuenta 
Escenario 2: Correo inválido
•	Dado que el usuario está en el formulario 
•	Cuando ingresa un correo con formato incorrecto 
•	Entonces el sistema muestra un error indicando “correo inválido” 
•	Y no permite continuar 
Escenario 3: Contraseña inválida
•	Dado que el usuario ingresa una contraseña 
•	Cuando la contraseña tiene menos de 8 caracteres 
•	Entonces el sistema muestra un mensaje de error 
•	Y no permite el registro 
Escenario 4: Usuario ya existente
•	Dado que el correo ya está registrado 
•	Cuando el usuario intenta registrarse con ese correo 
•	Entonces el sistema muestra un mensaje “usuario ya existe” 
Escenario 5: Campos obligatorios
•	Dado que el usuario deja campos vacíos 
•	Cuando intenta enviar el formulario 
•	Entonces el sistema resalta los campos obligatorios 
•	Y no permite el registro


Inicio y cierre de sesión
El sistema debe permitir iniciar y cerrar sesión
Escenario 1: Login exitoso
•	Dado que el usuario está registrado 
•	Y se encuentra en la pantalla de login 
•	Cuando ingresa correo y contraseña correctos 
•	Entonces el sistema autentica al usuario 
Escenario 2: Credenciales incorrectas
•	Dado que el usuario intenta iniciar sesión 
•	Cuando ingresa contraseña o correo incorrecto 
•	Entonces el sistema muestra “credenciales inválidas” 
•	Y no permite el acceso 
Escenario 3: Campos vacíos
•	Dado que el usuario no completa los campos 
•	intenta iniciar sesión 
•	Entonces el sistema muestra errores en los campos obligatorios 
Escenario 4: Sesión expirada
•	Dado que el usuario está inactivo 
•	Cuando pasan 30 minutos sin actividad 
•	Entonces el sistema cierra la sesión automáticamente 
•	Y solicita iniciar sesión nuevamente 

CIERRE DE SESIÓN
Escenario 5: Logout exitoso
•	Dado que el usuario está autenticado 
•	Cuando hace clic en “cerrar sesión” 
•	Entonces el sistema termina la sesión 
•	Y redirige a la pantalla de login 
Escenario 6: Acceso después de logout
•	Dado que el usuario cerró sesión 
•	Cuando intenta acceder a una URL protegida 
•	Entonces el sistema redirige al login 
•	Y no permite acceso sin autenticación


-Acceso a contenido por módulos
El sistema debe permitir acceso a contenido por módulos
Escenario 1: Acceso permitido a módulo
•	Dado que el usuario ha iniciado sesión 
•	Y tiene permiso para el módulo “Reportes” 
•	Cuando accede a ese módulo 
•	Entonces el sistema muestra el contenido correspondiente 
Escenario 2: Acceso denegado
•	Dado que el usuario no tiene permiso para el módulo “Administración” 
•	Cuando intenta acceder (por menú o URL directa) 
•	Entonces el sistema bloquea el acceso 
•	Y muestra un mensaje de “acceso denegado” 
Escenario 3: Ocultar módulos no permitidos
•	Dado que el usuario está autenticado 
•	Cuando visualiza el menú principal 
•	Entonces solo se muestran los módulos a los que tiene acceso 
Escenario 4: Cambio de permisos
•	Dado que un administrador cambia los permisos del usuario 
•	Cuando el usuario vuelve a iniciar sesión 
•	Entonces el sistema actualiza los módulos disponibles según los nuevos permisos 
Escenario 5: Acceso sin sesión
•	Dado que el usuario no ha iniciado sesión 
•	Cuando intenta acceder a un módulo 
•	Entonces el sistema redirige a la pantalla de login 



Ejercicios prácticos
El sistema debe mostrar ejercicios prácticos
Escenario 1: Visualización de ejercicios
•	Dado que el usuario ha iniciado sesión 
•	Y accede a un módulo 
•	Cuando el módulo contiene ejercicios prácticos 
•	Entonces el sistema muestra la lista de ejercicios disponibles 
Escenario 2: Detalle del ejercicio
•	Dado que el usuario visualiza la lista de ejercicios 
•	Cuando selecciona un ejercicio 
•	Entonces el sistema muestra: 
o	enunciado 
o	instrucciones 
o	(opcional) recursos o archivos adjuntos 
Escenario 3: Sin ejercicios disponibles
•	Dado que el módulo no tiene ejercicios 
•	Cuando el usuario accede 
•	Entonces el sistema muestra un mensaje como “No hay ejercicios disponibles” 
Escenario 4: Acceso restringido
•	Dado que el usuario no tiene permiso al módulo 
•	Cuando intenta ver los ejercicios 
•	Entonces el sistema bloquea el acceso 
Escenario 5: Ejercicios por módulo
•	Dado que existen múltiples módulos 
•	Cuando el usuario cambia de módulo 
•	Entonces el sistema muestra los ejercicios correspondientes a ese módulo 

Escenario 6: Tiempo de carga
•	Dado que el usuario solicita los ejercicios 
•	Entonces el sistema los muestra en menos de 2 segundos 


Persistencia de progreso
El sistema debe guardar el progreso del usuario
Escenario 1: Guardado automático de progreso
•	Dado que el usuario está realizando un ejercicio 
•	Cuando completa una acción (ej: responde o avanza) 
•	Entonces el sistema guarda automáticamente su progreso 
Escenario 2: Recuperación de progreso
•	Dado que el usuario ya tiene progreso guardado 
•	Cuando vuelve a iniciar sesión 
•	Entonces el sistema restaura su progreso 
•	Y lo posiciona donde se quedó 
Escenario 3: Visualización del progreso
•	Dado que el usuario accede a un módulo 
•	Cuando tiene avances previos 
•	Entonces el sistema muestra el porcentaje de progreso (ej: 60%) 
Escenario 4: Usuario sin progreso
•	Dado que el usuario no ha iniciado actividades 
•	Cuando accede al módulo 
•	Entonces el sistema muestra progreso en 0% 
Escenario 5: Progreso por usuario
•	Dado que existen múltiples usuarios 
•	Cuando cada uno accede 
•	Entonces el sistema muestra únicamente su propio progreso 
Escenario 6: Persistencia ante cierre inesperado
•	Dado que el usuario está trabajando 
•	Cuando cierra la aplicación o pierde conexión 
•	Entonces el sistema conserva el último progreso guardado
