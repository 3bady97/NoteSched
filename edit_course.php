<?php
session_start();
include 'db.php';

// جلب بيانات الكورس المراد تعديله
if (isset($_GET['id'])) {
	$course_id = (int)$_GET['id'];
	try {
		$stmt = $db->prepare("SELECT * FROM courses WHERE id = ?");
		$stmt->execute([$course_id]);
		$course = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if (!$course) {
			$_SESSION['error'] = "الكورس غير موجود";
			header("Location: courses.php");
			exit;
		}
		
	} catch (Exception $e) {
		$_SESSION['error'] = "حدث خطأ: " . $e->getMessage();
		header("Location: courses.php");
		exit;
	}
} else {
	header("Location: courses.php");
	exit;
}

// معالجة تحديث البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	try {
		$name = trim($_POST['name'] ?? '');
		
		if (empty($name)) {
			throw new Exception("اسم الكورس مطلوب");
		}
		
		// التحقق من عدم وجود كورس آخر بنفس الاسم
		$stmt = $db->prepare("SELECT id FROM courses WHERE name = ? AND id != ?");
		$stmt->execute([$name, $course_id]);
		if ($stmt->fetch()) {
			throw new Exception("يوجد كورس آخر بنفس الاسم");
		}
		
		// تحديث الكورس
		$stmt = $db->prepare("UPDATE courses SET name = ? WHERE id = ?");
		$stmt->execute([$name, $course_id]);
		
		$_SESSION['success'] = "تم تحديث الكورس بنجاح";
		header("Location: courses.php");
		exit;
		
	} catch (Exception $e) {
		$_SESSION['error'] = "حدث خطأ: " . $e->getMessage();
	}
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>تعديل كورس</title>
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
					<a href="courses.php" class="text-gray-800 hover:text-blue-600 transition duration-300">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
						</svg>
					</a>
					<span class="text-2xl font-bold text-gray-800">تعديل كورس</span>
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
					<label class="block text-gray-700 mb-2">اسم الكورس</label>
					<input type="text" name="name" required 
						   value="<?= htmlspecialchars($course['name']) ?>"
						   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
				</div>

				<div class="flex justify-between">
					<button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
						حفظ التغييرات
					</button>
					<a href="courses.php" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-300">
						إلغاء
					</a>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
?>