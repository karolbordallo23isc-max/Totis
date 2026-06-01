<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../helpers.php';

/**
 * AdminController — Maneja todas las rutas del panel de administración.
 *
 * Protección: solo usuarios con is_admin = 1 en sesión pueden acceder.
 * Todas las operaciones de escritura validan el token CSRF.
 *
 * Rutas (page=admin&action=...):
 *   (vacío)           → dashboard de admin
 *   modules           → lista de módulos
 *   module_create     → formulario + POST crear módulo
 *   module_edit       → formulario + POST editar módulo
 *   module_delete     → POST eliminar módulo
 *   lessons           → lista de lecciones de un módulo
 *   lesson_create     → formulario + POST crear lección
 *   lesson_edit       → formulario + POST editar lección
 *   lesson_delete     → POST eliminar lección
 *   exercises         → lista de ejercicios de una lección
 *   exercise_create   → formulario + POST crear ejercicio con opciones
 *   exercise_edit     → formulario + POST editar ejercicio con opciones
 *   exercise_delete   → POST eliminar ejercicio
 */
class AdminController {

    /** Verifica que el usuario sea admin; si no, redirige al dashboard. */
    private static function requireAdmin(): void {
        require_auth();
        if (empty($_SESSION['is_admin'])) {
            redirect(base_url('index.php?page=dashboard'));
        }
    }

    /** Despacha la acción correcta según el parámetro GET `action`. */
    public static function dispatch(): void {
        self::requireAdmin();
        $action = $_GET['action'] ?? '';

        match ($action) {
            'modules'         => self::modules(),
            'module_create'   => self::moduleCreate(),
            'module_edit'     => self::moduleEdit(),
            'module_delete'   => self::moduleDelete(),
            'lessons'         => self::lessons(),
            'lesson_create'   => self::lessonCreate(),
            'lesson_edit'     => self::lessonEdit(),
            'lesson_delete'   => self::lessonDelete(),
            'exercises'       => self::exercises(),
            'exercise_create' => self::exerciseCreate(),
            'exercise_edit'   => self::exerciseEdit(),
            'exercise_delete' => self::exerciseDelete(),
            'users'           => self::users(),
            'user_progress'   => self::userProgress(),
            'toggle_admin'    => self::toggleAdmin(),
            'module_stats'    => self::moduleStats(),
            default           => self::dashboard(),
        };
    }

    // ── DASHBOARD ──────────────────────────────────────────────

    private static function dashboard(): void {
        $stats          = Admin::stats();
        $ranking        = Admin::userRanking();
        $statsByModule  = Admin::statsByModule();
        // Cargar ejercicios de todos los módulos de una vez
        $allModules     = Admin::allModules();
        $exercisesByModule = [];
        foreach ($allModules as $m) {
            $mid = (int)$m['id_modulo'];
            $exercisesByModule[$mid] = Admin::statsExercisesByModule($mid);
        }
        require __DIR__ . '/../views/admin/dashboard.php';
    }

    // ── MÓDULOS ────────────────────────────────────────────────

    private static function modules(): void {
        $modules = Admin::allModules();
        require __DIR__ . '/../views/admin/modules.php';
    }

