<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note_id'])) {
	try {
		$db->beginTransaction();
		
		$note_id = (int)$_POST['note_id'];
		
		// التحقق من وجود الملاحظة
		$stmt = $db->prepare("SELECT id FROM notes WHERE id = ?");
		$stmt->execute([$note_id]);
		if (!$stmt->fetch()) {
			throw new Exception("الملاحظة غير موجودة");
		}
		
		// حذف الملاحظة
		$stmt = $db->prepare("DELETE FROM notes WHERE id = ?");
		$stmt->execute([$note_id]);
		
		$db->commit();
		$_SESSION['success'] = "تم حذف الملاحظة بنجاح";
		
	} catch (Exception $e) {
		$db->rollBack();
		$_SESSION['error'] = "حدث خطأ: " . $e->getMessage();
	}
}

header("Location: notebook.php");
exit;
?>