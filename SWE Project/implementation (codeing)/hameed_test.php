<?php
require_once 'core/Database.php';
require_once 'app/models/dad_user.php';
require_once 'app/models/FloorStaff.php';
require_once 'app/models/Supplier.php';
require_once 'app/models/WarehouseManager.php';


// ... الاستدعاءات (require_once) بتفضل زي ما هي ...

$message = "";
$data = null;

// التحقق من أن الطلب تم إرساله عبر POST وأن الأزرار المطلوبة موجودة
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_type']) && isset($_POST['action'])) {

  $type = $_POST['user_type'];
  $action = $_POST['action'];
  // تحديد الـ Class بناءً على النوع
  if ($type == 'staff') $user = new FloorStaff($_POST['name'] ?? null, $_POST['email'] ?? null, $_POST['password'] ?? null);
  if ($type == 'supplier') $user = new Supplier($_POST['name'] ?? null, $_POST['email'] ?? null, $_POST['password'] ?? null);
  if ($type == 'manager') $user = new WarehouseManager($_POST['name'] ?? null, $_POST['email'] ?? null, $_POST['password'] ?? null);

  try {
    if ($action == 'create') {
      if ($type == 'staff') $res = $user->create($_POST['extra1'], $_POST['extra2'], $_POST['extra3']);
      elseif ($type == 'supplier') $res = $user->create($_POST['extra1']);
      else $res = $user->create();
      $message = $res ? "✔ تم الإنشاء بنجاح!" : "✘ فشل الإنشاء.";
    } elseif ($action == 'get') {
      $data = $user->getById($_POST['id']);
      $message = $data ? "✔ تم جلب البيانات!" : "✘ غير موجود.";
    } elseif ($action == 'update') {
      if ($type == 'staff') $res = $user->update($_POST['id'], $_POST['extra1'], $_POST['extra2'], $_POST['extra3']);
      elseif ($type == 'supplier') $res = $user->update($_POST['id'], $_POST['extra1']);
      else $res = $user->update($_POST['id']);
      $message = $res ? "✔ تم التحديث بنجاح!" : "✘ فشل التحديث.";
    } elseif ($action == 'delete') {
      $res = $user->delete($_POST['id']);
      $message = $res ? "✔ تم الحذف!" : "✘ فشل الحذف.";
    }
  } catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>Hameed Warehouse - Full Test</title>
  <style>
    body {
      font-family: sans-serif;
      margin: 30px;
      background: #eceff1;
    }

    .box {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      max-width: 600px;
      margin: auto;
    }

    input,
    select {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .btn-group {
      display: flex;
      gap: 10px;
    }

    button {
      flex: 1;
      padding: 10px;
      cursor: pointer;
      border: none;
      border-radius: 5px;
      color: white;
      font-weight: bold;
    }

    .c-btn {
      background: #27ae60;
    }

    .u-btn {
      background: #f39c12;
    }

    .d-btn {
      background: #e74c3c;
    }

    .g-btn {
      background: #3498db;
    }
  </style>
</head>

<body>
  <div class="box">
    <h2>لوحة اختبار نظام حميد للمخازن</h2>
    <p style="color: blue;"><?= $message ?></p>

    <form method="POST">
      <select name="user_type" required>
        <option value="staff">Floor Staff</option>
        <option value="supplier">Supplier</option>
        <option value="manager">Warehouse Manager</option>
      </select>

      <input type="number" name="id" placeholder="ID (لـ Get, Update, Delete)">
      <input type="text" name="name" placeholder="الاسم">
      <input type="email" name="email" placeholder="الإيميل">
      <input type="text" name="password" placeholder="كلمة السر">

      <p><small>* الحقول الإضافية (Staff: start, end, score | Supplier: score)</small></p>
      <input type="text" name="extra1" placeholder="Extra 1 (Shift Start / Perf Score)">
      <input type="text" name="extra2" placeholder="Extra 2 (Shift End)">
      <input type="text" name="extra3" placeholder="Extra 3 (Prod Score)">

      <div class="btn-group">
        <button type="submit" name="action" value="create" class="c-btn">Create</button>
        <button type="submit" name="action" value="get" class="g-btn">Get</button>
        <button type="submit" name="action" value="update" class="u-btn">Update</button>
        <button type="submit" name="action" value="delete" class="d-btn">Delete</button>
      </div>
    </form>

    <?php if ($data): ?>
      <hr>
      <h3>البيانات المسترجعة:</h3>
      <pre><?php print_r($data); ?></pre>
    <?php endif; ?>
  </div>
</body>

</html>