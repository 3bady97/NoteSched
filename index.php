<?php
include 'db.php';

$page_title = "نظام إدارة الكورسات";
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
      min-height: 100vh;
    }
    .card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

  <div class="container mx-auto px-4 py-8">
    <header class="text-center mb-12">
      <h1 class="text-4xl font-bold text-gray-800 mb-4">نظام إدارة الكورس</h1>
      <p class="text-gray-600">نظام متكامل لإدارة الدورات والملاحظات والجداول الزمنية</p>
    </header>

    <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
      
    <div class="card bg-white rounded-lg shadow-lg overflow-hidden">
      <div class="p-6">
        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-4 mx-auto">
        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        </div>
        <h2 class="text-2xl font-bold text-center mb-4">إدارة الكورسات</h2>
        <p class="text-gray-600 text-center mb-6">أضف وعدل وأدر الكورسات الخاصة بك</p>
        <a href="courses.php" class="block bg-purple-600 text-white text-center py-3 px-6 rounded-lg hover:bg-purple-700 transition duration-300">
        إدارة الكورسات
        </a>
      </div>
      </div>
      
      <div class="card bg-white rounded-lg shadow-lg overflow-hidden">
      <div class="p-6">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4 mx-auto">
        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        </div>
        <h2 class="text-2xl font-bold text-center mb-4">دفتر الملاحظات</h2>
        <p class="text-gray-600 text-center mb-6">سجل ملاحظاتك وأفكارك لكل درس في الكورس</p>
        <a href="notebook.php" class="block bg-blue-600 text-white text-center py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-300">
        فتح دفتر الملاحظات
        </a>
      </div>
      </div>

      <div class="card bg-white rounded-lg shadow-lg overflow-hidden">
      <div class="p-6">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4 mx-auto">
        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        </div>
        <h2 class="text-2xl font-bold text-center mb-4">جدولة الكورس</h2>
        <p class="text-gray-600 text-center mb-6">نظم مواعيد دروسك وتتبع تقدمك في الكورس</p>
        <a href="schedule.php" class="block bg-green-600 text-white text-center py-3 px-6 rounded-lg hover:bg-green-700 transition duration-300">
        فتح الجدول الزمني
        </a>
      </div>
      </div>

    </div>
  </div>
</body>
</html>
