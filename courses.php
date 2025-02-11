<?php
session_start();
include 'db.php';

$page_title = "إدارة الكورسات";

// جلب قائمة الكورسات مع عدد الملاحظات والمواعيد لكل كورس
$stmt = $db->query("SELECT 
						courses.*, 
						COUNT(DISTINCT notes.id) as notes_count,
						COUNT(DISTINCT schedules.id) as schedules_count
					FROM courses 
					LEFT JOIN notes ON courses.id = notes.course_id
					LEFT JOIN schedules ON courses.id = schedules.course_id
					GROUP BY courses.id
					ORDER BY courses.name ASC");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $page_title ?></title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

	<style>
		body {
			font-family: 'Cairo', sans-serif;
			background: linear-gradient(135deg, #f6f8fc 0%, #e9f0f7 100%);
		}
		.course-card {
			transition: all 0.3s ease;
		}
		.course-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 10px 20px rgba(0,0,0,0.1);
		}
	</style>
</head>
<body class="min-h-screen pb-12">
	<?php include 'header.php'; ?>

	<div class="max-w-6xl mx-auto px-4">


		<!-- نموذج إضافة كورس جديد -->
		<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
			<h2 class="text-2xl font-bold text-center mb-6">إضافة كورس جديد</h2>
			<form action="add_course.php" method="post" class="space-y-6">
				<div>
					<label class="block text-gray-700 mb-2">اسم الكورس</label>
					<input type="text" name="name" required 
						   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
						   placeholder="أدخل اسم الكورس">
				</div>
				<div class="text-center">
					<button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
						إضافة الكورس
					</button>
				</div>
			</form>
		</div>

		<!-- عرض الكورسات -->
		<div class="grid md:grid-cols-2 gap-6">
			<?php foreach($courses as $course): ?>
				<div class="course-card bg-white rounded-lg shadow-lg p-6">
					<div class="flex justify-between items-start">
						<div>
							<h3 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($course['name']) ?></h3>
							<div class="flex space-x-4 space-x-reverse text-sm text-gray-600">
								<span>
									<svg class="w-5 h-5 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
									</svg>
									<?= $course['notes_count'] ?> ملاحظة
								</span>
								<span>
									<svg class="w-5 h-5 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
									</svg>
									<?= $course['schedules_count'] ?> موعد
								</span>
							</div>
						</div>
						<div class="flex space-x-2 space-x-reverse">
							<a href="edit_course.php?id=<?= $course['id'] ?>" 
							   class="text-blue-600 hover:text-blue-800 transition duration-300">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
								</svg>
							</a>
							<?php if($course['notes_count'] == 0 && $course['schedules_count'] == 0): ?>
								<form action="delete_course.php" method="post" class="inline-block" 
									  onsubmit="return confirm('هل أنت متأكد من حذف هذا الكورس؟');">
									<input type="hidden" name="course_id" value="<?= $course['id'] ?>">
									<button type="submit" class="text-red-600 hover:text-red-800 transition duration-300">
										<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
										</svg>
									</button>
								</form>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</body>
</html>