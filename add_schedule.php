<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        $course_id = $_POST['course_id'] ?? null;
        $scheduled_date = trim($_POST['scheduled_date'] ?? '');
        $duration_hours = isset($_POST['duration_hours']) ? (int)$_POST['duration_hours'] : 0;
        $duration_minutes = isset($_POST['duration_minutes']) ? (int)$_POST['duration_minutes'] : 0;
        $description = trim($_POST['description'] ?? '');
        
        // حساب إجمالي الدقائق
        $totalDuration = ($duration_hours * 60) + $duration_minutes;
        
        // التحقق من البيانات المطلوبة
        if (empty($course_id)) {
            throw new Exception("الكورس مطلوب");
        }
        if (empty($scheduled_date)) {
            throw new Exception("تاريخ ووقت البدء مطلوب");
        }
        
        // التحقق من وجود الكورس
        $stmt = $db->prepare("SELECT id FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        if (!$stmt->fetch()) {
            throw new Exception("الكورس غير موجود");
        }
        
        // إضافة الموعد
        $stmt = $db->prepare("INSERT INTO schedules (course_id, scheduled_date, duration, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$course_id, $scheduled_date, $totalDuration, $description]);
        
        $db->commit();
        $_SESSION['success'] = "تم إضافة الموعد بنجاح";
        
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'] = "حدث خطأ: " . $e->getMessage();
    }
}

header("Location: schedule.php");
exit;
?>

