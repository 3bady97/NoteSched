<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_id'])) {
	try {
		$db->beginTransaction();
		
		$schedule_id = (int)$_POST['schedule_id'];
		
		// التحقق من وجود الموعد
		$stmt = $db->prepare("SELECT id FROM schedules WHERE id = ?");
		$stmt->execute([$schedule_id]);
		if (!$stmt->fetch()) {
			throw new Exception("الموعد غير موجود");
		}
		
		// حذف الموعد
		$stmt = $db->prepare("DELETE FROM schedules WHERE id = ?");
		$stmt->execute([$schedule_id]);
		
		$db->commit();
		$_SESSION['success'] = "تم حذف الموعد بنجاح";
		
	} catch (Exception $e) {
		$db->rollBack();
		$_SESSION['error'] = "حدث خطأ: " . $e->getMessage();
	}
}

header("Location: schedule.php");
exit;
?>