<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly, they'll break JSON
header('Content-Type: application/json');

$type = $_POST['type'] ?? '';
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$email = trim($_POST['email'] ?? '');

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'يرجى ملء جميع الحقول المطلوبة.']);
    exit;
}

$file = 'users.txt';

// Ensure the file exists
if (!file_exists($file)) {
    file_put_contents($file, "# Respect Aura User Database\n# Format: username|password_hash|email|registration_date\n");
}

if ($type === 'register') {
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني مطلوب للتسجيل.']);
        exit;
    }

    // Check if user already exists
    $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($users as $line) {
        if (strpos($line, '#') === 0) continue; // Skip comments
        $data = explode('|', $line);
        if (isset($data[0]) && strtolower($data[0]) === strtolower($username)) {
            echo json_encode(['success' => false, 'message' => 'اسم المستخدم موجود مسبقاً.']);
            exit;
        }
    }

    // Save user data
    $logEntry = $username . "|" . password_hash($password, PASSWORD_DEFAULT) . "|" . $email . "|" . date('Y-m-d H:i:s') . PHP_EOL;
    if (file_put_contents($file, $logEntry, FILE_APPEND) === false) {
        echo json_encode(['success' => false, 'message' => 'فشل في حفظ البيانات. يرجى التحقق من صلاحيات الملف.']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'تم إنشاء الحساب بنجاح!']);

} elseif ($type === 'login') {
    $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $found = false;
    foreach ($users as $line) {
        if (strpos($line, '#') === 0) continue; // Skip comments
        $data = explode('|', $line);
        if (isset($data[0]) && strtolower($data[0]) === strtolower($username)) {
            if (isset($data[1]) && password_verify($password, $data[1])) {
                $found = true;
                break;
            }
        }
    }

    if ($found) {
        echo json_encode(['success' => true, 'message' => 'تم تسجيل الدخول بنجاح!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'اسم المستخدم أو كلمة المرور غير صحيحة.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طلب غير صالح.']);
}
?>
