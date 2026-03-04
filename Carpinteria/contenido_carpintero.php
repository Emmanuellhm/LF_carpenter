<?php
// Contenido dinámico para el panel del carpintero
// Se espera que `session_start()` ya haya sido llamado y que exista `$_SESSION['user_id']` y `$_SESSION['role']=='carpenter'`.
include 'db_conexion.php';

$user_id = $_SESSION['user_id'];
$msg = '';

// Manejo de POST para diferentes acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $city = trim($_POST['city'] ?? '');

      // Guardar nombre previo para buscar la fila en carpenters
      $old_name = $_SESSION['user_name'] ?? '';

      $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, city = ? WHERE user_id = ?");
      $stmt->bind_param('ssssi', $full_name, $email, $phone, $city, $user_id);
      if ($stmt->execute()) {
        // Actualizar sesión
        $_SESSION['user_name'] = $full_name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_phone'] = $phone;
        $_SESSION['user_city'] = $city;

        // Sincronizar con tabla carpenters: buscar por nombre previo
        if (!empty($old_name)) {
          $sc = $conn->prepare("SELECT carpenter_id, description FROM carpenters WHERE carpenter_name = ? LIMIT 1");
          $sc->bind_param('s', $old_name);
          $sc->execute();
          $rc = $sc->get_result();
          if ($rc && $rc->num_rows > 0) {
            $rowc = $rc->fetch_assoc();
            $carp_id = $rowc['carpenter_id'];
            $desc = $rowc['description'] ?? '';

            // Helper: reemplaza o añade una clave en description
            $set_desc_kv = function($d, $key, $val) {
              if (preg_match('/' . preg_quote($key, '/') . ':\\s*([^|]+)/i', $d)) {
                return preg_replace('/' . preg_quote($key, '/') . ':\\s*([^|]+)/i', $key . ': ' . $val, $d);
              } else {
                if (strlen(trim($d)) > 0) return $d . ' | ' . $key . ': ' . $val;
                return $key . ': ' . $val;
              }
            };

            $new_desc = $desc;
            $new_desc = $set_desc_kv($new_desc, 'Email', $email);
            $new_desc = $set_desc_kv($new_desc, 'Tel', $phone);
            $new_desc = $set_desc_kv($new_desc, 'Ciudad', $city);

            // Actualizar nombre y descripción
            $upc = $conn->prepare("UPDATE carpenters SET carpenter_name = ?, description = ? WHERE carpenter_id = ?");
            $upc->bind_param('ssi', $full_name, $new_desc, $carp_id);
            $upc->execute();
            $upc->close();
          } else {
            // Si no existe fila, crear una mínima vinculada
            $insc = $conn->prepare("INSERT INTO carpenters (carpenter_name, specialties, experience_years, description, is_verified, approved, created_at) VALUES (?, '', 0, ?, 0, 1, NOW())");
            $desc_new = 'Email: ' . $email . ' | Tel: ' . $phone . ' | Ciudad: ' . $city;
            $insc->bind_param('ss', $full_name, $desc_new);
            $insc->execute();
            $insc->close();
          }
          $sc->close();
        }

        $msg = 'Perfil actualizado correctamente.';
      } else {
        $msg = 'Error al actualizar perfil: ' . $conn->error;
      }
      $stmt->close();

      header('Location: carpintero.php');
      exit;
    }

    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        // Verificar contraseña actual
        $stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ? LIMIT 1");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if (password_verify($current, $row['password_hash'])) {
                $new_hash = password_hash($new, PASSWORD_BCRYPT);
                $upd = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
                $upd->bind_param('si', $new_hash, $user_id);
                if ($upd->execute()) {
                    $msg = 'Contraseña actualizada correctamente.';
                } else {
                    $msg = 'Error al actualizar contraseña: ' . $conn->error;
                }
                $upd->close();
            } else {
                $msg = 'Contraseña actual actual incorrecta.';
            }
        }
        $stmt->close();

        header('Location: carpintero.php');
        exit;
    }

    if ($action === 'update_carpenter') {
        // Actualizar los campos específicos de carpintero
        $specialties = trim($_POST['specialties'] ?? '');
        $experience_years = intval($_POST['experience_years'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $availability = trim($_POST['availability'] ?? 'Disponible');

        // Intentar encontrar la fila en carpenters por nombre
        $stmt = $conn->prepare("SELECT carpenter_id FROM carpenters WHERE carpenter_name = ? LIMIT 1");
        $stmt->bind_param('s', $_SESSION['user_name']);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $carp_id = $row['carpenter_id'];
            $stmt->close();

            $upd = $conn->prepare("UPDATE carpenters SET specialties = ?, experience_years = ?, description = ? WHERE carpenter_id = ?");
            $upd->bind_param('sisi', $specialties, $experience_years, $description, $carp_id);
            if ($upd->execute()) {
                $msg = 'Información del carpintero actualizada.';
            } else {
                $msg = 'Error al actualizar carpintero: ' . $conn->error;
            }
            $upd->close();
        } else {
            // Si no existe, insertar nueva fila vinculada por nombre
            $stmt->close();
            $ins = $conn->prepare("INSERT INTO carpenters (carpenter_name, specialties, experience_years, description, is_verified, approved, created_at) VALUES (?, ?, ?, ?, 0, 1, NOW())");
            $ins->bind_param('ssis', $_SESSION['user_name'], $specialties, $experience_years, $description);
            if ($ins->execute()) {
                $msg = 'Perfil de carpintero creado y guardado.';
            } else {
                $msg = 'Error al crear perfil de carpintero: ' . $conn->error;
            }
            $ins->close();
        }

        header('Location: carpintero.php');
        exit;
    }

      if ($action === 'upload_project') {
        // Manejo de subida de proyecto
        $pname = trim($_POST['project_name'] ?? '');
        $pdesc = trim($_POST['project_description'] ?? '');
        $pprice = floatval($_POST['project_price'] ?? 0);

        // Crear carpeta si no existe
        $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'projects';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $imagePath = null;
        if (!empty($_FILES['project_image']['tmp_name'])) {
          $tmp = $_FILES['project_image']['tmp_name'];
          $orig = basename($_FILES['project_image']['name']);
          $ext = pathinfo($orig, PATHINFO_EXTENSION);
          $filename = uniqid('proj_') . '.' . $ext;
          $dest = $uploadDir . DIRECTORY_SEPARATOR . $filename;
          if (move_uploaded_file($tmp, $dest)) {
            $imagePath = 'uploads/projects/' . $filename;
          }
        }

        // Insertar proyecto en BD (tabla portafolio)
        $stmtP = $conn->prepare("INSERT INTO portafolio (carpenter_user_id, title, description, image_path, price, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmtP->bind_param('isssd', $user_id, $pname, $pdesc, $imagePath, $pprice);
        if ($stmtP->execute()) {
          $msg = 'Proyecto subido correctamente.';
        } else {
          $msg = 'Error al subir proyecto: ' . $conn->error;
        }
        $stmtP->close();

        header('Location: carpintero.php');
        exit;
      }
}

// Cargar datos del carpintero desde tabla carpenters (carpinteros NO están en users)
$carp = null;
$user = [
    'user_id' => $user_id,
    'full_name' => $_SESSION['user_name'] ?? '',
    'email' => '',
    'phone' => '',
    'city' => ''
];

$stmt = $conn->prepare("SELECT * FROM carpenters WHERE carpenter_name = ? LIMIT 1");
$stmt->bind_param('s', $user['full_name']);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows > 0) {
    $carp = $res->fetch_assoc();
}
$stmt->close();

