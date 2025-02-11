<?php
$database_file = __DIR__ . '/database.sqlite';

try {
    // إنشاء ملف قاعدة البيانات إذا لم يكن موجوداً
    if (!file_exists($database_file)) {
        if (!touch($database_file)) {
            throw new Exception("فشل في إنشاء ملف قاعدة البيانات");
        }
        if (!chmod($database_file, 0666)) {
            throw new Exception("فشل في تعيين صلاحيات ملف قاعدة البيانات");
        }
        error_log("تم إنشاء ملف قاعدة البيانات بنجاح");
    }

    if (!is_writable($database_file)) {
        throw new Exception("ملف قاعدة البيانات غير قابل للكتابة");
    }

    $db = new PDO('sqlite:' . $database_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("تم الاتصال بقاعدة البيانات بنجاح");
    
    // تفعيل دعم المفاتيح الأجنبية
    $db->exec('PRAGMA foreign_keys = ON');

    // إنشاء جدول الكورسات إذا لم يكن موجوداً
    $db->exec("CREATE TABLE IF NOT EXISTS courses (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT UNIQUE NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    error_log("تم إنشاء جدول الكورسات بنجاح");

    // إنشاء جدول الملاحظات إذا لم يكن موجوداً
    $db->exec("CREATE TABLE IF NOT EXISTS notes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        course_id INTEGER NOT NULL,
        lesson_title TEXT NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (course_id) REFERENCES courses(id)
    )");
    error_log("تم إنشاء جدول الملاحظات بنجاح");

    // إنشاء جدول المواعيد إذا لم يكن موجوداً
    $db->exec("CREATE TABLE IF NOT EXISTS schedules (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        course_id INTEGER NOT NULL,
        scheduled_date DATETIME NOT NULL,
        duration INTEGER,
        description TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (course_id) REFERENCES courses(id)
    )");
    error_log("تم إنشاء جدول المواعيد بنجاح");



} catch (PDOException $e) {
    error_log("خطأ في قاعدة البيانات: " . $e->getMessage());
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
} catch (Exception $e) {
    error_log("خطأ: " . $e->getMessage());
    die($e->getMessage());
}
?>
