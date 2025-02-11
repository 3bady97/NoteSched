<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/vendor/autoload.php';
include 'db.php';

use Mpdf\Mpdf;

if (!isset($_GET['course'])) {
    die("اسم الكورس غير موجود.");
}

$course_name = urldecode($_GET['course']);

// جلب البيانات من قاعدة البيانات مع اسم الكورس
try {
    // البحث عن معرف الكورس أولاً
    $stmt = $db->prepare("SELECT id FROM courses WHERE name = ?");
    $stmt->execute([$course_name]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        die("الكورس غير موجود.");
    }

    // جلب الملاحظات المرتبطة بالكورس
    $stmt = $db->prepare("SELECT notes.*, courses.name as course_name 
                         FROM notes 
                         JOIN courses ON notes.course_id = courses.id 
                         WHERE course_id = ? 
                         ORDER BY notes.created_at ASC");
    $stmt->execute([$course['id']]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("خطأ في قاعدة البيانات: " . $e->getMessage());
}

// إعداد محتوى HTML
$html = '
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>ملاحظات ' . htmlspecialchars($course_name, ENT_QUOTES, 'UTF-8') . '</title>
    <style>
        body {
            line-height: 1.8;
            margin: 20px;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .note {
            margin-bottom: 25px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .lesson-title {
            color: #e74c3c;
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .note-content {
            margin: 15px 0;
            text-align: justify;
        }
        .note-date {
            color: #7f8c8d;
            font-size: 0.9em;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>ملاحظات  ' . htmlspecialchars($course_name, ENT_QUOTES, 'UTF-8') . '</h1>';

foreach ($notes as $note) {
    $timestamp = strtotime($note['created_at']);
    $date = date('Y-m-d', $timestamp);
    $time = date('h:i A', $timestamp);
    $time = str_replace(['AM', 'PM'], ['ص', 'م'], $time);
    
    $html .= '
    <div class="note">
        <div class="lesson-title">' . htmlspecialchars($note['lesson_title'], ENT_QUOTES, 'UTF-8') . '</div>
        <div class="note-content">' . nl2br(htmlspecialchars($note['content'], ENT_QUOTES, 'UTF-8')) . '</div>
        <div class="note-date">' . $date . ' - ' . $time . '</div>
    </div>';
}

$html .= '</body></html>';

// إعداد mPDF
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'direction' => 'rtl',
    'fontDir' => array_merge(
        [__DIR__ . '/custom-fonts'],
        [__DIR__ . '/vendor/mpdf/mpdf/ttfonts']
    ),
    'fontdata' => [
        'cairo' => [
            'R' => 'Cairo-Regular.ttf',
            'B' => 'Cairo-Bold.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75
        ],
    ],
    'default_font' => 'cairo'
]);

$mpdf->WriteHTML($html);
$mpdf->Output('notes_' . $course_name . '.pdf', 'D');
exit;
?>