// Parsear información guardada en la descripción de la solicitud (si existe)
$reg_email = '';
$reg_phone = '';
$reg_city = '';
$reg_portfolio = '';
$reg_cv = ''; // Ruta del archivo CV
$reg_pwd_hash = '';
$reg_specialties = '';
$reg_experience = '';
if ($carp) {
  $reg_specialties = $carp['specialties'] ?? '';
  $reg_experience = $carp['experience_years'] ?? '';
  
  // CV está en el campo cv_file directamente
  $reg_cv = $carp['cv_file'] ?? '';
  
  $desc = $carp['description'] ?? '';
  if (!empty($desc)) {
    if (preg_match('/Email:\s*([^|]+)/i', $desc, $m)) {
      $reg_email = trim($m[1]);
    }
    if (preg_match('/Tel(?:efo?n)?:\s*([^|]+)/i', $desc, $m2)) {
      $reg_phone = trim($m2[1]);
    }
    if (preg_match('/Ciudad:\s*([^|]+)/i', $desc, $m3)) {
      $reg_city = trim($m3[1]);
    }
    if (preg_match('/Portafolio:\s*([^|]+)/i', $desc, $m4)) {
      $reg_portfolio = trim($m4[1]);
    }
    // Ya no buscamos CV en description porque está en cv_file
    if (preg_match('/Password:\s*([^|]+)/i', $desc, $m6)) {
      $reg_pwd_hash = trim($m6[1]);
    }
  }
}

// Obtener solicitudes de proyectos personalizados
$project_requests = [];
$requests_query = "
    SELECT pr.*, u.full_name as client_name 
    FROM project_requests pr 
    JOIN users u ON pr.user_id = u.user_id 
    WHERE pr.carpenter_user_id = ? 
    ORDER BY pr.created_at DESC";
$stmt_req = $conn->prepare($requests_query);
$stmt_req->bind_param("i", $user_id);
$stmt_req->execute();
$requests_res = $stmt_req->get_result();
if ($requests_res) {
    while ($req = $requests_res->fetch_assoc()) {
        $project_requests[] = $req;
    }
}
$stmt_req->close();

// Cargar proyectos del carpintero (si existen)
$projects = [];

// Comprobar existencia de la tabla 'portafolio' para evitar excepciones
$tblExists = false;
$check = $conn->query("SHOW TABLES LIKE 'portafolio'");
if ($check && $check->num_rows > 0) {
    $tblExists = true;
}

if ($tblExists) {
    $stmt = $conn->prepare("SELECT * FROM portafolio WHERE carpenter_user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
      while ($p = $res->fetch_assoc()) {
        // Cargar comentarios por proyecto (si existe la tabla)
        $p['comments'] = [];
        $cExists = false;
        $chkC = $conn->query("SHOW TABLES LIKE 'project_comments'");
        if ($chkC && $chkC->num_rows > 0) $cExists = true;

        if ($cExists) {
            $stmtC = $conn->prepare("SELECT pc.*, u.full_name as author_name FROM project_comments pc LEFT JOIN users u ON pc.user_id = u.user_id WHERE pc.project_id = ? ORDER BY pc.created_at DESC");
            $stmtC->bind_param('i', $p['project_id']);
            $stmtC->execute();
            $resC = $stmtC->get_result();
            if ($resC) {
              while ($c = $resC->fetch_assoc()) {
                $p['comments'][] = $c;
              }
            }
            $stmtC->close();
        }

        $projects[] = $p;
      }
    }
    $stmt->close();
} else {
  // No mostrar aviso en la UI para usuarios finales; el admin puede ejecutar setup_projects_tables.php si es necesario.
  // (Se evita setear $msg aquí para no mostrar el banner verde.)
}
?>

