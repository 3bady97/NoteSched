<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
	try {
		$db->beginTransaction();
		
		$course_id = (int)$_POST['course_id'];
		
		// التحقق من وجود الكورس
		$stmt = $db->prepare("SELECT id FROM courses WHERE id = ?");
		$stmt->execute([$course_id]);
		if (!$stmt->fetch()) {
			throw new Exception("الكورس غير موجود");
		}
		
		// التحقق من عدم وجود ملاحظات مرتبطة بالكورس
		$stmt = $db->prepare("SELECT COUNT(*) FROM notes WHERE course_id = ?");
		$stmt->execute([$course_id]);
		if ($stmt->fetchColumn() > 0) {
			throw new Exception("لا يمكن حذف الكورس لوجود ملاحظات مرتبطة به");
		}
		
		// التحقق من عدم وجود مواعيد مرتبطة بالكورس
		$stmt = $db->prepare("SELECT COUNT(*) FROM schedules WHERE course_id = ?");
		$stmt->execute([$course_id]);
		if ($stmt->fetchColumn() > 0) {
			throw new Exception("لا يمكن حذف الكورس لوجود مواعيد مرتبطة به");
		}
		
		// حذف الكورس
		$stmt = $db->prepare("DELETE FROM courses WHERE id = ?");
		$stmt->execute([$course_id]);
		
		$db->commit();
		$_SESSION['success'] = "تم حذف الكورس بنجاح";
		
	} catch (Exception $e) {
		$db->rollBack();
		$_SESSION['error'] = "حدث خطأ: " . $e->getMessage();
	}
}

header("Location: courses.php");
exit;
?>