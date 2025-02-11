<?php
session_start();
include 'db.php';

$page_title = "دفتر الملاحظات";

$stmt = $db->query("SELECT * FROM courses ORDER BY name ASC");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// جلب الملاحظات مع أسماء الكورسات
$stmt = $db->query("SELECT notes.*, courses.name as course_name 
                    FROM notes 
                    JOIN courses ON notes.course_id = courses.id 
                    ORDER BY notes.created_at DESC");
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// تجميع الملاحظات حسب اسم الكورس
$groupedNotes = [];
foreach ($notes as $note) {
    $groupedNotes[$note['course_name']][] = $note;
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
        .note-card {
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.95);
        }
        .note-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        textarea {
            background: repeating-linear-gradient(
                to bottom,
                #fff,
                #fff 35px,
                #f7fafc 35px,
                #f7fafc 36px
            );
        }
        .floating-note {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .floating-note:hover {
            box-shadow: 0 6px 10px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }


        .modal {
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body class="min-h-screen pb-12">
    <?php include 'header.php'; ?>

    <div class="max-w-6xl mx-auto px-4">


        <!-- نموذج إضافة ملاحظة جديدة -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-center mb-6">أضف ملاحظة جديدة</h2>
            <form action="add_note.php" method="post" class="space-y-6" id="noteForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 mb-2">اختر الكورس</label>
                        <select name="course_id" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                            <?php foreach($courses as $course): ?>
                                <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">عنوان الدرس</label>
                        <input type="text" name="lesson_title" required id="lessonTitle"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent" 
                               placeholder="مثلاً: مقدمة إلى JavaScript">
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">الملاحظات</label>
                        <textarea name="content" required id="noteContent"
                                  class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent" 
                                  rows="5" placeholder="اكتب ملاحظاتك هنا..."></textarea>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition duration-300 transform hover:scale-105">
                        حفظ الملاحظة
                    </button>
                </div>
            </form>
        </div>

        <!-- تحديث زر النافذة المنبثقة -->
        <button onclick="openFloatingNote()" class="floating-note fixed bottom-8 right-8 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg shadow-md transition duration-300 ease-in-out flex items-center space-x-2 z-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span class="font-medium text-sm">ملاحظة سريعة</span>
        </button>


        <!-- النافذة المنبثقة -->
        <div id="floatingNoteModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
            <div id="modalContent" class="relative mx-auto p-5 border shadow-lg rounded-lg bg-white transform transition-all duration-300 min-h-screen md:min-h-0 md:top-20 w-full md:w-2/3">
                <div class="flex flex-col h-full">
                    <div class="flex justify-between items-center border-b pb-3">
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <h3 class="text-xl font-bold text-gray-800">ملاحظة سريعة</h3>
                        </div>
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <div class="text-sm text-gray-500 hidden md:flex items-center space-x-2 space-x-reverse">
                                <span>Ctrl + Enter للحفظ</span>
                                <span class="mx-2">|</span>
                                <span>F11 لملء الشاشة</span>
                                <span class="mx-2">|</span>
                                <span>Esc للإغلاق</span>
                            </div>
                            <button onclick="toggleModalSize()" class="text-gray-600 hover:text-gray-800 transition duration-300 ml-2" id="toggleSizeBtn">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-2V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                </svg>
                            </button>
                            <button onclick="closeFloatingNote()" class="text-gray-600 hover:text-gray-800 transition duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 min-h-0 relative">
                        <textarea id="floatingNoteContent" 
                                  class="absolute inset-0 w-full h-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent resize-none"
                                  placeholder="اكتب ملاحظاتك هنا..."
                                  autofocus></textarea>
                    </div>
                    <div class="flex justify-end space-x-2 space-x-reverse border-t pt-3 mt-3">
                        <button onclick="saveToForm()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition duration-300 ease-in-out">
                            إضافة إلى النموذج
                        </button>
                        <button onclick="closeFloatingNote()" class="bg-gray-500 hover:bg-gray-600 text-white font-medium px-6 py-2 rounded-lg transition duration-300 ease-in-out">
                            إغلاق
                        </button>
                    </div>

                </div>
            </div>
        </div>


        <!-- عرض الملاحظات -->
        <?php if(count($groupedNotes) > 0): ?>
            <?php foreach($groupedNotes as $courseName => $courseNotes): ?>
                <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($courseName) ?></h2>
                        <a href="export_pdf.php?course=<?= urlencode($courseName) ?>" 
                           class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300 flex items-center">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            تصدير PDF
                        </a>
                    </div>
                    <div class="grid gap-6">
                        <?php foreach($courseNotes as $note): ?>
                            <div class="note-card rounded-lg p-6">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-blue-600 mb-2"><?= htmlspecialchars($note['lesson_title']) ?></h3>
                                        <div class="text-gray-700 leading-relaxed mb-4">
                                            <?= nl2br(htmlspecialchars($note['content'])) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php 
                                                $timestamp = strtotime($note['created_at']);
                                                $timeFormatted = date("g:i A", $timestamp);
                                                $timeFormatted = str_replace(["AM", "PM"], ["ص", "م"], $timeFormatted);
                                                $dateFormatted = date("Y-m-d", $timestamp);
                                            ?>
                                            <?= $dateFormatted ?> - <?= $timeFormatted ?>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2 space-x-reverse">
                                        <a href="edit_note.php?id=<?= $note['id'] ?>" 
                                           class="text-blue-600 hover:text-blue-800 transition duration-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="delete_note.php" method="post" class="inline-block" 
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الملاحظة؟');">
                                            <input type="hidden" name="note_id" value="<?= $note['id'] ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800 transition duration-300">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center text-gray-600 mt-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p>لا توجد ملاحظات بعد. ابدأ بإضافة ملاحظتك الأولى!</p>
            </div>
        <?php endif; ?>
    </div> <!-- إغلاق div.max-w-6xl -->

    <script>
        let isFullScreen = false;

        function updateToggleIcon() {
            const toggleBtn = document.getElementById('toggleSizeBtn');
            if (isFullScreen) {
                toggleBtn.innerHTML = `
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14h6m-6 4h6m6-4h4m-4 4h4M4 8V4m0 0h4M4 4l5 5m11-2V4m0 0h-4m4 0l-5 5"/>
                    </svg>
                `;
                toggleBtn.title = 'تصغير النافذة (F11)';
            } else {
                toggleBtn.innerHTML = `
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-2V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                `;
                toggleBtn.title = 'تكبير النافذة (F11)';
            }
        }

        window.openFloatingNote = function() {
            const modal = document.getElementById('floatingNoteModal');
            const textarea = document.getElementById('floatingNoteContent');
            modal.classList.remove('hidden');
            textarea.style.height = isFullScreen ? 'calc(100vh - 200px)' : '400px';
            textarea.focus();
            updateToggleIcon();
        }

        window.closeFloatingNote = function() {
            const modal = document.getElementById('floatingNoteModal');
            modal.classList.add('hidden');
            isFullScreen = false;
            const modalContent = document.getElementById('modalContent');
            modalContent.classList.add('md:top-20', 'md:w-2/3', 'md:min-h-0');
            modalContent.classList.remove('min-h-screen', 'w-full');
            updateToggleIcon();
        }

        window.toggleModalSize = function() {
            const modalContent = document.getElementById('modalContent');
            const textarea = document.getElementById('floatingNoteContent');
            isFullScreen = !isFullScreen;
            
            if (isFullScreen) {
                modalContent.classList.remove('md:top-20', 'md:w-2/3', 'md:min-h-0');
                modalContent.classList.add('min-h-screen', 'w-full');
                textarea.style.height = 'calc(100vh - 200px)';
            } else {
                modalContent.classList.add('md:top-20', 'md:w-2/3', 'md:min-h-0');
                modalContent.classList.remove('min-h-screen', 'w-full');
                textarea.style.height = '400px';
            }
            
            updateToggleIcon();
            textarea.focus();
        }

        window.saveToForm = function() {
            const floatingContent = document.getElementById('floatingNoteContent').value;
            const noteContent = document.getElementById('noteContent');
            
            if (floatingContent.trim()) {
                if (noteContent.value.trim()) {
                    noteContent.value += '\n\n' + floatingContent;
                } else {
                    noteContent.value = floatingContent;
                }
                
                document.getElementById('floatingNoteContent').value = '';
                closeFloatingNote();
                
                // إظهار رسالة نجاح مؤقتة
                const successMessage = document.createElement('div');
                successMessage.className = 'fixed bottom-4 left-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300';
                successMessage.textContent = 'تم نقل الملاحظة بنجاح';
                document.body.appendChild(successMessage);
                
                setTimeout(() => {
                    successMessage.style.opacity = '0';
                    setTimeout(() => successMessage.remove(), 300);
                }, 2000);
            }
        }

        // اختصارات لوحة المفاتيح
        document.addEventListener('keydown', function(e) {
            // Escape لإغلاق النافذة المنبثقة
            if (e.key === 'Escape' && !document.getElementById('floatingNoteModal').classList.contains('hidden')) {
                closeFloatingNote();
            }
            // Ctrl/Cmd + Enter لحفظ الملاحظة
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && !document.getElementById('floatingNoteModal').classList.contains('hidden')) {
                e.preventDefault();
                saveToForm();
            }
            // F11 لتبديل وضع ملء الشاشة
            if (e.key === 'F11' && !document.getElementById('floatingNoteModal').classList.contains('hidden')) {
                e.preventDefault();
                toggleModalSize();
            }
        });


        // إغلاق النافذة المنبثقة عند النقر خارجها
        window.onclick = function(event) {
            const modal = document.getElementById('floatingNoteModal');
            if (event.target == modal) {
                closeFloatingNote();
            }
        }

        // إضافة تلميحات للاختصارات
        const floatingButton = document.querySelector('.floating-note');
        floatingButton.title = 'فتح نافذة الملاحظات السريعة (Alt + N)';
    </script>

</body>
</html>

