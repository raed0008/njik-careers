<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/vendor/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$positions = [
    'مطور ويب كامل' => 'مطور ويب كامل (Full Stack)',
    'مطور واجهات أمامية' => 'مطور واجهات أمامية (Frontend)',
    'مطور خلفي' => 'مطور خلفي (Backend)',
    'مطور تطبيقات موبايل' => 'مطور تطبيقات موبايل',
    'مصمم جرافيك' => 'مصمم جرافيك',
    'مصمم UI/UX' => 'مصمم تجربة مستخدم (UI/UX)',
    'محاسب' => 'محاسب',
    'محاسب قانوني' => 'محاسب قانوني',
    'مدير مبيعات' => 'مدير مبيعات',
    'موظف مبيعات' => 'موظف مبيعات',
    'مسوق رقمي' => 'مسوق رقمي',
    'مسؤول وسائل التواصل' => 'مسؤول وسائل التواصل الاجتماعي',
    'موظف خدمة عملاء' => 'موظف خدمة عملاء',
    'مدير مشروع' => 'مدير مشروع',
    'محلل بيانات' => 'محلل بيانات',
    'مهندس شبكات' => 'مهندس شبكات',
    'أخصائي أمن معلومات' => 'أخصائي أمن معلومات',
    'أخرى' => 'أخرى',
];
$availabilityOptions = [
    'full_time' => 'دوام كامل',
    'part_time' => 'دوام جزئي',
    'freelance' => 'عمل حر',
    'remote' => 'عن بُعد',
];
$appliedBeforeOptions = ['no' => 'لا، هذه المرة الأولى', 'yes' => 'نعم، سبق التقديم'];
$educationOptions = array_combine(
    ['ثانوية عامة', 'دبلوم', 'بكالوريوس', 'ماجستير', 'دكتوراه'],
    ['ثانوية عامة', 'دبلوم', 'بكالوريوس', 'ماجستير', 'دكتوراه']
);
$experienceOptions = [
    'بدون خبرة' => 'حديث التخرج (بدون خبرة)',
    'أقل من سنة' => 'أقل من سنة',
    '1-2 سنة' => 'سنة إلى سنتين',
    '2-3 سنوات' => 'سنتان إلى 3 سنوات',
    '3-5 سنوات' => '3 إلى 5 سنوات',
    '5-7 سنوات' => '5 إلى 7 سنوات',
    '7-10 سنوات' => '7 إلى 10 سنوات',
    'أكثر من 10 سنوات' => 'أكثر من 10 سنوات',
];

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function old($key)
{
    return isset($_POST[$key]) ? e($_POST[$key]) : '';
}

function selected($key, $value)
{
    return isset($_POST[$key]) && (string) $_POST[$key] === (string) $value ? ' selected' : '';
}

function postedText($key, $required = true)
{
    $value = isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
    if ($required && $value === '') {
        throw new RuntimeException('يرجى تعبئة جميع الحقول المطلوبة قبل الإرسال.');
    }
    return preg_replace('/\s+/u', ' ', $value);
}

function allowedChoice($key, array $choices)
{
    $value = isset($_POST[$key]) ? (string) $_POST[$key] : '';
    if (!array_key_exists($value, $choices)) {
        throw new RuntimeException('إحدى القيم المختارة غير صحيحة. يرجى مراجعة الطلب.');
    }
    return $value;
}

