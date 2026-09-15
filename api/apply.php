<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once dirname(__DIR__) . '/vendor/autoload.php';

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function respond($status, array $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function cleanField($key, $required = true)
{
    $value = isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
    if ($required && $value === '') {
        throw new RuntimeException('يرجى تعبئة جميع الحقول المطلوبة قبل الإرسال.');
    }
    return preg_replace('/\s+/u', ' ', $value);
}

function allowedField($key, array $allowed)
{
    $value = isset($_POST[$key]) ? (string) $_POST[$key] : '';
    if (!array_key_exists($value, $allowed)) {
        throw new RuntimeException('إحدى القيم المختارة غير صحيحة. يرجى مراجعة الطلب.');
    }
    return $value;
}

function html($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function sendRecruitmentEmail(array $data, $cvPath, array $config)
{
    $mailConfig = $config['mail'];
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $mailConfig['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $mailConfig['username'];
        $mail->Password = $mailConfig['password'];
        $mail->SMTPSecure = $mailConfig['encryption'];
        $mail->Port = $mailConfig['port'];
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($mailConfig['from_address'], $mailConfig['from_name']);
        $mail->addAddress($mailConfig['to_address'], $mailConfig['to_name']);
        $mail->addAttachment($cvPath, 'CV_' . $data['full_name'] . '.pdf');

        if (!empty($mailConfig['debug'])) {
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = static function ($line, $level) {
                error_log("SMTP[$level] $line");
            };
        }

        $safe = array_map('html', $data);
        $mail->isHTML(true);
        $mail->Subject = 'طلب توظيف جديد - ' . $data['full_name'];
        $mail->Body = "<html dir='rtl' lang='ar'><body style='margin:0;background:#f4f3f0;font-family:Arial,sans-serif;color:#202020'>
            <div style='max-width:680px;margin:24px auto;background:#fff;border-top:6px solid #f26522'>
              <div style='padding:28px 32px;background:#191919;color:#fff'><div style='color:#f26522;font-weight:700'>نجيك | الموارد البشرية</div><h2>طلب توظيف جديد</h2></div>
              <div style='padding:28px 32px'>
                <h3 style='border-right:4px solid #f26522;padding-right:10px'>البيانات الشخصية</h3>
                <p><b>الاسم:</b> {$safe['full_name']}</p><p><b>البريد:</b> {$safe['email']}</p><p><b>الجوال:</b> {$safe['phone']}</p>
                <p><b>تاريخ الميلاد:</b> {$safe['birth_date']}</p><p><b>المدينة:</b> {$safe['city']}</p><p><b>الجنسية:</b> {$safe['nationality']}</p>
                <h3 style='border-right:4px solid #f26522;padding-right:10px;margin-top:28px'>الوظيفة والخبرة</h3>
                <p><b>الوظيفة المطلوبة:</b> {$safe['position_label']}</p><p><b>الوظيفة الحالية:</b> {$safe['current_job']}</p>
                <p><b>التفرغ:</b> {$safe['availability_label']}</p><p><b>سبق التقديم:</b> {$safe['applied_before_label']}</p>
                <p><b>المؤهل:</b> {$safe['education']}</p><p><b>التخصص:</b> {$safe['major']}</p><p><b>الخبرة:</b> {$safe['experience_label']}</p>
                <p style='padding:14px;background:#fff2e9'>السيرة الذاتية مرفقة بهذه الرسالة.</p>
              </div>
            </div></body></html>";
        $mail->AltBody = "طلب توظيف جديد\nالاسم: {$data['full_name']}\nالبريد: {$data['email']}\nالوظيفة: {$data['position_label']}";
        $mail->send();
        return true;
    } catch (Throwable $exception) {
        error_log('فشل إرسال إشعار التوظيف: ' . $exception->getMessage());
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    respond(200, ['csrf_token' => $_SESSION['csrf_token']]);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: GET, POST');
    respond(405, ['success' => false, 'message' => 'طريقة الطلب غير مدعومة.']);
}

$positions = [
    'مطور ويب كامل' => 'مطور ويب كامل (Full Stack)', 'مطور واجهات أمامية' => 'مطور واجهات أمامية (Frontend)',
    'مطور خلفي' => 'مطور خلفي (Backend)', 'مطور تطبيقات موبايل' => 'مطور تطبيقات موبايل',
    'مصمم جرافيك' => 'مصمم جرافيك', 'مصمم UI/UX' => 'مصمم تجربة مستخدم (UI/UX)',
    'محاسب' => 'محاسب', 'محاسب قانوني' => 'محاسب قانوني', 'مدير مبيعات' => 'مدير مبيعات',
    'موظف مبيعات' => 'موظف مبيعات', 'مسوق رقمي' => 'مسوق رقمي',
    'مسؤول وسائل التواصل' => 'مسؤول وسائل التواصل الاجتماعي', 'موظف خدمة عملاء' => 'موظف خدمة عملاء',
    'مدير مشروع' => 'مدير مشروع', 'محلل بيانات' => 'محلل بيانات', 'مهندس شبكات' => 'مهندس شبكات',
    'أخصائي أمن معلومات' => 'أخصائي أمن معلومات', 'أخرى' => 'أخرى',
];
$availability = ['full_time' => 'دوام كامل', 'part_time' => 'دوام جزئي', 'freelance' => 'عمل حر', 'remote' => 'عن بُعد'];
$appliedBefore = ['no' => 'لا، هذه المرة الأولى', 'yes' => 'نعم، سبق التقديم'];
$education = array_fill_keys(['ثانوية عامة', 'دبلوم', 'بكالوريوس', 'ماجستير', 'دكتوراه'], true);
$experience = [
    'بدون خبرة' => 'حديث التخرج (بدون خبرة)', 'أقل من سنة' => 'أقل من سنة', '1-2 سنة' => 'سنة إلى سنتين',
    '2-3 سنوات' => 'سنتان إلى 3 سنوات', '3-5 سنوات' => '3 إلى 5 سنوات', '5-7 سنوات' => '5 إلى 7 سنوات',
    '7-10 سنوات' => '7 إلى 10 سنوات', 'أكثر من 10 سنوات' => 'أكثر من 10 سنوات',
];

$savedPath = null;
try {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string) $_POST['csrf_token'])) {
        throw new RuntimeException('انتهت صلاحية الجلسة. حدّث الصفحة ثم أعد المحاولة.');
    }

    $fullName = cleanField('full_name');
    $email = cleanField('email');
    $phone = cleanField('phone');
    $city = cleanField('city');
    $nationality = cleanField('nationality');
    if (mb_strlen($fullName) < 6 || mb_strlen($fullName) > 120) throw new RuntimeException('يرجى إدخال الاسم الكامل كما يظهر في الهوية.');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('البريد الإلكتروني غير صحيح. يرجى مراجعته.');
    if (!preg_match('/^[0-9+\-\s()]{8,20}$/', $phone)) throw new RuntimeException('رقم الجوال غير صحيح.');

    $day = filter_input(INPUT_POST, 'birth_day', FILTER_VALIDATE_INT);
    $month = filter_input(INPUT_POST, 'birth_month', FILTER_VALIDATE_INT);
    $year = filter_input(INPUT_POST, 'birth_year', FILTER_VALIDATE_INT);
    if (!$day || !$month || !$year || !checkdate($month, $day, $year)) throw new RuntimeException('تاريخ الميلاد غير صحيح.');
    $birthDate = sprintf('%04d-%02d-%02d', $year, $month, $day);

    $position = allowedField('position', $positions);
    $availabilityValue = allowedField('availability', $availability);
    $appliedBeforeValue = allowedField('applied_before', $appliedBefore);
    $educationValue = allowedField('education', $education);
    $experienceValue = allowedField('experience', $experience);
    $currentJob = cleanField('current_job', false) ?: 'غير محدد';
    $major = cleanField('major');
    if (!isset($_POST['privacy_consent']) || $_POST['privacy_consent'] !== '1') {
        throw new RuntimeException('يلزم الموافقة على استخدام البيانات لغرض مراجعة طلب التوظيف.');
    }

    if (!isset($_FILES['cv_file']) || $_FILES['cv_file']['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('تعذر رفع السيرة الذاتية.');
    if ($_FILES['cv_file']['size'] > 5 * 1024 * 1024) throw new RuntimeException('حجم السيرة الذاتية يتجاوز 5 ميجابايت.');
    $temporaryFile = (string) $_FILES['cv_file']['tmp_name'];
    $extension = strtolower(pathinfo((string) $_FILES['cv_file']['name'], PATHINFO_EXTENSION));
    $handle = fopen($temporaryFile, 'rb');
    $signature = $handle ? fread($handle, 5) : '';
    if ($handle) fclose($handle);
    if ($extension !== 'pdf' || $signature !== '%PDF-') throw new RuntimeException('السيرة الذاتية يجب أن تكون ملف PDF صالحًا.');

    $configPath = dirname(__DIR__) . '/config.local.php';
    if (!is_file($configPath)) throw new RuntimeException('إعدادات الاستقبال غير متوفرة حاليًا. يرجى المحاولة لاحقًا.');
    $config = require $configPath;
    if (!isset($config['database'], $config['mail'])) throw new RuntimeException('إعدادات النظام غير مكتملة.');

    $uploadDirectory = dirname(__DIR__) . '/uploads/cv/';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) throw new RuntimeException('تعذر تجهيز مساحة حفظ السيرة الذاتية.');
    $savedName = uniqid('cv_', true) . '.pdf';
    $savedPath = $uploadDirectory . $savedName;
    if (!move_uploaded_file($temporaryFile, $savedPath)) throw new RuntimeException('تعذر حفظ السيرة الذاتية.');

    $database = $config['database'];
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $database['host'], $database['port'], $database['name']);
    $pdo = new PDO($dsn, $database['username'], $database['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]);
    $statement = $pdo->prepare('INSERT INTO job_applications
        (full_name, email, phone, birth_date, city, nationality, position, education, major, experience, current_job, cv_file, applied_before, availability, application_date)
        VALUES (:full_name, :email, :phone, :birth_date, :city, :nationality, :position, :education, :major, :experience, :current_job, :cv_file, :applied_before, :availability, NOW())');
    $statement->execute([
        ':full_name' => $fullName, ':email' => $email, ':phone' => $phone, ':birth_date' => $birthDate,
        ':city' => $city, ':nationality' => $nationality, ':position' => $position, ':education' => $educationValue,
        ':major' => $major, ':experience' => $experienceValue, ':current_job' => $currentJob, ':cv_file' => $savedName,
        ':applied_before' => $appliedBeforeValue, ':availability' => $availabilityValue,
    ]);

    $mailSent = sendRecruitmentEmail([
        'full_name' => $fullName, 'email' => $email, 'phone' => $phone, 'birth_date' => $birthDate,
        'city' => $city, 'nationality' => $nationality, 'position_label' => $positions[$position],
        'current_job' => $currentJob, 'availability_label' => $availability[$availabilityValue],
        'applied_before_label' => $appliedBefore[$appliedBeforeValue], 'education' => $educationValue,
        'major' => $major, 'experience_label' => $experience[$experienceValue],
    ], $savedPath, $config);

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    respond(201, [
        'success' => true,
        'message' => $mailSent
            ? 'وصلنا طلبك بنجاح. سيراجعه فريق الموارد البشرية، وسنتواصل معك إذا تطابقت خبراتك مع الفرصة المناسبة.'
            : 'وصلنا طلبك وحُفظ بنجاح. تعذر إرسال الإشعار الداخلي مؤقتًا، لكن طلبك موجود لدى فريق الموارد البشرية.',
        'csrf_token' => $_SESSION['csrf_token'],
    ]);
} catch (PDOException $exception) {
    if ($savedPath && is_file($savedPath)) unlink($savedPath);
    error_log('خطأ قاعدة بيانات التوظيف: ' . $exception->getMessage());
    respond($exception->getCode() === '23000' ? 409 : 500, [
        'success' => false,
        'message' => $exception->getCode() === '23000'
            ? 'يوجد طلب مسجل مسبقًا بهذه البيانات.'
            : 'تعذر تسجيل الطلب الآن بسبب مشكلة تقنية. يرجى المحاولة مرة أخرى لاحقًا.',
    ]);
} catch (Throwable $exception) {
    if ($savedPath && is_file($savedPath)) unlink($savedPath);
    respond(422, ['success' => false, 'message' => $exception->getMessage()]);
}
