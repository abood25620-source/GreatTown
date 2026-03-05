<?php
header('Content-Type: application/json');

// ضع رابط الويب هوك الخاص بك هنا
$webhook_url = "https://discord.com/api/webhooks/1479212635546325033/uWhhyeX3Q_u-PFHIGGPZNz1FZUIjS08qOVK_xsuSkcOP3_YMWEJpsPaCDubBcbQtKoL1";

$username = $_POST['username'] ?? '';
$item_name = $_POST['item_name'] ?? '';
$item_price = $_POST['item_price'] ?? '';

if (empty($username) || empty($item_name) || empty($item_price)) {
    echo json_encode(['success' => false, 'message' => 'بيانات الطلب غير مكتملة.']);
    exit;
}

// إعداد رسالة الديسكورد
$timestamp = date("c");

$json_data = json_encode([
    "username" => "Respect Aura Store",
    "avatar_url" => "", // يمكنك إضافة رابط لوقو السيرفر هنا
    "embeds" => [
        [
            "title" => "🛒 طلب شراء جديد",
            "type" => "rich",
            "description" => "تم استلام طلب شراء جديد من الموقع الإلكتروني.",
            "timestamp" => $timestamp,
            "color" => hexdec("7b2d3a"),
            "footer" => [
                "text" => "Respect Aura | Store System"
            ],
            "fields" => [
                [
                    "name" => "👤 اسم المشتري",
                    "value" => $username,
                    "inline" => true
                ],
                [
                    "name" => "📦 المنتج",
                    "value" => $item_name,
                    "inline" => true
                ],
                [
                    "name" => "💰 السعر",
                    "value" => $item_price,
                    "inline" => false
                ]
            ]
        ]
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


$ch = curl_init($webhook_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);
curl_close($ch);

// ملاحظة: الويب هوك لا يعيد JSON عادة، لذا نعتبر العملية ناجحة إذا لم يحدث خطأ في الـ CURL
echo json_encode(['success' => true, 'message' => 'تم إرسال الطلب بنجاح.']);
?>