<main class="p-10 space-y-10">
  <!-- Mensaje -->
  <?php if (!empty($msg)): ?>
    <div class="max-w-2xl mx-auto bg-green-100 text-green-800 p-4 rounded-lg">
      <?php echo htmlspecialchars($msg); ?>
    </div>
  <?php endif; ?>

  <!-- Dashboard / Panel Principal -->
  <section id="dashboard" class="seccion">
    <h1 class="text-4xl font-bold text-stone-800 mb-2">Panel de Carpintero</h1>
    <p class="text-stone-600 mb-8">Bienvenido a tu panel de gestión, <?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></p>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Mi Información -->
      <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200 hover:shadow-xl transition">
        <div class="flex items-center gap-4 mb-4">
          <div class="bg-amber-100 p-3 rounded-lg">
            <i class="fas fa-user text-2xl text-amber-600"></i>
          </div>
          <h2 class="text-xl font-bold text-stone-800">Mi Información</h2>
        </div>
        <p class="text-stone-600 mb-4">Edita tu perfil y datos personales.</p>
        <button onclick="mostrarSeccion('info')" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg shadow w-full text-center">
          Editar Perfil
        </button>
      </div>

      <!-- Proyectos -->
      <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200 hover:shadow-xl transition">
        <div class="flex items-center gap-4 mb-4">
          <div class="bg-stone-100 p-3 rounded-lg">
            <i class="fas fa-folder-open text-2xl text-stone-600"></i>
          </div>
          <h2 class="text-xl font-bold text-stone-800">Proyectos</h2>
        </div>
        <p class="text-stone-600 mb-4">Sube y gestiona tus proyectos.</p>
        <button onclick="mostrarSeccion('proyectos')" class="inline-block bg-stone-600 hover:bg-stone-700 text-white font-semibold px-6 py-3 rounded-lg shadow w-full text-center">
          Ver Proyectos
        </button>
      </div>

      <!-- Solicitudes -->
      <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200 hover:shadow-xl transition">
        <div class="flex items-center gap-4 mb-4">
          <div class="bg-orange-100 p-3 rounded-lg">
            <i class="fas fa-clipboard-list text-2xl text-orange-600"></i>
          </div>
          <h2 class="text-xl font-bold text-stone-800">Solicitudes</h2>
        </div>
        <p class="text-stone-600 mb-4">Revisa solicitudes de proyectos personalizados.</p>
        <button onclick="mostrarSeccion('solicitudes')" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg shadow w-full text-center">
          Ver Solicitudes
        </button>
      </div>
    </div>

    <!-- Información de cuenta -->
    <div class="mt-10 bg-white p-8 rounded-xl shadow-lg border border-stone-200">
      <h2 class="text-2xl font-bold text-stone-800 mb-6">Información de la Cuenta</h2>
      <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
          <p class="text-xs font-bold text-stone-400 uppercase mb-1">Nombre</p>
          <p class="text-stone-800 font-medium"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'N/A'); ?></p>
        </div>
        <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
          <p class="text-xs font-bold text-stone-400 uppercase mb-1">Especialidad</p>
          <p class="text-stone-800 font-medium"><?php echo htmlspecialchars($reg_specialties ?: 'No especificada'); ?></p>
        </div>
        <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
          <p class="text-xs font-bold text-stone-400 uppercase mb-1">Años de Experiencia</p>
          <p class="text-stone-800 font-medium"><?php echo htmlspecialchars($reg_experience !== '' ? $reg_experience . ' años' : 'No especificado'); ?></p>
        </div>
        <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
          <p class="text-xs font-bold text-stone-400 uppercase mb-1">Solicitudes Recibidas</p>
          <p class="text-stone-800 font-medium"><?php echo count($project_requests); ?> en total</p>
        </div>
      </div>
      <div class="mt-6 text-center">
        <button onclick="mostrarSeccion('info')" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-8 py-3 rounded-lg shadow">
          <i class="fas fa-edit mr-2"></i>
          Editar Mi Perfil
        </button>
      </div>
    </div>
  </section>

  <!-- Información -->
  <section id="info" class="seccion hidden">
    <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Información del Carpintero</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Columna 1: Formulario de Edición -->
      <div class="bg-white p-8 rounded-xl shadow-lg border border-stone-200">
        <h2 class="text-xl font-bold text-stone-800 mb-4">Editar Perfil</h2>
        <form method="POST" class="space-y-4">
          <input type="hidden" name="action" value="update_profile">
          <div>
            <label class="block font-semibold text-stone-700">Nombre completo</label>
            <input name="full_name" type="text" value="<?php echo htmlspecialchars($user['full_name']); ?>"
              class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>
          <div>
            <label class="block font-semibold text-stone-700">Correo</label>
            <input name="email" type="email" value="<?php echo htmlspecialchars($reg_email ?: ($user['email'] ?? '')); ?>"
              class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>
          <div>
            <label class="block font-semibold text-stone-700">Teléfono</label>
            <input name="phone" type="text" value="<?php echo htmlspecialchars($reg_phone ?: ($user['phone'] ?? '')); ?>"
              class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>
          <div>
            <label class="block font-semibold text-stone-700">Ubicación</label>
            <input name="city" type="text" value="<?php echo htmlspecialchars($reg_city ?: ($user['city'] ?? '')); ?>"
              class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
          </div>
          <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white py-3 rounded-lg font-semibold shadow">Guardar cambios</button>
        </form>
      </div>

      <!-- Columna 2: Datos de Solicitud -->
      <div class="bg-white p-8 rounded-xl shadow-lg border border-stone-200 h-fit">
        <h2 class="text-xl font-bold text-stone-800 mb-4">Datos de Solicitud</h2>
        <div class="bg-stone-50 p-6 rounded-lg border border-stone-100 space-y-4">
          <div>
            <span class="block text-xs font-bold text-stone-400 uppercase tracking-wide">Especialidad</span>
            <span class="text-stone-800 font-medium"><?php echo htmlspecialchars($reg_specialties ?: '-'); ?></span>
          </div>
          <div>
            <span class="block text-xs font-bold text-stone-400 uppercase tracking-wide">Años de experiencia</span>
            <span class="text-stone-800 font-medium"><?php echo htmlspecialchars($reg_experience !== '' ? $reg_experience : '-'); ?></span>
          </div>
          <div>
            <span class="block text-xs font-bold text-stone-400 uppercase tracking-wide">Portafolio</span>
            <?php if (!empty($reg_portfolio)): ?>
              <a class="text-amber-600 hover:underline font-medium" href="<?php echo htmlspecialchars($reg_portfolio); ?>" target="_blank">Ver portafolio</a>
            <?php else: ?>
              <span class="text-stone-500">-</span>
            <?php endif; ?>
          </div>
          <div>
            <span class="block text-xs font-bold text-stone-400 uppercase tracking-wide">Hoja de vida</span>
            <?php if (!empty($reg_cv)): ?>
              <a class="text-amber-600 hover:underline font-medium" href="<?php echo htmlspecialchars($reg_cv); ?>" target="_blank">Ver/Descargar CV</a>
            <?php else: ?>
              <span class="text-stone-500">-</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Subir proyectos -->
  <section id="proyectos" class="seccion hidden">
    <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Gestión de Proyectos</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Columna Izquierda: Subir Proyecto -->
      <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200 sticky top-6">
          <h2 class="text-xl font-bold text-stone-800 mb-4">Subir Nuevo Proyecto</h2>
          <form id="uploadForm" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-stone-700 mb-1">Nombre del proyecto</label>
              <input name="project_name" type="text" placeholder="Ej: Mesa de comedor" required
                class="w-full px-3 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 text-sm">
            </div>
            <div>
              <label class="block text-sm font-semibold text-stone-700 mb-1">Descripción</label>
              <textarea name="project_description" rows="3" placeholder="Detalles del proyecto..."
                class="w-full px-3 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 text-sm"></textarea>
            </div>
            <div>
              <label class="block text-sm font-semibold text-stone-700 mb-1">Imagen</label>
              <input name="project_image" type="file" accept="image/*"
                class="w-full text-xs text-stone-600 file:mr-2 file:py-2 file:px-3 
                       file:rounded-lg file:border-0 
                       file:text-xs file:font-semibold 
                       file:bg-amber-600 file:text-white
                       hover:file:bg-amber-700 cursor-pointer">
            </div>
            <div>
              <label class="block text-sm font-semibold text-stone-700 mb-1">Precio</label>
              <input name="project_price" type="number" placeholder="Ej: 500000"
                class="w-full px-3 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500 text-sm">
            </div>
            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white py-2.5 rounded-lg font-semibold shadow text-sm">Publicar Proyecto</button>
          </form>
        </div>
      </div>
      
      <!-- Columna Derecha: Lista de Proyectos (2 columnas) -->
      <div class="lg:col-span-2">
        <h3 class="text-xl font-bold text-stone-800 mb-4">Tus Proyectos Publicados</h3>
        <?php if (empty($projects)): ?>
          <div class="bg-white p-8 rounded-xl shadow border border-stone-200 text-center">
            <p class="text-stone-500">Aún no has subido proyectos.</p>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($projects as $p): ?>
              <div class="bg-white border border-stone-200 rounded-xl p-6 shadow-sm hover:shadow-md transition">
                <?php if (!empty($p['image_path'])): ?>
                  <img src="<?php echo htmlspecialchars($p['image_path']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" class="w-full rounded-lg mb-4">
                <?php endif; ?>
                <h3 class="font-bold text-xl text-stone-800 line-clamp-1 mb-2"><?php echo htmlspecialchars($p['title']); ?></h3>
                <p class="text-sm text-stone-600 mt-1 line-clamp-3 mb-3"><?php echo htmlspecialchars($p['description']); ?></p>
                <p class="text-amber-700 font-bold text-lg mb-4"><?php echo '$' . number_format($p['price'], 0, ',', '.'); ?></p>
                <div class="flex gap-2 mt-3">
                  <button onclick="document.getElementById('modal-proj-<?php echo $p['project_id']; ?>').classList.remove('hidden')" type="button" class="flex-1 px-4 py-3 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-lg text-sm font-medium transition">Ver</button>
                  <form method="POST" action="carp_delete_project.php" class="flex-1">
                    <input type="hidden" name="project_id" value="<?php echo $p['project_id']; ?>">
                    <button type="submit" class="w-full px-4 py-3 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-sm font-medium transition">Eliminar</button>
                  </form>
                </div>

                <!-- Modal simple para ver proyecto -->
                <div id="modal-proj-<?php echo $p['project_id']; ?>" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center hidden z-50 backdrop-blur-sm">
                  <div class="bg-white w-full max-w-2xl rounded-2xl p-0 overflow-hidden shadow-2xl relative max-h-[90vh] overflow-y-auto">
                    <!-- Header Modal -->
                    <div class="bg-gradient-to-r from-amber-600 to-amber-700 p-4 flex justify-between items-center sticky top-0 z-10">
                      <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($p['title']); ?></h3>
                      <button onclick="document.getElementById('modal-proj-<?php echo $p['project_id']; ?>').classList.add('hidden')" class="text-white hover:bg-white/20 rounded-full p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                    
                    <div class="p-6">
                      <?php if (!empty($p['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($p['image_path']); ?>" class="w-full h-64 object-cover rounded-xl mb-6 shadow-sm">
                      <?php endif; ?>
                      
                      <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                           <p class="text-xs font-bold text-stone-400 uppercase tracking-wide">Precio</p>
                           <p class="text-xl font-bold text-amber-600"><?php echo '$' . number_format($p['price'], 0, ',', '.'); ?></p>
                        </div>
                      </div>

                      <div class="mb-6">
                        <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-2">Descripción</p>
                        <p class="text-stone-700 bg-stone-50 p-4 rounded-lg border border-stone-100"><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
                      </div>

                      <div class="border-t border-stone-200 pt-6">
                        <h4 class="font-bold text-stone-800 mb-4 flex items-center gap-2">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                          </svg>
                          Comentarios
                        </h4>
                        
                        <div class="space-y-3 mb-4 max-h-60 overflow-y-auto">
                          <?php if (empty($p['comments'])): ?>
                            <p class="text-sm text-stone-500 italic">No hay comentarios aún.</p>
                          <?php else: ?>
                            <?php foreach ($p['comments'] as $c): ?>
                              <div class="bg-stone-50 p-3 rounded-lg border border-stone-100">
                                <div class="flex justify-between items-start mb-1">
                                  <span class="font-semibold text-stone-800 text-sm"><?php echo htmlspecialchars($c['author_name'] ?? 'Anónimo'); ?></span>
                                  <span class="text-xs text-stone-400"><?php echo date('d/m/Y', strtotime($c['created_at'])); ?></span>
                                </div>
                                <p class="text-sm text-stone-600"><?php echo htmlspecialchars($c['comment']); ?></p>
                              </div>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </div>

                        <form method="POST" action="carp_add_comment.php" class="flex gap-2">
                          <input type="hidden" name="project_id" value="<?php echo $p['project_id']; ?>">
                          <input name="comment" required class="flex-1 border border-stone-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 outline-none" placeholder="Escribe una respuesta..."></input>
                          <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Enviar</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Solicitudes -->
  <section id="solicitudes" class="seccion hidden">
    <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Solicitudes de Proyectos Personalizados</h1>
    
    <?php if (!empty($project_requests)): ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($project_requests as $req): ?>
          <div class="bg-white p-6 rounded-xl shadow-md border border-stone-200 flex flex-col justify-between hover:shadow-lg transition">
            <div>
              <div class="flex items-center gap-3 mb-3">
                <div class="bg-amber-100 p-2 rounded-full text-amber-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 2.2c.4-.4 1-.4 1.4 0l3.7 3.7c.4.4.4 1 0 1.4l-2.8 2.8c-.4.4-1 .4-1.4 0l-3.7-3.7c-.4-.4-.4-1 0-1.4l2.8-2.8zM12 9l-8 8c-1.1 1.1-1.1 2.9 0 4 .5.5 1.3.5 1.8 0l8-8-1.8-1.8z" />
                  </svg>
                </div>
                <h3 class="font-bold text-lg text-stone-800 line-clamp-1"><?php echo htmlspecialchars($req['title']); ?></h3>
              </div>
              
              <p class="text-stone-600 text-sm mb-1">
                <strong>Cliente:</strong> 
                <span class="text-amber-700 font-medium"><?php echo htmlspecialchars($req['client_name']); ?></span>
              </p>
              <p class="text-stone-600 text-sm mb-3">
                <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($req['created_at'])); ?>
              </p>
              
              <div class="flex items-center justify-between mb-3">
                <?php if (!empty($req['budget'])): ?>
                  <span class="text-amber-600 font-bold text-lg">
                    $<?php echo number_format($req['budget'], 0, ',', '.'); ?>
                  </span>
                <?php else: ?>
                  <span class="text-stone-400 text-sm">Sin presupuesto</span>
                <?php endif; ?>
                
                <span class="px-3 py-1 rounded-full text-xs font-bold 
                  <?php 
                    echo match($req['status']) {
                      'pending' => 'bg-yellow-100 text-yellow-800',
                      'accepted' => 'bg-green-100 text-green-800',
                      'rejected' => 'bg-red-100 text-red-800',
                      'completed' => 'bg-blue-100 text-blue-800',
                      default => 'bg-gray-100 text-gray-800'
                    };
                  ?>">
                  <?php 
                    echo match($req['status']) {
                      'pending' => 'Pendiente',
                      'accepted' => 'Aceptada',
                      'rejected' => 'Rechazada',
                      'completed' => 'Completada',
                      default => $req['status']
                    };
                  ?>
                </span>
              </div>
            </div>
            
            <button 
              onclick="verDetalleProyecto(<?php echo $req['request_id']; ?>)"
              class="w-full bg-stone-100 hover:bg-stone-200 text-stone-700 py-2 rounded-lg font-semibold transition flex items-center justify-center gap-2 mt-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              Ver Detalles
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="bg-white p-8 rounded-xl shadow-md border border-stone-200 text-center">
        <p class="text-stone-500 text-lg">No tienes solicitudes de proyectos pendientes.</p>
      </div>
    <?php endif; ?>
  </section>

  <!-- Notificaciones -->
  <section id="notificaciones" class="seccion hidden">
    <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Notificaciones</h1>
    <div id="lista-notificaciones" class="space-y-4">
      <p class="text-stone-600">No tienes notificaciones nuevas.</p>
    </div>
  </section>

  <!-- Cambiar contraseña -->
  <section id="cambiar-password" class="seccion hidden">
    <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Cambiar contraseña</h1>
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-2xl border border-stone-200">
      <form method="POST" class="space-y-4">
        <input type="hidden" name="action" value="change_password">
        <div>
          <label class="block font-semibold text-stone-700">Contraseña actual</label>
          <input name="current_password" type="password" required
            class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
        </div>
        <div>
          <label class="block font-semibold text-stone-700">Nueva contraseña</label>
          <input name="new_password" type="password" required
            class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-amber-500">
        </div>
        <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white py-3 rounded-lg font-semibold shadow">Cambiar contraseña</button>
      </form>
    </div>
  </section>

  <!-- Modal de Detalle de Proyecto -->
  <div id="modal-proyecto" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center hidden z-50 backdrop-blur-sm">
    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden transform transition-all scale-100 max-h-[90vh] overflow-y-auto">
      <!-- Header Modal -->
      <div class="bg-gradient-to-r from-amber-600 to-amber-700 p-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Detalle de Solicitud
        </h2>
        <button onclick="cerrarModalProyecto()" class="text-white hover:bg-white/20 rounded-full p-1 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Body Modal -->
      <div class="p-8">
        <div class="flex items-start gap-6 mb-6">
          <div class="bg-stone-100 p-4 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-stone-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="flex-1">
            <h3 id="proy-titulo" class="text-2xl font-bold text-stone-800">Título del Proyecto</h3>
            <p class="text-amber-600 font-medium text-lg">Cliente: <span id="proy-cliente">Nombre Cliente</span></p>
            <div class="flex gap-4 mt-2 text-sm text-stone-500">
              <span id="proy-fecha">Fecha</span>
              <span id="proy-estado-badge" class="px-2 py-1 rounded-full text-xs font-bold">Estado</span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-stone-50 p-6 rounded-xl border border-stone-100 mb-6">
          <div id="proy-medidas-container">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-1">Medidas</p>
            <p id="proy-medidas" class="text-stone-800 font-medium">No especificado</p>
          </div>
          <div id="proy-presupuesto-container">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-1">Presupuesto</p>
            <p id="proy-presupuesto" class="text-stone-800 font-medium text-amber-600">No especificado</p>
          </div>
          <div id="proy-deadline-container">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-1">Fecha Deseada</p>
            <p id="proy-deadline" class="text-stone-800 font-medium">No especificada</p>
          </div>
        </div>

        <div class="mb-6">
          <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-2">Descripción del Proyecto</p>
          <p id="proy-descripcion" class="text-stone-700 bg-stone-50 p-4 rounded-lg border border-stone-100"></p>
        </div>

        <div id="proy-materiales-container" class="mb-6 hidden">
          <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-2">Materiales Preferidos</p>
          <p id="proy-materiales" class="text-stone-700 bg-amber-50 p-4 rounded-lg border border-amber-100"></p>
        </div>

        <div id="proy-imagen-container" class="mb-6 hidden">
          <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-2">Imagen de Referencia</p>
          <img id="proy-imagen" src="" alt="Referencia" class="max-w-full h-auto rounded-lg border border-stone-200 shadow-md cursor-pointer hover:shadow-lg transition">
        </div>
      </div>

      <!-- Footer Modal -->
      <div class="bg-stone-50 px-8 py-6 border-t border-stone-200 flex justify-end gap-4" id="proy-actions">
        <button onclick="cerrarModalProyecto()" class="px-5 py-2 rounded-lg text-stone-600 hover:bg-stone-200 font-medium transition">Cancelar</button>
        <button id="btn-proy-rechazar" onclick="actualizarEstadoDesdeModal('rejected')" class="px-5 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 font-medium transition border border-red-200">Rechazar</button>
        <button id="btn-proy-aceptar" onclick="actualizarEstadoDesdeModal('accepted')" class="px-6 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-bold shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">Aceptar Solicitud</button>
      </div>
    </div>
  </div>

</main>

<script>
function mostrarSeccion(id) {
  // Ocultar todas las secciones
  document.querySelectorAll('.seccion').forEach(sec => sec.classList.add('hidden'));

  // Mostrar la sección seleccionada
  document.getElementById(id).classList.remove('hidden');

  // Actualizar botones activos
  document.querySelectorAll('.seccion-btn').forEach(btn => {
    btn.classList.remove('bg-amber-100', 'text-amber-700');
  });

  // Marcar botón activo
  const activeBtn = document.querySelector(`button[onclick="mostrarSeccion('${id}')"]`);
  if (activeBtn) {
    activeBtn.classList.add('bg-amber-100', 'text-amber-700');
  }
}

// Mostrar dashboard por defecto al cargar
window.addEventListener('DOMContentLoaded', () => {
  mostrarSeccion('dashboard');
});

async function actualizarEstadoSolicitud(requestId, status) {
  try {
    const response = await fetch('carp_update_request_status.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ request_id: requestId, status: status })
    });

    const result = await response.json();

    if (result.success) {
      // Recargar la página para mostrar los cambios
      location.reload();
    } else {
      alert('Error: ' + (result.message || 'No se pudo actualizar la solicitud'));
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Error al procesar la solicitud');
  }
}

// Variables globales para el modal
let currentRequestId = null;
const projectRequests = <?php echo json_encode($project_requests); ?>;

function verDetalleProyecto(requestId) {
  currentRequestId = requestId;
  const req = projectRequests.find(r => r.request_id == requestId);
  
  if (!req) {
    alert('Solicitud no encontrada');
    return;
  }

  // Poblar datos del modal
  document.getElementById('proy-titulo').textContent = req.title;
  document.getElementById('proy-cliente').textContent = req.client_name;
  document.getElementById('proy-fecha').textContent = 'Solicitado: ' + new Date(req.created_at).toLocaleDateString('es-ES');
  
  // Estado badge
  const estadoBadge = document.getElementById('proy-estado-badge');
  const statusMap = {
    'pending': { text: 'Pendiente', class: 'bg-yellow-100 text-yellow-800' },
    'accepted': { text: 'Aceptada', class: 'bg-green-100 text-green-800' },
    'rejected': { text: 'Rechazada', class: 'bg-red-100 text-red-800' },
    'completed': { text: 'Completada', class: 'bg-blue-100 text-blue-800' }
  };
  const statusInfo = statusMap[req.status] || { text: req.status, class: 'bg-gray-100 text-gray-800' };
  estadoBadge.textContent = statusInfo.text;
  estadoBadge.className = 'px-2 py-1 rounded-full text-xs font-bold ' + statusInfo.class;

  // Datos opcionales
  document.getElementById('proy-medidas').textContent = req.dimensions || 'No especificado';
  document.getElementById('proy-presupuesto').textContent = req.budget ? '$' + parseFloat(req.budget).toLocaleString('es-CO') : 'No especificado';
  document.getElementById('proy-deadline').textContent = req.deadline ? new Date(req.deadline).toLocaleDateString('es-ES') : 'No especificada';
  
  // Descripción
  document.getElementById('proy-descripcion').innerHTML = req.description.replace(/\n/g, '<br>');
  
  // Materiales
  const materialesContainer = document.getElementById('proy-materiales-container');
  if (req.materials && req.materials.trim()) {
    document.getElementById('proy-materiales').innerHTML = req.materials.replace(/\n/g, '<br>');
    materialesContainer.classList.remove('hidden');
  } else {
    materialesContainer.classList.add('hidden');
  }
  
  // Imagen
  const imagenContainer = document.getElementById('proy-imagen-container');
  if (req.image_path && req.image_path.trim()) {
    const imgEl = document.getElementById('proy-imagen');
    imgEl.src = req.image_path;
    imgEl.onclick = () => window.open(req.image_path, '_blank');
    imagenContainer.classList.remove('hidden');
  } else {
    imagenContainer.classList.add('hidden');
  }
  
  // Botones de acción (solo si está pendiente)
  const btnAceptar = document.getElementById('btn-proy-aceptar');
  const btnRechazar = document.getElementById('btn-proy-rechazar');
  if (req.status === 'pending') {
    btnAceptar.classList.remove('hidden');
    btnRechazar.classList.remove('hidden');
  } else {
    btnAceptar.classList.add('hidden');
    btnRechazar.classList.add('hidden');
  }
  
  // Mostrar modal
  document.getElementById('modal-proyecto').classList.remove('hidden');
}

function cerrarModalProyecto() {
  document.getElementById('modal-proyecto').classList.add('hidden');
  currentRequestId = null;
}

async function actualizarEstadoDesdeModal(status) {
  if (!currentRequestId) return;
  cerrarModalProyecto();
   await actualizarEstadoSolicitud(currentRequestId, status);
}
</script>
<!-- Mejoras JS: AJAX upload, render projects, AJAX comments, toast -->
<script>
// Toast simple
function showToast(msg, type = 'success') {
  const t = document.createElement('div');
  t.textContent = msg;
  t.className = 'fixed right-6 bottom-24 z-50 px-4 py-2 rounded shadow text-white ' + (type === 'error' ? 'bg-red-600' : 'bg-green-600');
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 4000);
}

// Re-render projects list from API
function renderProjects() {
  fetch('carp_projects_api.php')
    .then(r => r.json())
    .then(data => {
      const container = document.querySelector('#proyectos .mt-8 > div') || document.querySelector('#proyectos');
      // We'll re-render the projects grid inside the existing structure
      const grid = document.querySelector('#proyectos .grid.md\:grid-cols-2');
      if (!grid) return;
      grid.innerHTML = '';
      if (!data || data.length === 0) {
        grid.innerHTML = '<p class="text-stone-600">Aún no has subido proyectos.</p>';
        return;
      }
      data.forEach(p => {
        const div = document.createElement('div');
        div.className = 'border rounded-lg p-4 bg-stone-50';
        div.innerHTML = `
          ${p.image_path ? `<img src="${p.image_path}" class="w-full h-40 object-cover rounded-lg mb-3">` : ''}
          <h3 class="font-bold text-lg">${escapeHtml(p.title)}</h3>
          <p class="text-sm text-stone-700 mt-2">${escapeHtml(p.description)}</p>
          <p class="text-amber-700 font-semibold mt-2">$${Number(p.price).toLocaleString()}</p>
          <div class="flex gap-2 mt-3">
            <button data-id="${p.project_id}" class="btn-view px-3 py-2 bg-blue-600 text-white rounded">Ver</button>
            <form method="POST" action="carp_delete_project.php" onsubmit="return confirm('¿Eliminar proyecto?');">
              <input type="hidden" name="project_id" value="${p.project_id}">
              <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded">Eliminar</button>
            </form>
          </div>
        `;
        grid.appendChild(div);
      });

      // Delegación para ver (abrir modal simple reusando modal-proj-* si existe)
      document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', e => {
          const id = e.currentTarget.getAttribute('data-id');
          // Intentar abrir modal existente
          const m = document.getElementById('modal-proj-' + id);
          if (m) m.classList.remove('hidden');
        });
      });
    }).catch(err => console.error(err));
}

