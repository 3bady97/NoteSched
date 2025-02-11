<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        $course_id = $_POST['course_id'] ?? null;
        $lesson_title = trim($_POST['lesson_title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        
        // التحقق من البيانات المطلوبة
        if (empty($course_id)) {
            throw new Exception("الكورس مطلوب");
        }
        if (empty($lesson_title)) {
            throw new Exception("عنوان الدرس مطلوب");
        }
        if (empty($content)) {
            throw new Exception("محتوى الملاحظة مطلوب");
        }
        
        // التحقق من وجود الكورس
        $stmt = $db->prepare("SELECT id FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        if (!$stmt->fetch()) {
            throw new Exception("الكورس غير موجود");
        }
        
        // إضافة الملاحظة
        $stmt = $db->prepare("INSERT INTO notes (course_id, lesson_title, content) VALUES (?, ?, ?)");
        $stmt->execute([$course_id, $lesson_title, $content]);
        
        $db->commit();
        $_SESSION['success'] = "تم إضافة الملاحظة بنجاح";
        
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'] = "حدث خطأ: " . $e->getMessage();
    }
}

header("Location: notebook.php");
exit;
?>

