<?php
header('Content-Type: application/json');

$type = $_POST['type'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$email = $_POST['email'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'يرجى ملء جميع الحقول المطلوبة.']);
    exit;
}

$file = 'users.txt';

if ($type === 'register') {
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني مطلوب للتسجيل.']);
        exit;
    }

    // Check if user already exists
    if (file_exists($file)) {
        $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($users as $line) {
            $data = explode('|', $line);
            if ($data[0] === $username) {
                echo json_encode(['success' => false, 'message' => 'اسم المستخدم موجود مسبقاً.']);
                exit;
            }
        }
    }

    // Save user data
    $logEntry = $username . "|" . password_hash($password, PASSWORD_DEFAULT) . "|" . $email . "|" . date('Y-m-d H:i:s') . PHP_EOL;
    file_put_contents($file, $logEntry, FILE_APPEND);

    echo json_encode(['success' => true, 'message' => 'تم إنشاء الحساب بنجاح!']);

} elseif ($type === 'login') {
    if (!file_exists($file)) {
        echo json_encode(['success' => false, 'message' => 'لا يوجد مستخدمين مسجلين بعد.']);
        exit;
    }

    $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $found = false;
    foreach ($users as $line) {
        $data = explode('|', $line);
        if ($data[0] === $username) {
            if (password_verify($password, $data[1])) {
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