function escapeHtml(s) { return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

// Interceptar formulario de subida para AJAX
const uploadForm = document.querySelector('#uploadForm');
if (uploadForm) {
  uploadForm.addEventListener('submit', e => {
    e.preventDefault();
    const fd = new FormData(uploadForm);
    // Validación imagen
    const file = uploadForm.querySelector('input[type=file]')?.files[0];
    if (file) {
      if (!file.type.startsWith('image/')) { showToast('Archivo no es una imagen', 'error'); return; }
      if (file.size > 4 * 1024 * 1024) { showToast('Imagen demasiado grande (max 4MB)', 'error'); return; }
    }
    fetch('carp_upload_project.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(res => {
        if (res.ok) {
          showToast('Proyecto subido');
          // limpiar form
          uploadForm.reset();
          // Si la API nos devolvió el proyecto insertado, lo añadimos al grid inmediatamente
          if (res.project) {
            appendProjectToGrid(res.project);
          } else {
            renderProjects();
          }
        } else {
          showToast(res.error || 'Error', 'error');
        }
      }).catch(() => showToast('Error de red', 'error'));
  });
}

// Añadir dinámicamente un proyecto al grid con modal
function appendProjectToGrid(p) {
  try {
    const grid = document.querySelector('#proyectos .grid.md\\:grid-cols-2');
    
    if (!grid) {
      console.log('Grid no encontrado');
      return;
    }
    
    // Crear el contenedor del proyecto (card) - MISMO TAMAÑO QUE LOS CARGADOS
    const projectCard = document.createElement('div');
    projectCard.className = 'bg-white border border-stone-200 rounded-xl p-6 shadow-sm hover:shadow-md transition';
    
    // Crear el modal
    const modalDiv = document.createElement('div');
    modalDiv.id = `modal-proj-${p.project_id}`;
    modalDiv.className = 'fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center hidden z-50 backdrop-blur-sm';
    
    modalDiv.innerHTML = `
      <div class="bg-white w-full max-w-2xl rounded-2xl p-0 overflow-hidden shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-amber-600 to-amber-700 p-4 flex justify-between items-center sticky top-0 z-10">
          <h3 class="text-xl font-bold text-white">${escapeHtml(p.title || '')}</h3>
          <button onclick="document.getElementById('modal-proj-${p.project_id}').classList.add('hidden')" class="text-white hover:bg-white/20 rounded-full p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="p-6">
          ${p.image_path ? `<img src="${p.image_path}" class="w-full h-64 object-cover rounded-xl mb-6 shadow-sm">` : ''}
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
              <p class="text-xs font-bold text-stone-400 uppercase tracking-wide">Precio</p>
              <p class="text-xl font-bold text-amber-600">$${Number(p.price || 0).toLocaleString()}</p>
            </div>
          </div>
          <div class="mb-6">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-wide mb-2">Descripción</p>
            <p class="text-stone-700 bg-stone-50 p-4 rounded-lg border border-stone-100">${escapeHtml(p.description || '')}</p>
          </div>
          <div class="border-t border-stone-200 pt-6">
            <h4 class="font-bold text-stone-800 mb-4 flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
              </svg>
              Comentarios
            </h4>
            <div class="space-y-3 mb-4 max-h-60 overflow-y-auto">
              <p class="text-sm text-stone-500 italic">No hay comentarios aún.</p>
            </div>
          </div>
        </div>
      </div>
    `;
    
    // HTML del card - MISMO DISEÑO Y TAMAÑO QUE PHP
    projectCard.innerHTML = `
      ${p.image_path ? `<img src="${p.image_path}" alt="${escapeHtml(p.title || '')}" class="w-full rounded-lg mb-4">` : ''}
      <h3 class="font-bold text-xl text-stone-800 line-clamp-1 mb-2">${escapeHtml(p.title || '')}</h3>
      <p class="text-sm text-stone-600 mt-1 line-clamp-3 mb-3">${escapeHtml(p.description || '')}</p>
      <p class="text-amber-700 font-bold text-lg mb-4">$${Number(p.price || 0).toLocaleString()}</p>
      <div class="flex gap-2 mt-3">
        <button onclick="document.getElementById('modal-proj-${p.project_id}').classList.remove('hidden')" type="button" class="flex-1 px-4 py-3 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-lg text-sm font-medium transition">Ver</button>
        <form method="POST" action="carp_delete_project.php" class="flex-1">
          <input type="hidden" name="project_id" value="${p.project_id}">
          <button type="submit" class="w-full px-4 py-3 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-sm font-medium transition">Eliminar</button>
        </form>
      </div>
    `;
    
    // Agregar modal al card
    projectCard.appendChild(modalDiv);
    
    // Insertar al FINAL del grid (no al inicio)
    grid.appendChild(projectCard);
    
    console.log('✅ Proyecto agregado exitosamente al final del grid');
    
  } catch (e) {
    console.error('❌ Error al agregar proyecto:', e);
  }
}

