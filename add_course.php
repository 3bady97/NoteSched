<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	try {
		$name = trim($_POST['name'] ?? '');
		
		if (empty($name)) {
			throw new Exception("اسم الكورس مطلوب");
		}
		
		// التحقق من عدم وجود كورس بنفس الاسم
		$stmt = $db->prepare("SELECT id FROM courses WHERE name = ?");
		$stmt->execute([$name]);
		if ($stmt->fetch()) {
			throw new Exception("يوجد كورس بنفس الاسم بالفعل");
		}
		
		// إضافة الكورس
		$stmt = $db->prepare("INSERT INTO courses (name) VALUES (?)");
		$stmt->execute([$name]);
		
		$_SESSION['success'] = "تم إضافة الكورس بنجاح";
		
	} catch (Exception $e) {
		$_SESSION['error'] = "حدث خطأ: " . $e->getMessage();
	}
}

header("Location: courses.php");
exit;
?>