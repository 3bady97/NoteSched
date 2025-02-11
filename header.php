<nav class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg mb-8">
	<div class="max-w-6xl mx-auto px-4">
		<div class="flex justify-between items-center py-4">
			<div class="flex items-center space-x-4 space-x-reverse">
				<a href="index.php" class="text-white hover:text-gray-200 transition duration-300">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
					</svg>
				</a>
				<span class="text-2xl font-bold"><?= $page_title ?? 'نظام إدارة الدورة' ?></span>
			</div>
			<div class="flex items-center space-x-6 space-x-reverse">
				<a href="courses.php" class="flex items-center text-white hover:text-gray-200 transition duration-300">
					<svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
					</svg>
					الكورسات
				</a>
				<a href="notebook.php" class="flex items-center text-white hover:text-gray-200 transition duration-300">
					<svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
					</svg>
					الملاحظات
				</a>
				<a href="schedule.php" class="flex items-center text-white hover:text-gray-200 transition duration-300">
					<svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
					</svg>
					الجدول الزمني
				</a>
			</div>
		</div>
	</div>
</nav>

<?php if(isset($success_message)): ?>
	<div class="max-w-6xl mx-auto px-4 mb-6">
		<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
			<?= htmlspecialchars($success_message) ?>
		</div>
	</div>
<?php endif; ?>

<?php if(isset($error_message)): ?>
	<div class="max-w-6xl mx-auto px-4 mb-6">
		<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
			<?= htmlspecialchars($error_message) ?>
		</div>
	</div>
<?php endif; ?>