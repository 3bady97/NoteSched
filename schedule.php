<?php
session_start();
include 'db.php';

$page_title = "الجدول الزمني";

// جلب قائمة الكورسات
$stmt = $db->query("SELECT * FROM courses ORDER BY name ASC");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// جلب المواعيد مع أسماء الكورسات
$stmt = $db->query("SELECT schedules.*, courses.name as course_name 
                    FROM schedules 
                    JOIN courses ON schedules.course_id = courses.id 
                    ORDER BY schedules.scheduled_date ASC");
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// تجميع المواعيد بحسب اسم الكورس
$groupedSchedules = [];
foreach ($schedules as $schedule) {
    $groupedSchedules[$schedule['course_name']][] = $schedule;
}
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
    /* إنشاء خط زمني عمودي على الجانب الأيمن */
    .timeline {
      position: relative;
      padding-right: 40px; /* مساحة للعلامة والخط */
    }
    .timeline::before {
      content: "";
      position: absolute;
      top: 0;
      bottom: 0;
      right: 20px;
      width: 2px;
      background: #68D391; /* لون أخضر مميز */
    }
    /* لكل عنصر في الـ timeline، تُضاف نقطة marker على الجانب الأيمن */
    .timeline-item {
      position: relative;
      padding-right: 40px;
    }
    .timeline-item::before {
      content: "";
      position: absolute;
      top: 0.5rem;
      right: 12px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #68D391;
    }
  </style>
</head>
<body class="min-h-screen pb-12">
  <?php include 'header.php'; ?>

  <div class="max-w-4xl mx-auto px-4">
    <!-- نموذج إضافة موعد جديد -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
      <h2 class="text-2xl font-bold text-center mb-4">أضف موعدًا جديدًا</h2>
      <form action="add_schedule.php" method="post" class="space-y-4">
        <div>
          <label class="block text-gray-700 mb-2">اختر الكورس</label>
          <select name="course_id" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring focus:ring-green-300">
            <?php foreach ($courses as $course): ?>
              <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-gray-700 mb-2">تاريخ ووقت البدء</label>
          <input type="datetime-local" name="scheduled_date" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring focus:ring-green-300">
        </div>
        <div class="flex space-x-4 space-x-reverse">
          <div class="w-1/2">
            <label class="block text-gray-700 mb-2">الساعات</label>
            <input type="number" name="duration_hours" min="0" placeholder="0" class="w-full border border-gray-300 rounded-lg p-3 focus:ring focus:ring-green-300">
          </div>
          <div class="w-1/2">
            <label class="block text-gray-700 mb-2">الدقائق</label>
            <input type="number" name="duration_minutes" min="0" max="59" placeholder="0" class="w-full border border-gray-300 rounded-lg p-3 focus:ring focus:ring-green-300">
          </div>
        </div>
        <div>
          <label class="block text-gray-700 mb-2">ملاحظات إضافية</label>
          <textarea name="description" rows="3" placeholder="اكتب ملاحظات حول الموعد" class="w-full border border-gray-300 rounded-lg p-3 focus:ring focus:ring-green-300"></textarea>
        </div>
        <div class="text-center">
          <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded hover:bg-green-600 transition">
            حفظ الموعد
          </button>
        </div>
      </form>
    </div>

    <!-- عرض الجدول الزمني لكل كورس داخل بطاقة منفصلة -->
    <?php if (!empty($groupedSchedules)): ?>
      <?php foreach ($groupedSchedules as $courseName => $courseSchedules): ?>
        <section class="bg-white rounded-lg shadow p-6 mb-8">
          <h3 class="text-2xl font-bold mb-4 text-right"><?= htmlspecialchars($courseName) ?></h3>
          <div class="timeline">
            <ul class="space-y-8">
              <?php foreach ($courseSchedules as $schedule): ?>
                <li class="timeline-item">
                  <div class="bg-gray-50 rounded-lg shadow p-4 text-right">
                    <div class="text-sm text-gray-600">
                      <?php
                        $dateFormatted = date("Y-m-d", strtotime($schedule['scheduled_date']));
                        $timeFormatted = date("g:i A", strtotime($schedule['scheduled_date']));
                        $timeFormatted = str_replace(["AM", "PM"], ["ص", "م"], $timeFormatted);
                        echo "$dateFormatted - $timeFormatted";
                      ?>
                    </div>
                    <div class="mt-1 text-sm text-gray-600">
                      <?php
                        $totalMinutes = isset($schedule['duration']) 
                                         ? (int)$schedule['duration'] 
                                         : (((int)$schedule['duration_hours'] ?? 0) * 60 + ((int)$schedule['duration_minutes'] ?? 0));
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;
                        $durationStr = "";
                        if ($hours > 0) {
                          $durationStr .= "$hours ساعة";
                        }
                        if ($minutes > 0) {
                          if ($durationStr != "") {
                            $durationStr .= " و ";
                          }
                          $durationStr .= "$minutes دقيقة";
                        }
                        if ($durationStr == "") {
                          $durationStr = "0 دقيقة";
                        }
                        echo $durationStr;
                      ?>
                    </div>
                    <?php if (!empty($schedule['description'])): ?>
                      <div class="mt-2 text-gray-700">
                        <?= htmlspecialchars($schedule['description']) ?>
                      </div>
                    <?php endif; ?>
                    <!-- أزرار التعديل والحذف -->
                    <div class="mt-4 flex justify-end space-x-2 space-x-reverse">
                      <a href="edit_schedule.php?id=<?= $schedule['id'] ?>" class="flex items-center text-blue-500 hover:text-blue-700">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        تعديل
                      </a>
                      <form action="delete_schedule.php" method="post" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموعد؟');">
                        <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">
                        <button type="submit" class="flex items-center text-red-500 hover:text-red-700">
                          <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3"/>
                          </svg>
                          حذف
                        </button>
                      </form>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </section>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="text-center text-gray-600 mt-12">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p>لا توجد مواعيد مسجلة حتى الآن.</p>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
