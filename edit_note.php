<?php
session_start();
include 'db.php';

// جلب بيانات الملاحظة المراد تعديلها
if (isset($_GET['id'])) {
	$note_id = (int)$_GET['id'];
	try {
		$stmt = $db->prepare("SELECT notes.*, courses.name as course_name 
							 FROM notes 
							 JOIN courses ON notes.course_id = courses.id 
							 WHERE notes.id = ?");
		$stmt->execute([$note_id]);
		$note = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if (!$note) {
			$_SESSION['error'] = "الملاحظة غير موجودة";
			header("Location: notebook.php");
			exit;
		}

		// جلب قائمة الكورسات
		$stmt = $db->query("SELECT * FROM courses ORDER BY name ASC");
		$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
	} catch (Exception $e) {
		$_SESSION['error'] = "حدث خطأ: " . $e->getMessage();
		header("Location: notebook.php");
		exit;
	}
} else {
	header("Location: notebook.php");
	exit;
}

// معالجة تحديث البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	try {
		$db->beginTransaction();
		
		$lesson_title = trim($_POST['lesson_title'] ?? '');
		$content = trim($_POST['content'] ?? '');
		$course_id = $_POST['course_id'] ?? null;
		
		// التحقق من البيانات
		if (empty($lesson_title)) {
			throw new Exception("عنوان الدرس مطلوب");
		}
		if (empty($content)) {
			throw new Exception("محتوى الملاحظة مطلوب");
		}
		
		// تحديث الملاحظة
		$stmt = $db->prepare("UPDATE notes SET course_id = ?, lesson_title = ?, content = ? WHERE id = ?");
		$stmt->execute([$course_id, $lesson_title, $content, $note_id]);
		
		$db->commit();
		$_SESSION['success'] = "تم تحديث الملاحظة بنجاح";
		header("Location: notebook.php");
		exit;
		
	} catch (Exception $e) {
		$db->rollBack();
		$_SESSION['error'] = "حدث خطأ: " . $e->getMessage();
	}
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>تعديل ملاحظة</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
	<style>
		body {
			font-family: 'Cairo', sans-serif;
			background: linear-gradient(135deg, #f6f8fc 0%, #e9f0f7 100%);
		}
	</style>
</head>
<body class="min-h-screen pb-12">
	<nav class="bg-white shadow-lg mb-8">
		<div class="max-w-6xl mx-auto px-4">
			<div class="flex justify-between items-center py-4">
				<div class="flex items-center space-x-4 space-x-reverse">
					<a href="notebook.php" class="text-gray-800 hover:text-blue-600 transition duration-300">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
						</svg>
					</a>
					<span class="text-2xl font-bold text-gray-800">تعديل ملاحظة</span>
				</div>
			</div>
		</div>
	</nav>

	<div class="max-w-4xl mx-auto px-4">
		<?php if(isset($_SESSION['error'])): ?>
			<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
				<?= htmlspecialchars($_SESSION['error']) ?>
			</div>
			<?php unset($_SESSION['error']); ?>
		<?php endif; ?>

		<div class="bg-white rounded-lg shadow-lg p-6">
			<form method="post" class="space-y-6">
				<div>
					<label class="block text-gray-700 mb-2">الكورس</label>
					<select name="course_id" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
						<?php foreach($courses as $course): ?>
							<option value="<?= $course['id'] ?>" <?= $course['id'] == $note['course_id'] ? 'selected' : '' ?>>
								<?= htmlspecialchars($course['name']) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div>
					<label class="block text-gray-700 mb-2">عنوان الدرس</label>
					<input type="text" name="lesson_title" required 
						   value="<?= htmlspecialchars($note['lesson_title']) ?>"
						   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
				</div>

				<div>
					<label class="block text-gray-700 mb-2">المحتوى</label>
					<textarea name="content" required rows="6"
							  class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent"><?= htmlspecialchars($note['content']) ?></textarea>
				</div>

				<div class="flex justify-between">
					<button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
						حفظ التغييرات
					</button>
					<a href="notebook.php" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-300">
						إلغاء
					</a>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
?>