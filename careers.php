<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج طلب توظيف - إدارة الموارد البشرية</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            background: #e8e8e8;
            min-height: 100vh;
            padding: 30px 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 950px;
            margin: 0 auto;
            background: white;
            border: 3px solid #2a2a2a;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(180deg, #1a1a1a 0%, #2a2a2a 100%);
            color: white;
            padding: 35px 40px;
            border-bottom: 5px solid #f2652a;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #f2652a 0%, #ffa500 50%, #f2652a 100%);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 2.2em;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .header p {
            font-size: 1em;
            color: #d0d0d0;
            font-weight: 400;
        }

        .header-logo {
            font-size: 3em;
            color: #f2652a;
        }

        .form-container {
            padding: 45px;
            background: #fafafa;
        }

        .form-inner {
            background: white;
            padding: 35px;
            border: 2px solid #ddd;
        }

        .section-title {
            font-size: 1.4em;
            font-weight: 700;
            margin: 35px 0 25px 0;
            padding: 12px 15px;
            background: #f5f5f5;
            border-right: 5px solid #f2652a;
            color: #2a2a2a;
            position: relative;
        }

        .section-title:first-child {
            margin-top: 0;
        }

        .section-title::before {
            color: #f2652a;
            margin-left: 8px;
            font-size: 0.8em;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2a2a2a;
            font-weight: 600;
            font-size: 0.95em;
        }

        .form-group label span {
            color: #f2652a;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ccc;
            background: white;
            font-size: 0.95em;
            font-family: inherit;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #f2652a;
            background: #fffaf5;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 110px;
            line-height: 1.5;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .submit-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 3px solid #e0e0e0;
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(180deg, #f2652a 0%, #e67e00 100%);
            color: white;
            border: 3px solid #d67000;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: linear-gradient(180deg, #ffa520 0%, #f2652a 100%);
            border-color: #f2652a;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 18px 20px;
            margin-bottom: 25px;
            border: 3px solid;
            font-weight: 600;
            font-size: 1em;
        }

        .alert-success {
            background: #f8f8f8;
            color: #2a2a2a;
            border-color: #f2652a;
            border-right-width: 8px;
        }

        .alert-error {
            background: #f8f8f8;
            color: #2a2a2a;
            border-color: #333;
            border-right-width: 8px;
        }

        .info-box {
            background: #f9f9f9;
            border: 2px solid #e0e0e0;
            border-right: 5px solid #f2652a;
            padding: 18px 20px;
            margin-bottom: 25px;
            color: #555;
            font-size: 0.95em;
        }

        .info-box strong {
            color: #2a2a2a;
            display: block;
            margin-bottom: 5px;
            font-size: 1.05em;
        }

        .required-note {
            background: #fffaf5;
            border: 2px dashed #f2652a;
            padding: 12px 15px;
            margin-bottom: 25px;
            font-size: 0.9em;
            color: #666;
            text-align: center;
        }

        .footer-note {
            background: #f5f5f5;
            padding: 20px;
            text-align: center;
            border-top: 3px solid #ddd;
            color: #666;
            font-size: 0.9em;
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: left 15px center;
            padding-right: 40px;
        }

        .birth-wrapper {
            margin-bottom: 30px;
        }

        .birth-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #2a2a2a;
            font-size: 0.95em;
        }

        .birth-label span {
            color: #f2652a;
            font-weight: bold;
        }

        .birth-row {
            display: grid;
            grid-template-columns: 1fr 1.5fr 1fr;
            gap: 12px;
        }

        .birth-row select {
            padding: 12px 15px;
            border: 2px solid #ccc;
            background: white;
            font-size: 0.95em;
            font-family: inherit;
            transition: all 0.2s;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: left 15px center;
            padding-right: 40px;
        }

        .birth-row select:focus {
            outline: none;
            border-color: #f2652a;
            background-color: #fffaf5;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .birth-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .header h1 {
                font-size: 1.6em;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-logo {
                margin-top: 15px;
            }

            .form-container {
                padding: 25px 20px;
            }

            .form-inner {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1>نموذج طلب التوظيف الإلكتروني</h1>
                    <p>شركة نجيك لبابك التجارية - قسم التوظيف والاستقطاب</p>
                </div>
            </div>
        </div>

        <div class="form-container">
            <div class="form-inner">
                <?php
                // إضافة مكتبة PHPMailer
                use PHPMailer\PHPMailer\PHPMailer;
                use PHPMailer\PHPMailer\SMTP;
                use PHPMailer\PHPMailer\Exception;

                require_once __DIR__ . '/vendor/autoload.php';

                $configPath = __DIR__ . '/config.local.php';
                if (!is_file($configPath)) {
                    throw new RuntimeException('إعدادات النظام غير متوفرة.');
                }

                $config = require $configPath;
                $database = $config['database'];

                $message = '';
                $messageType = '';

                // دالة إرسال الإيميل
                function sendJobApplicationEmail($applicantData, $cvFilePath, array $config) {
                    $mail = new PHPMailer(true);
                    $mailConfig = $config['mail'];

                    try {
                        // SMTP settings
                        $mail->isSMTP();
                        $mail->Host       = $mailConfig['host'];
                        $mail->SMTPAuth   = true;
                        $mail->Username   = $mailConfig['username'];
                        $mail->Password   = $mailConfig['password'];
                        $mail->SMTPSecure = $mailConfig['encryption'];
                        $mail->Port       = $mailConfig['port'];
                        $mail->CharSet    = 'UTF-8';

                        // إعدادات المرسل والمستقبل
                        $mail->setFrom($mailConfig['from_address'], $mailConfig['from_name']);
                        $mail->addAddress($mailConfig['to_address'], $mailConfig['to_name']);

                        // إرفاق ملف السيرة الذاتية
                        $mail->addAttachment(
                            $cvFilePath,
                            'CV_' . $applicantData['full_name'] . '.pdf'
                        );

                        if (!empty($mailConfig['debug'])) {
                            $mail->SMTPDebug = 2;
                            $mail->Debugoutput = static function ($str, $level) {
                                error_log("SMTP[$level] $str");
                            };
                        }

                        // إعدادات المحتوى
                        $mail->isHTML(true);
                        $mail->CharSet = 'UTF-8';
                        $mail->Subject = 'طلب توظيف جديد - ' . $applicantData['full_name'];

                        // محتوى الإيميل
                        $emailBody = "
                        <html dir='rtl' lang='ar'>
                        <head>
                            <meta charset='UTF-8'>
                            <style>
                                body { font-family: Arial, sans-serif; direction: rtl; }
                                .header { background: #f2652a; color: white; padding: 20px; text-align: center; }
                                .content { padding: 20px; background: #f9f9f9; }
                                .info-section { background: white; padding: 15px; margin: 10px 0; border-right: 4px solid #f2652a; }
                                .info-title { font-weight: bold; color: #2a2a2a; margin-bottom: 10px; }
                                .info-item { margin: 5px 0; }
                                .footer { text-align: center; color: #666; margin-top: 20px; font-size: 12px; }
                            </style>
                        </head>
                        <body>
                            <div class='header'>
                                <h2>طلب توظيف جديد</h2>
                                <p>شركة نجيك لبابك التجارية</p>
                            </div>
                            
                            <div class='content'>
                                <p>السلام عليكم ورحمة الله وبركاته،</p>
                                <p>تم استقبال طلب توظيف جديد عبر الموقع الإلكتروني. إليكم تفاصيل المتقدم:</p>
                                
                                <div class='info-section'>
                                    <div class='info-title'>البيانات الشخصية:</div>
                                    <div class='info-item'><strong>الاسم الكامل:</strong> {$applicantData['full_name']}</div>
                                    <div class='info-item'><strong>البريد الإلكتروني:</strong> {$applicantData['email']}</div>
                                    <div class='info-item'><strong>رقم الجوال:</strong> {$applicantData['phone']}</div>
                                    <div class='info-item'><strong>تاريخ الميلاد:</strong> {$applicantData['birth_date']}</div>
                                    <div class='info-item'><strong>المدينة:</strong> {$applicantData['city']}</div>
                                    <div class='info-item'><strong>الجنسية:</strong> {$applicantData['nationality']}</div>
                                </div>
                                
                                <div class='info-section'>
                                    <div class='info-title'>معلومات الوظيفة:</div>
                                    <div class='info-item'><strong>الوظيفة المطلوبة:</strong> {$applicantData['position']}</div>
                                    <div class='info-item'><strong>الوظيفة الحالية:</strong> " . ($applicantData['current_job'] ?: 'غير محدد') . "</div>
                                    <div class='info-item'><strong>حالة التفرغ:</strong> {$applicantData['availability']}</div>
                                    <div class='info-item'><strong>سبق التقديم:</strong> {$applicantData['applied_before']}</div>
                                </div>
                                
                                <div class='info-section'>
                                    <div class='info-title'>المؤهلات والخبرة:</div>
                                    <div class='info-item'><strong>المؤهل العلمي:</strong> {$applicantData['education']}</div>
                                    <div class='info-item'><strong>التخصص:</strong> {$applicantData['major']}</div>
                                    <div class='info-item'><strong>سنوات الخبرة:</strong> {$applicantData['experience']}</div>
                                </div>
                                
                                <div class='info-section'>
                                    <div class='info-title'>ملاحظات:</div>
                                    <div class='info-item'>• تم رفع السيرة الذاتية بنجاح في النظام</div>
                                    <div class='info-item'>• تاريخ التقديم: " . date('Y-m-d H:i:s') . "</div>
                                    <div class='info-item'>• يمكن الاطلاع على السيرة الذاتية من خلال النظام الإداري</div>
                                </div>
                                
                                <p>يرجى مراجعة الطلب واتخاذ الإجراء المناسب.</p>
                            </div>
                            
                            <div class='footer'>
                                <p>هذا الإيميل تم إرساله تلقائياً من نظام التوظيف الإلكتروني</p>
                                <p>شركة نجيك لبابك التجارية © 2024</p>
                            </div>
                        </body>
                        </html>";

                        $mail->Body = $emailBody;

                        $mail->send();
                        return true;
                    } catch (Exception $e) {
                        error_log("فشل في إرسال الإيميل: {$mail->ErrorInfo}");
                        return false;
                    }
                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    try {
                        // الاتصال بقاعدة البيانات
                        $dsn = sprintf(
                            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                            $database['host'],
                            $database['port'],
                            $database['name']
                        );
                        $conn = new PDO($dsn, $database['username'], $database['password']);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // استقبال البيانات وتنظيفها
                        $full_name = htmlspecialchars(trim($_POST['full_name']));
                        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                        $phone = htmlspecialchars(trim($_POST['phone']));
                        $birth_date = $_POST['birth_year'] . '-' . str_pad($_POST['birth_month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($_POST['birth_day'], 2, '0', STR_PAD_LEFT);
                        $city = htmlspecialchars(trim($_POST['city']));
                        $nationality = htmlspecialchars(trim($_POST['nationality']));
                        $position = htmlspecialchars(trim($_POST['position']));
                        $education = htmlspecialchars(trim($_POST['education']));
                        $major = htmlspecialchars(trim($_POST['major']));
                        $experience = htmlspecialchars(trim($_POST['experience']));
                        $current_job = htmlspecialchars(trim($_POST['current_job']));
                        $applied_before = htmlspecialchars(trim($_POST['applied_before']));
                        $availability = htmlspecialchars(trim($_POST['availability']));

                        // التحقق من صحة البريد الإلكتروني
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            throw new Exception('البريد الإلكتروني غير صحيح. يرجى التأكد من صحة البريد المدخل.');
                        }

                        // التحقق من رقم الجوال
                        if (!preg_match('/^[0-9+\-\s()]+$/', $phone)) {
                            throw new Exception('رقم الجوال غير صحيح. يرجى إدخال أرقام فقط.');
                        }

                        // معالجة رفع ملف السيرة الذاتية
                        $uploadDir = __DIR__ . '/uploads/cv/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        if (!isset($_FILES['cv_file']) || $_FILES['cv_file']['error'] !== UPLOAD_ERR_OK) {
                            throw new Exception('فشل رفع السيرة الذاتية. يرجى المحاولة مرة أخرى.');
                        }

                        $fileTmp = $_FILES['cv_file']['tmp_name'];
                        $fileName = $_FILES['cv_file']['name'];
                        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                        if ($fileExt !== 'pdf') {
                            throw new Exception('يجب أن يكون الملف بصيغة PDF فقط.');
                        }

                        $newFileName = uniqid('cv_', true) . '.pdf';
                        $filePath = $uploadDir . $newFileName;

                        if (!move_uploaded_file($fileTmp, $filePath)) {
                            throw new Exception('فشل حفظ السيرة الذاتية. يرجى المحاولة مرة أخرى.');
                        }

                        // إدراج البيانات في قاعدة البيانات
                        $sql = "INSERT INTO job_applications (full_name, email, phone, birth_date, city, nationality, position, education, major, experience, current_job, cv_file, applied_before, availability, application_date) 
                                VALUES (:full_name, :email, :phone, :birth_date, :city, :nationality, :position, :education, :major, :experience, :current_job, :cv_file, :applied_before, :availability, NOW())";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            ':full_name' => $full_name,
                            ':email' => $email,
                            ':phone' => $phone,
                            ':birth_date' => $birth_date,
                            ':city' => $city,
                            ':nationality' => $nationality,
                            ':position' => $position,
                            ':education' => $education,
                            ':major' => $major,
                            ':experience' => $experience,
                            ':current_job' => $current_job,
                            ':cv_file' => $newFileName,
                            ':applied_before' => $applied_before,
                            ':availability' => $availability
                        ]);

                        // إرسال الإيميل بعد نجاح الحفظ
                        $applicantData = [
                            'full_name' => $full_name,
                            'email' => $email,
                            'phone' => $phone,
                            'birth_date' => $birth_date,
                            'city' => $city,
                            'nationality' => $nationality,
                            'position' => $position,
                            'education' => $education,
                            'major' => $major,
                            'experience' => $experience,
                            'current_job' => $current_job,
                            'applied_before' => $applied_before,
                            'availability' => $availability
                        ];

                        $emailSent = sendJobApplicationEmail($applicantData, $filePath, $config);
                        
                        if ($emailSent) {
                            $message = 'تم استلام طلبك بنجاح وتسجيله في النظام. تم إخطار قسم الموارد البشرية وسيتم التواصل معك قريباً. شكراً لاهتمامك.';
                        } else {
                            $message = 'تم استلام طلبك بنجاح وتسجيله في النظام، ولكن حدث خطأ في إرسال الإشعار. سيتم التواصل معك من قبل قسم الموارد البشرية.';
                        }
                        $messageType = 'success';

                    } catch (PDOException $e) {
                        if ($e->getCode() == 1049) {
                            $message = 'خطأ في النظام: قاعدة البيانات غير متوفرة. يرجى التواصل مع الدعم الفني.';
                        } elseif ($e->getCode() == 23000) {
                            $message = 'البريد الإلكتروني مسجل مسبقاً في النظام. إذا كنت قد تقدمت سابقاً، يرجى الانتظار للحصول على رد.';
                        } else {
                            $message = 'حدث خطأ تقني في النظام. يرجى المحاولة مرة أخرى أو التواصل مع الدعم الفني.';
                        }
                        $messageType = 'error';
                    } catch (Exception $e) {
                        $message = $e->getMessage();
                        $messageType = 'error';
                    }
                }

                if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <div class="info-box">
                    <strong>إرشادات التعبئة:</strong>
                    يرجى قراءة التعليمات بعناية وتعبئة جميع الحقول المطلوبة بدقة. تأكد من صحة البيانات المدخلة قبل الإرسال.
                </div>

                <div class="required-note">
                    الحقول المميزة بعلامة (<span style="color: #f2652a;">*</span>) إلزامية ويجب تعبئتها
                </div>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="section-title">البيانات الشخصية</div>

                    <div class="form-group">
                        <label>الاسم الكامل (رباعي) <span>*</span></label>
                        <input type="text" name="full_name" required>
                    </div>

                    <div class="form-group">
                        <label>البريد الإلكتروني <span>*</span></label>
                        <input type="email" name="email" placeholder="example@email.com" required>
                    </div>

                    <div class="form-group">
                        <label>رقم الجوال <span>*</span></label>
                        <input type="tel" name="phone" placeholder="966 5X XXX XXXX" required>
                    </div>

                    <div class="birth-wrapper">
                        <label class="birth-label">تاريخ الميلاد <span>*</span></label>

                        <div class="birth-row">
                            <select name="birth_day" required>
                                <option value="">اليوم</option>
                                <?php for ($i = 1; $i <= 31; $i++) echo "<option value='$i'>$i</option>"; ?>
                            </select>

                            <select name="birth_month" required>
                                <option value="">الشهر</option>
                                <option value="1">يناير</option>
                                <option value="2">فبراير</option>
                                <option value="3">مارس</option>
                                <option value="4">أبريل</option>
                                <option value="5">مايو</option>
                                <option value="6">يونيو</option>
                                <option value="7">يوليو</option>
                                <option value="8">أغسطس</option>
                                <option value="9">سبتمبر</option>
                                <option value="10">أكتوبر</option>
                                <option value="11">نوفمبر</option>
                                <option value="12">ديسمبر</option>
                            </select>

                            <select name="birth_year" required>
                                <option value="">السنة</option>
                                <?php for ($y = date('Y') - 18; $y >= 1950; $y--) echo "<option value='$y'>$y</option>"; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>المدينة <span>*</span></label>
                        <input
                            type="text"
                            name="city"
                            placeholder="مثال: جدة، الرياض"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>الجنسية <span>*</span></label>
                        <input
                            type="text"
                            name="nationality"
                            required>
                    </div>

                    <div class="section-title">معلومات الوظيفة المطلوبة</div>

                    <div class="form-group">
                        <label>المسمى الوظيفي المتقدم له <span>*</span></label>
                        <select name="position" required>
                            <option value="">-- اختر الوظيفة --</option>
                            <option value="مطور ويب كامل">مطور ويب كامل (Full Stack)</option>
                            <option value="مطور واجهات أمامية">مطور واجهات أمامية (Frontend)</option>
                            <option value="مطور خلفي">مطور خلفي (Backend)</option>
                            <option value="مطور تطبيقات موبايل">مطور تطبيقات موبايل</option>
                            <option value="مصمم جرافيك">مصمم جرافيك</option>
                            <option value="مصمم UI/UX">مصمم تجربة مستخدم (UI/UX)</option>
                            <option value="محاسب">محاسب</option>
                            <option value="محاسب قانوني">محاسب قانوني</option>
                            <option value="مدير مبيعات">مدير مبيعات</option>
                            <option value="موظف مبيعات">موظف مبيعات</option>
                            <option value="مسوق رقمي">مسوق رقمي</option>
                            <option value="مسؤول وسائل التواصل">مسؤول وسائل التواصل الاجتماعي</option>
                            <option value="موظف خدمة عملاء">موظف خدمة عملاء</option>
                            <option value="مدير مشروع">مدير مشروع</option>
                            <option value="محلل بيانات">محلل بيانات</option>
                            <option value="مهندس شبكات">مهندس شبكات</option>
                            <option value="أخصائي أمن معلومات">أخصائي أمن معلومات</option>
                            <option value="أخرى">أخرى</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>الوظيفة الحالية (إن وجدت)</label>
                        <input type="text" name="current_job" placeholder="اذكر مسماك الوظيفي الحالي">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>حالة التفرغ للعمل <span>*</span></label>
                            <select name="availability" required>
                                <option value="">-- حالة التفرغ --</option>
                                <option value="full_time">دوام كامل</option>
                                <option value="part_time">دوام جزئي</option>
                                <option value="freelance">عمل حر</option>
                                <option value="remote">عن بعد</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>هل سبق التقديم لدينا؟ <span>*</span></label>
                            <select name="applied_before" required>
                                <option value="">-- اختر --</option>
                                <option value="no">لا، هذه المرة الأولى</option>
                                <option value="yes">نعم، سبق التقديم</option>
                            </select>
                        </div>
                    </div>

                    <div class="section-title">المؤهلات العلمية والخبرات</div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>أعلى مؤهل علمي <span>*</span></label>
                            <select name="education" required>
                                <option value="">-- اختر المؤهل --</option>
                                <option value="ثانوية عامة">ثانوية عامة</option>
                                <option value="دبلوم">دبلوم</option>
                                <option value="بكالوريوس">بكالوريوس</option>
                                <option value="ماجستير">ماجستير</option>
                                <option value="دكتوراه">دكتوراه</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>التخصص العلمي <span>*</span></label>
                            <input type="text" name="major" placeholder="مثال: علوم حاسب، إدارة أعمال" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>سنوات الخبرة العملية <span>*</span></label>
                        <select name="experience" required>
                            <option value="">-- اختر سنوات الخبرة --</option>
                            <option value="بدون خبرة">حديث التخرج (بدون خبرة)</option>
                            <option value="أقل من سنة">أقل من سنة</option>
                            <option value="1-2 سنة">سنة إلى سنتين</option>
                            <option value="2-3 سنوات">سنتين إلى 3 سنوات</option>
                            <option value="3-5 سنوات">3 إلى 5 سنوات</option>
                            <option value="5-7 سنوات">5 إلى 7 سنوات</option>
                            <option value="7-10 سنوات">7 إلى 10 سنوات</option>
                            <option value="أكثر من 10 سنوات">أكثر من 10 سنوات</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>السيرة الذاتية (PDF فقط) <span>*</span></label>
                        <input type="file" name="cv_file" accept=".pdf" required>
                    </div>

                    <div class="submit-section">
                        <button type="submit" class="submit-btn">إرسال الطلب للمراجعة</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="footer-note">
            جميع البيانات المدخلة محمية وفقاً لسياسة الخصوصية | © 2024 شركة نجيك لبابك التجارية
        </div>
    </div>
</body>

</html>
