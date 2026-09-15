# NJIK Careers

واجهة طلب توظيف عربية مبنية بـ React وVite، مع API بلغة PHP لحفظ الطلبات وإرسال السيرة الذاتية إلى فريق الموارد البشرية.

## المكونات

- `src/`: واجهة React متعددة الخطوات.
- `api/apply.php`: نقطة استقبال الطلبات بصيغة JSON.
- `database/schema.sql`: إنشاء جدول طلبات التوظيف.
- `config.example.php`: قالب إعدادات MySQL والبريد.
- `vendor/`: مكتبة PHPMailer.
- `careers.php`: النسخة التقليدية المستقلة من النموذج.

## التشغيل المحلي

المتطلبات: Node.js 20 أو أحدث، وPHP 8 مع إضافتي PDO MySQL وmbstring.

1. انسخ `config.example.php` إلى `config.local.php` وضع بيانات بيئة التطوير.
2. أنشئ الجدول بتنفيذ `database/schema.sql` على قاعدة MySQL.
3. شغّل API من جذر المشروع:

   ```bash
   php -S 127.0.0.1:8080
   ```

4. في نافذة طرفية أخرى ثبّت حزم الواجهة وشغّلها:

   ```bash
   npm install
   npm run dev
   ```

5. افتح `http://127.0.0.1:5173`. يمرّر Vite الطلبات التي تبدأ بـ `/api` إلى خادم PHP المحلي.

## بناء الإنتاج

شغّل `npm run build`. ستظهر الواجهة الجاهزة للنشر داخل `dist/`. يجب أن تكون نقطة `/api/apply.php` متاحة على النطاق نفسه، مع بقاء `config.local.php` و`uploads/cv` خارج ملفات Git العامة.

لا تضع بيانات الإنتاج أو السير الذاتية أو السجلات في GitHub.