// Interceptar envío de comentarios en modales (delegación)
document.addEventListener('submit', function(e) {
  const f = e.target;
  if (f && f.action && f.action.endsWith('carp_add_comment.php')) {
    e.preventDefault();
    const fd = new FormData(f);
    fetch('carp_add_comment.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(res => {
        if (res.ok) {
          showToast('Comentario añadido');
          renderProjects();
        } else {
          showToast(res.error || 'Error', 'error');
        }
      }).catch(() => showToast('Error de red', 'error'));
  }
  
  // Interceptar eliminación de proyectos (AJAX sin redirección)
  if (f && f.action && f.action.endsWith('carp_delete_project.php')) {
    e.preventDefault();
    if (!confirm('¿Eliminar proyecto?')) return;
    
    const fd = new FormData(f);
    const projectId = fd.get('project_id');
    
    fetch('carp_delete_project.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(res => {
        if (res.ok) {
          showToast('Proyecto eliminado');
          // Buscar y eliminar el card del proyecto
          const card = f.closest('.bg-white.border');
          if (card) {
            card.style.transition = 'opacity 0.3s';
            card.style.opacity = '0';
            setTimeout(() => card.remove(), 300);
          }
        } else {
          showToast(res.error || 'Error al eliminar', 'error');
        }
      }).catch(() => showToast('Error de red', 'error'));
  }
});