function sendApplicationEmail(array $data, $cvPath, array $config)
{
    $mail = new PHPMailer(true);
    $mailConfig = $config['mail'];
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

        $safe = array_map('e', $data);
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

$message = '';
$messageType = '';
$initialStep = 0;
if (!empty($_SESSION['application_flash'])) {
    $message = $_SESSION['application_flash']['message'];
    $messageType = $_SESSION['application_flash']['type'];
    unset($_SESSION['application_flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $savedCvPath = null;
    try {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string) $_POST['csrf_token'])) {
            throw new RuntimeException('انتهت صلاحية الجلسة. حدّث الصفحة ثم أعد المحاولة.');
        }

        $fullName = postedText('full_name');
        $email = postedText('email');
        $phone = postedText('phone');
        $city = postedText('city');
        $nationality = postedText('nationality');
        if (mb_strlen($fullName) < 6 || mb_strlen($fullName) > 120) {
            throw new RuntimeException('يرجى إدخال الاسم الكامل كما يظهر في الهوية.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('البريد الإلكتروني غير صحيح. يرجى مراجعته.');
        }
        if (!preg_match('/^[0-9+\-\s()]{8,20}$/', $phone)) {
            throw new RuntimeException('رقم الجوال غير صحيح. استخدم أرقامًا فقط مع رمز الدولة عند الحاجة.');
        }

        $day = filter_input(INPUT_POST, 'birth_day', FILTER_VALIDATE_INT);
        $month = filter_input(INPUT_POST, 'birth_month', FILTER_VALIDATE_INT);
        $year = filter_input(INPUT_POST, 'birth_year', FILTER_VALIDATE_INT);
        if (!$day || !$month || !$year || !checkdate($month, $day, $year)) {
            throw new RuntimeException('تاريخ الميلاد غير صحيح. يرجى اختيار تاريخ صالح.');
        }
        $birthDate = sprintf('%04d-%02d-%02d', $year, $month, $day);

        $initialStep = 1;
        $position = allowedChoice('position', $positions);
        $availability = allowedChoice('availability', $availabilityOptions);
        $appliedBefore = allowedChoice('applied_before', $appliedBeforeOptions);
        $currentJob = postedText('current_job', false) ?: 'غير محدد';

        $initialStep = 2;
        $education = allowedChoice('education', $educationOptions);
        $major = postedText('major');
        $experience = allowedChoice('experience', $experienceOptions);

        $initialStep = 3;
        if (empty($_POST['privacy_consent'])) {
            throw new RuntimeException('يلزم الموافقة على استخدام البيانات لغرض مراجعة طلب التوظيف.');
        }
        if (!isset($_FILES['cv_file']) || $_FILES['cv_file']['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('تعذر رفع السيرة الذاتية. اختر ملف PDF وحاول مرة أخرى.');
        }
        if ($_FILES['cv_file']['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('حجم السيرة الذاتية يتجاوز 5 ميجابايت.');
        }
        $tempFile = (string) $_FILES['cv_file']['tmp_name'];
        $extension = strtolower(pathinfo((string) $_FILES['cv_file']['name'], PATHINFO_EXTENSION));
        $handle = fopen($tempFile, 'rb');
        $signature = $handle ? fread($handle, 5) : '';
        if ($handle) fclose($handle);
        if ($extension !== 'pdf' || $signature !== '%PDF-') {
            throw new RuntimeException('السيرة الذاتية يجب أن تكون ملف PDF صالحًا.');
        }

        $configPath = __DIR__ . '/config.local.php';
        if (!is_file($configPath)) {
            throw new RuntimeException('إعدادات الاستقبال غير متوفرة حاليًا. يرجى المحاولة لاحقًا.');
        }
        $config = require $configPath;
        if (!isset($config['database'], $config['mail'])) {
            throw new RuntimeException('إعدادات النظام غير مكتملة. يرجى التواصل مع الدعم الفني.');
        }

        $uploadDirectory = __DIR__ . '/uploads/cv/';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
            throw new RuntimeException('تعذر تجهيز مساحة حفظ السيرة الذاتية.');
        }
        $savedCvName = uniqid('cv_', true) . '.pdf';
        $savedCvPath = $uploadDirectory . $savedCvName;
        if (!move_uploaded_file($tempFile, $savedCvPath)) {
            throw new RuntimeException('تعذر حفظ السيرة الذاتية. يرجى المحاولة مرة أخرى.');
        }

        $database = $config['database'];
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $database['host'], $database['port'], $database['name']);
        $connection = new PDO($dsn, $database['username'], $database['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $statement = $connection->prepare('INSERT INTO job_applications
            (full_name, email, phone, birth_date, city, nationality, position, education, major, experience, current_job, cv_file, applied_before, availability, application_date)
            VALUES (:full_name, :email, :phone, :birth_date, :city, :nationality, :position, :education, :major, :experience, :current_job, :cv_file, :applied_before, :availability, NOW())');
        $statement->execute([
            ':full_name' => $fullName, ':email' => $email, ':phone' => $phone, ':birth_date' => $birthDate,
            ':city' => $city, ':nationality' => $nationality, ':position' => $position, ':education' => $education,
            ':major' => $major, ':experience' => $experience, ':current_job' => $currentJob, ':cv_file' => $savedCvName,
            ':applied_before' => $appliedBefore, ':availability' => $availability,
        ]);

        $emailSent = sendApplicationEmail([
            'full_name' => $fullName, 'email' => $email, 'phone' => $phone, 'birth_date' => $birthDate,
            'city' => $city, 'nationality' => $nationality, 'position_label' => $positions[$position],
            'current_job' => $currentJob, 'availability_label' => $availabilityOptions[$availability],
            'applied_before_label' => $appliedBeforeOptions[$appliedBefore], 'education' => $educationOptions[$education],
            'major' => $major, 'experience_label' => $experienceOptions[$experience],
        ], $savedCvPath, $config);

        $_SESSION['application_flash'] = [
            'type' => 'success',
            'message' => $emailSent
                ? 'وصلنا طلبك بنجاح. سيراجعه فريق الموارد البشرية، وسنتواصل معك إذا تطابقت خبراتك مع الفرصة المناسبة.'
                : 'وصلنا طلبك وحُفظ بنجاح. تعذر إرسال الإشعار الداخلي مؤقتًا، لكن طلبك موجود لدى فريق الموارد البشرية.',
        ];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    } catch (PDOException $exception) {
        if ($savedCvPath && is_file($savedCvPath)) unlink($savedCvPath);
        $message = $exception->getCode() === '23000'
            ? 'يوجد طلب مسجل مسبقًا بهذه البيانات. إذا رغبت في تحديثه فتواصل مع فريق الموارد البشرية.'
            : 'تعذر تسجيل الطلب الآن بسبب مشكلة تقنية. يرجى المحاولة مرة أخرى لاحقًا.';
        $messageType = 'error';
        error_log('خطأ قاعدة بيانات التوظيف: ' . $exception->getMessage());
    } catch (Throwable $exception) {
        if ($savedCvPath && is_file($savedCvPath)) unlink($savedCvPath);
        $message = $exception->getMessage();
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="قدّم طلب انضمامك إلى فريق نجيك بخطوات واضحة وسريعة.">
    <title>انضم إلى فريق نجيك | طلب توظيف</title>
    <script>document.documentElement.classList.add('js');</script>
    <style>
        :root{--orange:#f26522;--orange-dark:#d95012;--orange-soft:#fff2e9;--ink:#191919;--muted:#68645f;--white:#fff;--canvas:#f4f3f0;--line:#dedbd6;--green:#16794b;--red:#b42318;--radius:22px;--shadow:0 18px 45px rgba(25,25,25,.08)}
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;min-height:100vh;background:var(--canvas);color:var(--ink);font-family:"Segoe UI",Tahoma,Arial,sans-serif;line-height:1.65}button,input,select{font:inherit}button,select{cursor:pointer}
        .topbar{background:var(--ink);color:#fff;border-bottom:4px solid var(--orange)}.topbar-inner{width:min(1180px,calc(100% - 40px));min-height:76px;margin:auto;display:flex;align-items:center;justify-content:space-between;gap:24px}.brand{display:inline-flex;align-items:center;gap:12px;color:#fff;text-decoration:none}.brand-mark{width:42px;height:42px;display:grid;place-items:center;background:var(--orange);border-radius:12px 4px;color:#fff;font-size:24px;font-weight:900}.brand-name{font-size:24px;font-weight:800}.topbar-label{color:#bcb8b2;font-size:14px}
        .hero{position:relative;overflow:hidden;background:var(--ink);color:#fff;padding:70px 0 96px}.hero:before{content:"";position:absolute;inset:auto -90px -210px auto;width:480px;height:480px;border:90px solid rgba(242,101,34,.13);border-radius:50%}.hero:after{content:"";position:absolute;width:180px;height:8px;top:56px;left:8%;background:var(--orange);transform:rotate(-10deg)}.hero-inner{position:relative;z-index:1;width:min(1180px,calc(100% - 40px));margin:auto;display:grid;grid-template-columns:1.2fr .8fr;gap:80px;align-items:end}.eyebrow{margin:0 0 18px;color:var(--orange);font-size:15px;font-weight:800;letter-spacing:.08em}.hero h1{max-width:760px;margin:0;font-size:clamp(38px,6vw,72px);line-height:1.12;letter-spacing:-2px}.hero-copy{max-width:660px;margin:24px 0 0;color:#c9c6c1;font-size:clamp(17px,2vw,20px)}.hero-facts{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}.fact{min-height:108px;padding:20px;border:1px solid #3a3937;border-radius:14px;background:rgba(255,255,255,.035)}.fact strong{display:block;font-size:17px}.fact span{color:#aaa7a2;font-size:14px}
        .shell{position:relative;z-index:2;width:min(1180px,calc(100% - 40px));margin:-48px auto 72px;display:grid;grid-template-columns:300px minmax(0,1fr);gap:28px;align-items:start}.journey,.card{background:#fff;border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow)}.journey{position:sticky;top:24px;padding:28px}.journey h2{margin:0 0 6px;font-size:21px}.journey>p{margin:0 0 26px;color:var(--muted);font-size:14px}.journey ol{list-style:none;padding:0;margin:0}.journey li{position:relative;display:grid;grid-template-columns:34px 1fr;gap:12px;padding-bottom:24px}.journey li:not(:last-child):after{content:"";position:absolute;top:34px;right:16px;width:2px;height:calc(100% - 30px);background:var(--line)}.journey-number{position:relative;z-index:1;width:34px;height:34px;display:grid;place-items:center;border-radius:50%;background:var(--orange-soft);color:var(--orange-dark);font-size:13px;font-weight:800}.journey strong{display:block;font-size:15px}.journey li span:last-child{color:var(--muted);font-size:13px}.privacy-note{padding:16px;border-radius:12px;background:#f6f6f5;color:var(--muted);font-size:13px}
        .card{overflow:hidden}.card-head{padding:32px 38px 26px;border-bottom:1px solid var(--line)}.card-head-row{display:flex;align-items:flex-start;justify-content:space-between;gap:20px}.card-head h2{margin:0 0 5px;font-size:28px}.card-head p{margin:0;color:var(--muted)}.time-badge{flex:0 0 auto;padding:8px 12px;border-radius:999px;background:var(--orange-soft);color:var(--orange-dark);font-size:13px;font-weight:700}.stepper{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:28px}.step-button{padding:0;border:0;background:transparent;color:var(--muted);text-align:right}.step-bar{display:block;height:5px;margin-bottom:9px;border-radius:99px;background:#e8e6e2;transition:.25s}.step-button.is-active,.step-button.is-complete{color:var(--ink)}.step-button.is-active .step-bar,.step-button.is-complete .step-bar{background:var(--orange)}.step-label{font-size:12px;font-weight:700}
        .alert{margin:28px 38px 0;padding:17px 18px;border-radius:12px;border:1px solid;font-weight:650}.alert-success{color:var(--green);background:#edf9f2;border-color:#b8e3cb}.alert-error{color:var(--red);background:#fff2f0;border-color:#f2c3bd}.form{padding:34px 38px 38px}.js .step-panel:not(.is-active){display:none}.panel-heading{margin-bottom:28px}.panel-kicker{display:block;margin-bottom:6px;color:var(--orange-dark);font-size:13px;font-weight:800}.panel-heading h3{margin:0 0 6px;font-size:24px}.panel-heading p{margin:0;color:var(--muted);font-size:15px}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px}.field{min-width:0}.field.full{grid-column:1/-1}.field label,.group-label{display:block;margin-bottom:8px;color:#2b2926;font-size:14px;font-weight:700}.required{color:var(--orange-dark)}
        input[type=text],input[type=email],input[type=tel],select{width:100%;height:52px;padding:0 15px;border:1px solid #c9c5bf;border-radius:10px;background:#fff;color:var(--ink);outline:none;transition:.18s}input:hover,select:hover{border-color:#8f8a83}input:focus,select:focus{border-color:var(--orange);box-shadow:0 0 0 4px rgba(242,101,34,.13)}input[aria-invalid=true],select[aria-invalid=true]{border-color:var(--red)}input::placeholder{color:#9a958e}.ltr{direction:ltr;text-align:left}.help{display:block;margin-top:7px;color:var(--muted);font-size:12px}.date-grid{display:grid;grid-template-columns:.8fr 1.25fr 1fr;gap:10px}
        .upload-box{position:relative;display:grid;place-items:center;min-height:190px;padding:30px;border:2px dashed #c8c3bc;border-radius:16px;background:#faf9f7;text-align:center;transition:.18s}.upload-box:hover,.upload-box.is-dragging{border-color:var(--orange);background:var(--orange-soft)}.upload-box input{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer}.upload-icon{width:48px;height:48px;display:grid;place-items:center;margin:0 auto 12px;border-radius:14px;background:var(--orange-soft);color:var(--orange-dark);font-size:24px;font-weight:800}.upload-title{display:block;font-weight:800}.upload-hint{display:block;margin-top:5px;color:var(--muted);font-size:13px}.file-status{margin:12px 0 0;color:var(--green);font-size:14px;font-weight:700}
        .review{margin-top:26px;padding:22px;border:1px solid var(--line);border-radius:16px;background:#faf9f7}.review h4{margin:0 0 16px;font-size:17px}.review-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px 24px}.review small{display:block;color:var(--muted);font-size:12px}.review strong{display:block;margin-top:2px;font-size:14px;overflow-wrap:anywhere}.consent{display:grid;grid-template-columns:22px 1fr;gap:10px;align-items:start;margin-top:24px;padding:17px;border-radius:12px;background:var(--orange-soft);color:#514942;font-size:13px}.consent input{width:18px;height:18px;margin:3px 0 0;accent-color:var(--orange)}
        .navigation{display:none;align-items:center;justify-content:space-between;gap:12px;margin-top:34px;padding-top:26px;border-top:1px solid var(--line)}.js .navigation{display:flex}.button{min-height:50px;padding:0 24px;border-radius:10px;border:1px solid transparent;font-weight:800;transition:.15s}.button:hover{transform:translateY(-1px)}.primary{background:var(--orange);color:#fff}.primary:hover,.submit:hover{background:var(--orange-dark)}.secondary{background:#fff;color:var(--ink);border-color:var(--line)}.button[hidden]{display:none}.submit{width:100%;min-height:56px;margin-top:18px;border:0;border-radius:11px;background:var(--orange);color:#fff;font-weight:800;font-size:16px}.submit:disabled{opacity:.7;cursor:wait}.no-js{margin-bottom:24px;padding:14px;border-radius:10px;background:#fff8d9;color:#6b5710}
        .footer{border-top:1px solid #333;background:var(--ink);color:#aaa7a2}.footer-inner{width:min(1180px,calc(100% - 40px));min-height:92px;margin:auto;display:flex;align-items:center;justify-content:space-between;gap:24px;font-size:13px}.footer strong{color:#fff}
        @media(max-width:930px){.hero-inner{grid-template-columns:1fr;gap:36px}.hero-facts{max-width:560px}.shell{grid-template-columns:1fr}.journey{position:static}.journey ol{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}.journey li{display:block;padding:0}.journey li:after{display:none}.journey-number{margin-bottom:8px}}
        @media(max-width:650px){.topbar-inner{min-height:68px}.topbar-label{display:none}.hero{padding:48px 0 78px}.hero h1{letter-spacing:-1px}.fact{min-height:96px;padding:16px}.shell{width:calc(100% - 24px);margin-bottom:40px}.journey{padding:22px}.journey ol{grid-template-columns:repeat(2,1fr);gap:22px 12px}.card-head,.form{padding-right:22px;padding-left:22px}.card-head-row{display:block}.time-badge{display:inline-block;margin-top:14px}.step-label{font-size:10px}.alert{margin-right:22px;margin-left:22px}.grid,.review-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.date-grid{grid-template-columns:1fr}.footer-inner{padding:22px 0;display:block}}
        @media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}*,*:before,*:after{transition:none!important}}
    </style>
</head>
<body>
<header class="topbar"><div class="topbar-inner"><a class="brand" href="/" aria-label="نجيك - الرئيسية"><span class="brand-mark" aria-hidden="true">ن</span><span class="brand-name">نجيك</span></a><span class="topbar-label">بوابة الفرص المهنية</span></div></header>
<main>
    <section class="hero" aria-labelledby="page-title"><div class="hero-inner">
        <div><p class="eyebrow">اصنع أثرًا معنا</p><h1 id="page-title">مكانك القادم قد يكون بيننا.</h1><p class="hero-copy">نبحث عن أشخاص يؤمنون بأن الخدمة الممتازة تبدأ بفريق استثنائي. شاركنا خبرتك وطموحك، ودعنا نتعرّف عليك.</p></div>
        <div class="hero-facts" aria-label="معلومات سريعة"><div class="fact"><strong>3–5 دقائق</strong><span>الوقت المتوقع لإكمال الطلب</span></div><div class="fact"><strong>PDF فقط</strong><span>السيرة الذاتية بحد أقصى 5 MB</span></div></div>
    </div></section>
    <div class="shell">
        <aside class="journey" aria-labelledby="journey-title"><h2 id="journey-title">ماذا بعد التقديم؟</h2><p>رحلة واضحة من الطلب إلى القرار.</p>
            <ol><li><span class="journey-number">1</span><div><strong>استلام الطلب</strong><span>نسجّل بياناتك وسيرتك الذاتية.</span></div></li><li><span class="journey-number">2</span><div><strong>مراجعة الفريق</strong><span>نراجع ملاءمة خبراتك للفرص.</span></div></li><li><span class="journey-number">3</span><div><strong>التواصل والمقابلة</strong><span>نتواصل مع المرشحين الأنسب.</span></div></li><li><span class="journey-number">4</span><div><strong>القرار</strong><span>نشاركك الخطوة التالية بوضوح.</span></div></li></ol>
            <div class="privacy-note">تُستخدم بياناتك لأغراض التوظيف فقط، ولا نطلب منك أي رسوم خلال عملية التوظيف.</div>
        </aside>
        <section class="card" aria-labelledby="application-title">
            <div class="card-head"><div class="card-head-row"><div><h2 id="application-title">طلب الانضمام</h2><p>أكمل البيانات التالية، ثم راجعها قبل الإرسال.</p></div><span class="time-badge">◷ 3–5 دقائق</span></div>
                <nav class="stepper" aria-label="خطوات النموذج"><button class="step-button is-active" type="button" data-target="0" aria-current="step"><span class="step-bar"></span><span class="step-label">بياناتك</span></button><button class="step-button" type="button" data-target="1"><span class="step-bar"></span><span class="step-label">الفرصة</span></button><button class="step-button" type="button" data-target="2"><span class="step-bar"></span><span class="step-label">الخبرة</span></button><button class="step-button" type="button" data-target="3"><span class="step-bar"></span><span class="step-label">السيرة والمراجعة</span></button></nav>
            </div>
            <?php if ($message !== ''): ?><div class="alert alert-<?php echo e($messageType); ?>" role="alert"><?php echo e($message); ?></div><?php endif; ?>
            <form class="form" method="POST" enctype="multipart/form-data" data-initial-step="<?php echo (int) $initialStep; ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><noscript><div class="no-js">يمكنك تعبئة النموذج كاملًا دون JavaScript، وستُراجع البيانات عند الإرسال.</div></noscript>
                <section class="step-panel is-active" data-step="0" aria-labelledby="step1-title"><div class="panel-heading"><span class="panel-kicker">الخطوة 1 من 4</span><h3 id="step1-title">لنبدأ بالتعرّف عليك</h3><p>اكتب بيانات التواصل كما تظهر في مستنداتك الرسمية.</p></div>
                    <div class="grid"><div class="field full"><label for="full_name">الاسم الكامل (رباعي) <span class="required">*</span></label><input id="full_name" type="text" name="full_name" value="<?php echo old('full_name'); ?>" autocomplete="name" maxlength="120" required></div>
                    <div class="field"><label for="email">البريد الإلكتروني <span class="required">*</span></label><input class="ltr" id="email" type="email" name="email" value="<?php echo old('email'); ?>" placeholder="name@example.com" autocomplete="email" maxlength="160" required><small class="help">سنستخدمه للتواصل بخصوص طلبك.</small></div>
                    <div class="field"><label for="phone">رقم الجوال <span class="required">*</span></label><input class="ltr" id="phone" type="tel" name="phone" value="<?php echo old('phone'); ?>" placeholder="+966 5X XXX XXXX" autocomplete="tel" maxlength="20" required></div>
                    <div class="field full"><span class="group-label" id="birth-label">تاريخ الميلاد <span class="required">*</span></span><div class="date-grid" aria-labelledby="birth-label"><select name="birth_day" aria-label="اليوم" required><option value="">اليوم</option><?php for ($d=1;$d<=31;$d++): ?><option value="<?php echo $d; ?>"<?php echo selected('birth_day',$d); ?>><?php echo $d; ?></option><?php endfor; ?></select><select name="birth_month" aria-label="الشهر" required><option value="">الشهر</option><?php foreach ([1=>'يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'] as $n=>$name): ?><option value="<?php echo $n; ?>"<?php echo selected('birth_month',$n); ?>><?php echo $name; ?></option><?php endforeach; ?></select><select name="birth_year" aria-label="السنة" required><option value="">السنة</option><?php for ($y=(int)date('Y')-18;$y>=1950;$y--): ?><option value="<?php echo $y; ?>"<?php echo selected('birth_year',$y); ?>><?php echo $y; ?></option><?php endfor; ?></select></div></div>
                    <div class="field"><label for="city">المدينة <span class="required">*</span></label><input id="city" type="text" name="city" value="<?php echo old('city'); ?>" placeholder="مثال: جدة" autocomplete="address-level2" maxlength="80" required></div><div class="field"><label for="nationality">الجنسية <span class="required">*</span></label><input id="nationality" type="text" name="nationality" value="<?php echo old('nationality'); ?>" autocomplete="country-name" maxlength="80" required></div></div>
                </section>
                <section class="step-panel" data-step="1" aria-labelledby="step2-title"><div class="panel-heading"><span class="panel-kicker">الخطوة 2 من 4</span><h3 id="step2-title">أي فرصة تبحث عنها؟</h3><p>اختر المجال الأقرب لك وأخبرنا عن وضعك المهني الحالي.</p></div>
                    <div class="grid"><div class="field full"><label for="position">المسمى الوظيفي المتقدم له <span class="required">*</span></label><select id="position" name="position" required><option value="">اختر الوظيفة</option><?php foreach($positions as $v=>$l): ?><option value="<?php echo e($v); ?>"<?php echo selected('position',$v); ?>><?php echo e($l); ?></option><?php endforeach; ?></select></div>
                    <div class="field full"><label for="current_job">الوظيفة الحالية <span class="help" style="display:inline">(اختياري)</span></label><input id="current_job" type="text" name="current_job" value="<?php echo old('current_job'); ?>" placeholder="اكتب مسماك الحالي إن وجد" maxlength="120"></div>
                    <div class="field"><label for="availability">حالة التفرغ <span class="required">*</span></label><select id="availability" name="availability" required><option value="">اختر حالة التفرغ</option><?php foreach($availabilityOptions as $v=>$l): ?><option value="<?php echo e($v); ?>"<?php echo selected('availability',$v); ?>><?php echo e($l); ?></option><?php endforeach; ?></select></div>
                    <div class="field"><label for="applied_before">هل سبق التقديم لدينا؟ <span class="required">*</span></label><select id="applied_before" name="applied_before" required><option value="">اختر الإجابة</option><?php foreach($appliedBeforeOptions as $v=>$l): ?><option value="<?php echo e($v); ?>"<?php echo selected('applied_before',$v); ?>><?php echo e($l); ?></option><?php endforeach; ?></select></div></div>
                </section>
                <section class="step-panel" data-step="2" aria-labelledby="step3-title"><div class="panel-heading"><span class="panel-kicker">الخطوة 3 من 4</span><h3 id="step3-title">دراستك وخبرتك</h3><p>هذه المعلومات تساعدنا على مواءمة طلبك مع الفرصة المناسبة.</p></div>
                    <div class="grid"><div class="field"><label for="education">أعلى مؤهل علمي <span class="required">*</span></label><select id="education" name="education" required><option value="">اختر المؤهل</option><?php foreach($educationOptions as $v=>$l): ?><option value="<?php echo e($v); ?>"<?php echo selected('education',$v); ?>><?php echo e($l); ?></option><?php endforeach; ?></select></div><div class="field"><label for="major">التخصص العلمي <span class="required">*</span></label><input id="major" type="text" name="major" value="<?php echo old('major'); ?>" placeholder="مثال: علوم الحاسب" maxlength="120" required></div><div class="field full"><label for="experience">سنوات الخبرة <span class="required">*</span></label><select id="experience" name="experience" required><option value="">اختر سنوات الخبرة</option><?php foreach($experienceOptions as $v=>$l): ?><option value="<?php echo e($v); ?>"<?php echo selected('experience',$v); ?>><?php echo e($l); ?></option><?php endforeach; ?></select></div></div>
                </section>
                <section class="step-panel" data-step="3" aria-labelledby="step4-title"><div class="panel-heading"><span class="panel-kicker">الخطوة 4 من 4</span><h3 id="step4-title">أضف سيرتك وراجع الطلب</h3><p>تأكد من أن سيرتك حديثة وواضحة قبل الإرسال.</p></div>
                    <div class="field"><label for="cv_file">السيرة الذاتية <span class="required">*</span></label><label class="upload-box" for="cv_file"><input id="cv_file" type="file" name="cv_file" accept="application/pdf,.pdf" required><span><span class="upload-icon">↑</span><span class="upload-title">اسحب ملف PDF هنا أو اضغط للاختيار</span><span class="upload-hint">ملف واحد بصيغة PDF، بحد أقصى 5 MB</span></span></label><p class="file-status" id="file-status" aria-live="polite"></p></div>
                    <div class="review"><h4>ملخص طلبك</h4><div class="review-grid"><div><small>الاسم</small><strong data-review="full_name">—</strong></div><div><small>البريد</small><strong data-review="email">—</strong></div><div><small>الوظيفة</small><strong data-review="position">—</strong></div><div><small>المدينة</small><strong data-review="city">—</strong></div><div><small>المؤهل</small><strong data-review="education">—</strong></div><div><small>الخبرة</small><strong data-review="experience">—</strong></div></div></div>
                    <label class="consent"><input type="checkbox" name="privacy_consent" value="1"<?php echo !empty($_POST['privacy_consent'])?' checked':''; ?> required><span>أوافق على استخدام بياناتي وسيرتي الذاتية لغرض تقييم طلب التوظيف والتواصل معي بشأن الفرص المناسبة.</span></label><button class="submit" type="submit">إرسال طلب التوظيف</button>
                </section>
                <div class="navigation"><button class="button secondary" type="button" data-previous hidden>السابق</button><button class="button primary" type="button" data-next>التالي</button></div>
            </form>
        </section>
    </div>
</main>
<footer class="footer"><div class="footer-inner"><p><strong>نجيك لبابك التجارية</strong> — نصنع تجربة خدمة أقرب وأسهل.</p><p>© <?php echo date('Y'); ?> جميع الحقوق محفوظة</p></div></footer>
<script>
(function(){
    const form=document.querySelector('.form');if(!form)return;
    const panels=[...form.querySelectorAll('.step-panel')],steps=[...document.querySelectorAll('.step-button')],prev=form.querySelector('[data-previous]'),next=form.querySelector('[data-next]'),submit=form.querySelector('.submit'),file=form.querySelector('#cv_file'),box=form.querySelector('.upload-box'),status=form.querySelector('#file-status');
    let current=Math.min(Math.max(Number(form.dataset.initialStep)||0,0),panels.length-1),furthest=current;
    function labelOf(field){const label=field.id?form.querySelector('label[for="'+field.id+'"]'):null;return label?label.textContent.replace('*','').trim():'هذا الحقل'}
    function validate(index){for(const field of panels[index].querySelectorAll('input,select')){field.setCustomValidity('');field.removeAttribute('aria-invalid');if(!field.checkValidity()){field.setAttribute('aria-invalid','true');if(field.validity.valueMissing)field.setCustomValidity('يرجى تعبئة '+labelOf(field));else if(field.validity.typeMismatch)field.setCustomValidity('يرجى إدخال قيمة صحيحة');field.reportValidity();field.addEventListener('input',()=>{field.setCustomValidity('');field.removeAttribute('aria-invalid')},{once:true});return false}}return true}
    function review(){form.querySelectorAll('[data-review]').forEach(out=>{const field=form.elements[out.dataset.review];if(!field)return;const value=field.tagName==='SELECT'?(field.selectedIndex>0?field.options[field.selectedIndex].text:''):field.value.trim();out.textContent=value||'—'})}
    function show(index,focus){current=Math.min(Math.max(index,0),panels.length-1);furthest=Math.max(furthest,current);panels.forEach((p,i)=>{p.classList.toggle('is-active',i===current);p.setAttribute('aria-hidden',i===current?'false':'true')});steps.forEach((b,i)=>{b.classList.toggle('is-active',i===current);b.classList.toggle('is-complete',i<current);b.toggleAttribute('aria-current',i===current);b.disabled=i>furthest});prev.hidden=current===0;next.hidden=current===panels.length-1;if(current===panels.length-1)review();if(focus){panels[current].querySelector('h3').focus({preventScroll:true});document.querySelector('.card').scrollIntoView({behavior:'smooth',block:'start'})}}
    panels.forEach(p=>p.querySelector('h3').setAttribute('tabindex','-1'));next.addEventListener('click',()=>{if(validate(current))show(current+1,true)});prev.addEventListener('click',()=>show(current-1,true));steps.forEach(b=>b.addEventListener('click',()=>{const target=Number(b.dataset.target);if(target<=furthest)show(target,true)}));
    file.addEventListener('change',()=>{const selectedFile=file.files[0];if(!selectedFile){status.textContent='';return}const size=(selectedFile.size/1048576).toFixed(1);if(selectedFile.size>5242880){file.setCustomValidity('حجم الملف يتجاوز 5 ميجابايت');status.style.color='var(--red)';status.textContent='الملف أكبر من الحد المسموح: '+size+' MB'}else{file.setCustomValidity('');status.style.color='var(--green)';status.textContent='تم اختيار: '+selectedFile.name+' ('+size+' MB)'}});['dragenter','dragover'].forEach(n=>box.addEventListener(n,()=>box.classList.add('is-dragging')));['dragleave','drop'].forEach(n=>box.addEventListener(n,()=>box.classList.remove('is-dragging')));
    form.addEventListener('submit',event=>{for(let i=0;i<panels.length;i++){if(!validate(i)){event.preventDefault();show(i,false);return}}submit.disabled=true;submit.textContent='جارٍ إرسال طلبك…'});show(current,false);
})();
</script>
</body>
</html>