    private static function moduleCreate(): void {
        $courses = Admin::allCourses();
        $nextOrder = Admin::nextModuleOrder();
        $error = $_SESSION['admin_error'] ?? '';
        unset($_SESSION['admin_error']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $nombre      = trim($_POST['nombre']      ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $cursoId     = (int)($_POST['curso_id']   ?? 1);
            $orden       = (int)($_POST['orden']      ?? $nextOrder);

            if ($nombre === '') {
                $_SESSION['admin_error'] = 'El nombre del módulo es obligatorio.';
                redirect(base_url('index.php?page=admin&action=module_create'));
            }
            Admin::createModule($cursoId, $nombre, $descripcion, $orden);
            $_SESSION['admin_ok'] = 'Módulo creado correctamente.';
            redirect(base_url('index.php?page=admin&action=modules'));
        }

        $csrfToken = csrf_token();
        require __DIR__ . '/../views/admin/module_form.php';
    }

    private static function moduleEdit(): void {
        $id = (int)($_GET['id'] ?? 0);
        $module = $id > 0 ? Admin::allModules() : [];
        // Buscar el módulo específico
        $moduleRow = null;
        foreach (Admin::allModules() as $m) {
            if ((int)$m['id_modulo'] === $id) { $moduleRow = $m; break; }
        }
        if (!$moduleRow) redirect(base_url('index.php?page=admin&action=modules'));

        $courses = Admin::allCourses();
        $error = $_SESSION['admin_error'] ?? '';
        unset($_SESSION['admin_error']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $nombre      = trim($_POST['nombre']      ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $orden       = (int)($_POST['orden']      ?? 1);

            if ($nombre === '') {
                $_SESSION['admin_error'] = 'El nombre del módulo es obligatorio.';
                redirect(base_url('index.php?page=admin&action=module_edit&id=' . $id));
            }
            Admin::updateModule($id, $nombre, $descripcion, $orden);
            $_SESSION['admin_ok'] = 'Módulo actualizado correctamente.';
            redirect(base_url('index.php?page=admin&action=modules'));
        }

        $csrfToken = csrf_token();
        require __DIR__ . '/../views/admin/module_form.php';
    }

    private static function moduleDelete(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('index.php?page=admin&action=modules'));
        }
        csrf_verify();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) Admin::deleteModule($id);
        $_SESSION['admin_ok'] = 'Módulo eliminado.';
        redirect(base_url('index.php?page=admin&action=modules'));
    }

    // ── LECCIONES ──────────────────────────────────────────────

    private static function lessons(): void {
        $moduleId = (int)($_GET['module_id'] ?? 0);
        if ($moduleId <= 0) redirect(base_url('index.php?page=admin&action=modules'));
        $lessons = Admin::lessonsByModule($moduleId);
        // Obtener nombre del módulo
        $moduleName = '';
        foreach (Admin::allModules() as $m) {
            if ((int)$m['id_modulo'] === $moduleId) { $moduleName = $m['nombre']; break; }
        }
        require __DIR__ . '/../views/admin/lessons.php';
    }

    private static function lessonCreate(): void {
        $moduleId  = (int)($_GET['module_id'] ?? $_POST['module_id'] ?? 0);
        if ($moduleId <= 0) redirect(base_url('index.php?page=admin&action=modules'));
        $nextOrder = Admin::nextLessonOrder($moduleId);
        $error     = $_SESSION['admin_error'] ?? '';
        unset($_SESSION['admin_error']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $titulo = trim($_POST['titulo'] ?? '');
            $texto  = trim($_POST['texto']  ?? '');
            $tipo   = $_POST['tipo']  ?? 'texto';
            $url    = trim($_POST['url']    ?? '');
            $orden  = (int)($_POST['orden'] ?? $nextOrder);

            if ($titulo === '') {
                $_SESSION['admin_error'] = 'El título es obligatorio.';
                redirect(base_url('index.php?page=admin&action=lesson_create&module_id=' . $moduleId));
            }
            Admin::createLesson($moduleId, $titulo, $texto, $tipo, $url, $orden);
            $_SESSION['admin_ok'] = 'Lección creada correctamente.';
            redirect(base_url('index.php?page=admin&action=lessons&module_id=' . $moduleId));
        }

        $lessonRow = null;
        $csrfToken = csrf_token();
        require __DIR__ . '/../views/admin/lesson_form.php';
    }

    private static function lessonEdit(): void {
        $id       = (int)($_GET['id'] ?? 0);
        $lessonRow = $id > 0 ? Admin::findLesson($id) : false;
        if (!$lessonRow) redirect(base_url('index.php?page=admin&action=modules'));
        $moduleId  = (int)$lessonRow['id_modulo'];
        $nextOrder = (int)$lessonRow['orden'];
        $error     = $_SESSION['admin_error'] ?? '';
        unset($_SESSION['admin_error']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $titulo = trim($_POST['titulo'] ?? '');
            $texto  = trim($_POST['texto']  ?? '');
            $tipo   = $_POST['tipo']  ?? 'texto';
            $url    = trim($_POST['url']    ?? '');
            $orden  = (int)($_POST['orden'] ?? $nextOrder);

            if ($titulo === '') {
                $_SESSION['admin_error'] = 'El título es obligatorio.';
                redirect(base_url('index.php?page=admin&action=lesson_edit&id=' . $id));
            }
            Admin::updateLesson($id, $titulo, $texto, $tipo, $url, $orden);
            $_SESSION['admin_ok'] = 'Lección actualizada correctamente.';
            redirect(base_url('index.php?page=admin&action=lessons&module_id=' . $moduleId));
        }

        $csrfToken = csrf_token();
        require __DIR__ . '/../views/admin/lesson_form.php';
    }

    private static function lessonDelete(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('index.php?page=admin&action=modules'));
        }
        csrf_verify();
        $id       = (int)($_POST['id']        ?? 0);
        $moduleId = (int)($_POST['module_id'] ?? 0);
        if ($id > 0) Admin::deleteLesson($id);
        $_SESSION['admin_ok'] = 'Lección eliminada.';
        redirect(base_url('index.php?page=admin&action=lessons&module_id=' . $moduleId));
    }

    // ── EJERCICIOS ─────────────────────────────────────────────

    private static function exercises(): void {
        $lessonId = (int)($_GET['lesson_id'] ?? 0);
        if ($lessonId <= 0) redirect(base_url('index.php?page=admin&action=modules'));
        $lesson    = Admin::findLesson($lessonId);
        if (!$lesson) redirect(base_url('index.php?page=admin&action=modules'));
        $moduleId  = (int)$lesson['id_modulo'];
        $exercises = Admin::exercisesByLesson($lessonId);
        foreach ($exercises as &$ex) {
            $ex['options'] = Admin::optionsByExercise((int)$ex['id_ejercicio']);
        }
        unset($ex);
        require __DIR__ . '/../views/admin/exercises.php';
    }

    private static function exerciseCreate(): void {
        $lessonId = (int)($_GET['lesson_id'] ?? $_POST['lesson_id'] ?? 0);
        $lesson   = $lessonId > 0 ? Admin::findLesson($lessonId) : false;
        if (!$lesson) redirect(base_url('index.php?page=admin&action=modules'));
        $moduleId = (int)$lesson['id_modulo'];
        $error    = $_SESSION['admin_error'] ?? '';
        unset($_SESSION['admin_error']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $pregunta = trim($_POST['pregunta'] ?? '');
            $retro    = trim($_POST['retroalimentacion'] ?? '');
            $tipo     = $_POST['tipo'] ?? 'opcion_multiple';
            $expected = trim($_POST['expected_output']   ?? '');
            $codeInst = trim($_POST['code_instructions'] ?? '');
            $codeHint = trim($_POST['code_hint']         ?? '');

            if ($pregunta === '') {
                $_SESSION['admin_error'] = 'La pregunta es obligatoria.';
                redirect(base_url('index.php?page=admin&action=exercise_create&lesson_id=' . $lessonId));
            }

            $exId = Admin::createExercise($moduleId, $lessonId, $pregunta, $retro, $tipo, $expected, $codeInst, $codeHint);

            // Guardar opciones (solo para tipos que las usan)
            if ($tipo !== 'codigo') {
                $textos    = $_POST['opt_texto']    ?? [];
                $correctas = $_POST['opt_correcta'] ?? [];
                $retros    = $_POST['opt_retro']    ?? [];
                $options   = [];
                foreach ($textos as $i => $txt) {
                    if (trim($txt) === '') continue;
                    $options[] = [
                        'texto'             => trim($txt),
                        'es_correcta'       => isset($correctas[$i]),
                        'retroalimentacion' => trim($retros[$i] ?? ''),
                    ];
                }
                if (!empty($options)) Admin::replaceOptions($exId, $options);
            }

            $_SESSION['admin_ok'] = 'Ejercicio creado correctamente.';
            redirect(base_url('index.php?page=admin&action=exercises&lesson_id=' . $lessonId));
        }

        $exerciseRow = null;
        $optionRows  = [];
        $csrfToken   = csrf_token();
        require __DIR__ . '/../views/admin/exercise_form.php';
    }

    private static function exerciseEdit(): void {
        $id          = (int)($_GET['id'] ?? 0);
        $exerciseRow = $id > 0 ? Admin::findExercise($id) : false;
        if (!$exerciseRow) redirect(base_url('index.php?page=admin&action=modules'));
        $lessonId = (int)$exerciseRow['id_contenido'];
        $lesson   = Admin::findLesson($lessonId);
        $moduleId = $lesson ? (int)$lesson['id_modulo'] : 0;
        $optionRows = Admin::optionsByExercise($id);
        $error    = $_SESSION['admin_error'] ?? '';
        unset($_SESSION['admin_error']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $pregunta = trim($_POST['pregunta'] ?? '');
            $retro    = trim($_POST['retroalimentacion'] ?? '');
            $tipo     = $_POST['tipo'] ?? 'opcion_multiple';
            $expected = trim($_POST['expected_output']   ?? '');
            $codeInst = trim($_POST['code_instructions'] ?? '');
            $codeHint = trim($_POST['code_hint']         ?? '');

            if ($pregunta === '') {
                $_SESSION['admin_error'] = 'La pregunta es obligatoria.';
                redirect(base_url('index.php?page=admin&action=exercise_edit&id=' . $id));
            }

            Admin::updateExercise($id, $pregunta, $retro, $tipo, $expected, $codeInst, $codeHint);

            if ($tipo !== 'codigo') {
                $textos    = $_POST['opt_texto']    ?? [];
                $correctas = $_POST['opt_correcta'] ?? [];
                $retros    = $_POST['opt_retro']    ?? [];
                $options   = [];
                foreach ($textos as $i => $txt) {
                    if (trim($txt) === '') continue;
                    $options[] = [
                        'texto'             => trim($txt),
                        'es_correcta'       => isset($correctas[$i]),
                        'retroalimentacion' => trim($retros[$i] ?? ''),
                    ];
                }
                Admin::replaceOptions($id, $options);
            }

            $_SESSION['admin_ok'] = 'Ejercicio actualizado correctamente.';
            redirect(base_url('index.php?page=admin&action=exercises&lesson_id=' . $lessonId));
        }

        $csrfToken = csrf_token();
        require __DIR__ . '/../views/admin/exercise_form.php';
    }

    private static function exerciseDelete(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('index.php?page=admin&action=modules'));
        }
        csrf_verify();
        $id       = (int)($_POST['id']        ?? 0);
        $lessonId = (int)($_POST['lesson_id'] ?? 0);
        if ($id > 0) Admin::deleteExercise($id);
        $_SESSION['admin_ok'] = 'Ejercicio eliminado.';
        redirect(base_url('index.php?page=admin&action=exercises&lesson_id=' . $lessonId));
    }

    // ── USUARIOS ───────────────────────────────────────────────

    private static function users(): void {
        $users = Admin::allUsers();
        require __DIR__ . '/../views/admin/users.php';
    }

    private static function userProgress(): void {
        $userId = (int)($_GET['user_id'] ?? 0);
        if ($userId <= 0) redirect(base_url('index.php?page=admin&action=users'));
        $user     = Admin::findUser($userId);
        if (!$user) redirect(base_url('index.php?page=admin&action=users'));
        $progress = Admin::userProgress($userId);
        require __DIR__ . '/../views/admin/user_progress.php';
    }

    private static function moduleStats(): void {
        $moduleId = (int)($_GET['module_id'] ?? 0);
        if ($moduleId <= 0) redirect(base_url('index.php?page=admin'));

        // Buscar el módulo
        $moduleRow = null;
        foreach (Admin::allModules() as $m) {
            if ((int)$m['id_modulo'] === $moduleId) { $moduleRow = $m; break; }
        }
        if (!$moduleRow) redirect(base_url('index.php?page=admin'));

        $exercises = Admin::statsExercisesByModule($moduleId);
        require __DIR__ . '/../views/admin/module_stats.php';
    }

    private static function toggleAdmin(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('index.php?page=admin&action=users'));
        }
        csrf_verify();

        // Solo el superadmin puede cambiar roles
        if (empty($_SESSION['is_superadmin'])) {
            $_SESSION['admin_error'] = 'No tienes permisos para cambiar roles de usuario.';
            redirect(base_url('index.php?page=admin&action=users'));
        }

        $userId = (int)($_POST['user_id'] ?? 0);

        // No puede modificarse a sí mismo
        if ($userId === (int)$_SESSION['user_id']) {
            $_SESSION['admin_error'] = 'No puedes modificar tu propio rol.';
            redirect(base_url('index.php?page=admin&action=users'));
        }

        if ($userId > 0) Admin::toggleAdmin($userId);
        $_SESSION['admin_ok'] = 'Rol de usuario actualizado correctamente.';
        redirect(base_url('index.php?page=admin&action=users'));
    }
}