// Inicializar
renderProjects();
</script>
<!-- Modal Vista Previa Dinámico -->
<div id="modal-vista-previa" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
  <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl p-8 overflow-y-auto max-h-[90vh] relative">
    <button onclick="cerrarVistaPrevia()" 
            class="absolute top-4 right-4 bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded-lg font-semibold">Volver</button>
    <h1 class="text-3xl font-extrabold text-stone-800 mb-6">Vista Previa del Perfil</h1>

    <div class="flex flex-row gap-6 items-center mb-6">
      <img src="<?php echo htmlspecialchars($carp['cv_file'] ?? 'img/fotoP.jpg'); ?>" alt="Foto Carpintero" class="w-32 h-32 rounded-full border-4 border-amber-600 object-cover shadow-md">
      <div class="flex-1">
        <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($user['full_name']); ?></h2>
        <p class="text-stone-600"><?php echo htmlspecialchars($user['city']); ?></p>
        <p class="text-stone-600"><?php echo htmlspecialchars($user['email']); ?></p>
        <p class="text-stone-600"><?php echo htmlspecialchars($user['phone']); ?></p>
        <p class="mt-2 text-stone-700"><strong>Especialidad:</strong> <?php echo htmlspecialchars($carp['specialties'] ?? 'No especificada'); ?></p>
        <p class="mt-1 text-stone-700"><strong>Experiencia:</strong> <?php echo htmlspecialchars($carp['experience_years'] ?? '0'); ?> años</p>
        <span id="badge-disponibilidad" class="inline-block mt-3 px-3 py-1 rounded-full <?php echo (isset($carp['availability']) && $carp['availability'] !== 'No disponible') ? 'bg-green-600' : 'bg-red-600'; ?> text-white text-sm font-semibold"><?php echo htmlspecialchars($carp['availability'] ?? 'Disponible'); ?></span>
      </div>
    </div>

    <h3 class="text-xl font-bold mb-4">Proyectos</h3>
    <div id="lista-proyectos" class="grid md:grid-cols-2 gap-6 mb-6">
      <?php if (empty($projects)): ?>
        <p class="text-stone-600">No hay proyectos aún.</p>
      <?php else: ?>
        <?php foreach ($projects as $p): ?>
          <div class="bg-stone-50 border border-stone-200 rounded-xl shadow p-4">
            <?php if (!empty($p['image_path'])): ?>
              <img src="<?php echo htmlspecialchars($p['image_path']); ?>" alt="Proyecto" class="w-full h-40 object-cover rounded-lg mb-3">
            <?php endif; ?>
            <h3 class="text-lg font-bold"><?php echo htmlspecialchars($p['title']); ?></h3>
            <p class="text-stone-600 text-sm mb-2"><?php echo htmlspecialchars($p['description']); ?></p>
            <p class="text-amber-700 font-semibold"><?php echo '$' . number_format($p['price'], 0, ',', '.'); ?></p>

            <div class="mt-4">
              <h4 class="font-semibold">Comentarios</h4>
              <?php if (empty($p['comments'])): ?>
                <p class="text-sm text-stone-600">Sin comentarios.</p>
              <?php else: ?>
                <?php foreach ($p['comments'] as $c): ?>
                  <div class="bg-white p-3 rounded-lg mt-2 border">
                    <p class="font-semibold"><?php echo htmlspecialchars($c['author_name'] ?? 'Anónimo'); ?> <span class="text-sm text-stone-500">(<?php echo $c['created_at']; ?>)</span></p>
                    <p class="text-sm text-stone-600"><?php echo htmlspecialchars($c['comment']); ?></p>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <h3 class="text-xl font-bold mb-4">Comentarios Generales</h3>
    <div id="lista-comentarios" class="space-y-4 mb-6">
      <!-- Comentarios globales podrían cargarse aquí -->
    </div>

    <div class="text-center space-x-4">
      <button class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg shadow">Solicitar proyecto personalizado</button>
      <button class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg shadow">Contratación</button>
    </div>
  </div>
</div>

<script>
function abrirVistaPrevia() {
  document.getElementById('modal-vista-previa').classList.remove('hidden');
}
function cerrarVistaPrevia() {
  document.getElementById('modal-vista-previa').classList.add('hidden');
}

// Polling para actualizar proyectos y notificaciones cada 7s
function fetchUpdates() {
  fetch('carp_projects_api.php')
    .then(r => r.json())
    .then(data => {
      // placeholder: se puede re-renderizar #lista-proyectos dinámicamente
    }).catch(()=>{});

  fetch('carp_notifications_api.php')
    .then(r => r.json())
    .then(data => {
      const list = document.getElementById('lista-notificaciones');
      if (!list) return;
      list.innerHTML = '';
      if (!data || data.length === 0) {
        list.innerHTML = '<p class="text-stone-600">No tienes notificaciones nuevas.</p>';
      } else {
        data.forEach(n => {
          const d = document.createElement('div');
          d.className = 'bg-stone-50 border border-stone-200 rounded-xl p-4';
          d.innerHTML = '<p class="text-sm text-stone-700">' + n.message + '</p>';
          list.prepend(d);
        });
      }
    }).catch(()=>{});
}

setInterval(fetchUpdates, 7000);
fetchUpdates();
</script>
