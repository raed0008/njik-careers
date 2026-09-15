import { useEffect, useMemo, useRef, useState } from 'react';
import {
  appliedBeforeOptions,
  availabilityOptions,
  educationOptions,
  emptyForm,
  experienceOptions,
  months,
  positions,
} from './data.js';
import { ArrowIcon, CheckIcon, ClockIcon, UploadIcon } from './icons.jsx';

const steps = [
  { short: 'بياناتك', title: 'لنبدأ بالتعرّف عليك', description: 'اكتب بيانات التواصل كما تظهر في مستنداتك الرسمية.' },
  { short: 'الفرصة', title: 'أي فرصة تبحث عنها؟', description: 'اختر المجال الأقرب لك وأخبرنا عن وضعك المهني الحالي.' },
  { short: 'الخبرة', title: 'دراستك وخبرتك', description: 'هذه المعلومات تساعدنا على مواءمة طلبك مع الفرصة المناسبة.' },
  { short: 'السيرة والمراجعة', title: 'أضف سيرتك وراجع الطلب', description: 'تأكد من أن سيرتك حديثة وواضحة قبل الإرسال.' },
];

const stepFields = [
  ['full_name', 'email', 'phone', 'birth_day', 'birth_month', 'birth_year', 'city', 'nationality'],
  ['position', 'availability', 'applied_before'],
  ['education', 'major', 'experience'],
  ['cv_file', 'privacy_consent'],
];

const optionLabel = (options, value) => options.find(([key]) => key === value)?.[1] || '—';

function Field({ label, name, error, optional = false, help, children }) {
  return (
    <div className="field">
      <label htmlFor={name}>
        {label} {!optional && <span className="required">*</span>}
        {optional && <span className="optional">اختياري</span>}
      </label>
      {children}
      {help && !error && <small className="field-help">{help}</small>}
      {error && <small className="field-error" id={`${name}-error`}>{error}</small>}
    </div>
  );
}

function TextInput({ name, value, onChange, error, ...props }) {
  return (
    <input
      id={name}
      name={name}
      value={value}
      onChange={onChange}
      aria-invalid={Boolean(error)}
      aria-describedby={error ? `${name}-error` : undefined}
      {...props}
    />
  );
}

function SelectInput({ name, value, onChange, error, placeholder, options }) {
  return (
    <select
      id={name}
      name={name}
      value={value}
      onChange={onChange}
      aria-invalid={Boolean(error)}
      aria-describedby={error ? `${name}-error` : undefined}
    >
      <option value="">{placeholder}</option>
      {options.map(([key, label]) => <option key={key} value={key}>{label}</option>)}
    </select>
  );
}

function App() {
  const [form, setForm] = useState(emptyForm);
  const [currentStep, setCurrentStep] = useState(0);
  const [furthestStep, setFurthestStep] = useState(0);
  const [errors, setErrors] = useState({});
  const [cvFile, setCvFile] = useState(null);
  const [isDragging, setIsDragging] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [serverMessage, setServerMessage] = useState(null);
  const [csrfToken, setCsrfToken] = useState('');
  const [submitted, setSubmitted] = useState(false);
  const panelHeadingRef = useRef(null);

  useEffect(() => {
    fetch('/api/apply.php', { credentials: 'same-origin' })
      .then((response) => response.ok ? response.json() : Promise.reject())
      .then((data) => setCsrfToken(data.csrf_token || ''))
      .catch(() => setCsrfToken(''));
  }, []);

  const years = useMemo(() => {
    const latest = new Date().getFullYear() - 18;
    return Array.from({ length: latest - 1949 }, (_, index) => String(latest - index));
  }, []);

  function updateField(event) {
    const { name, type, checked, value } = event.target;
    setForm((previous) => ({ ...previous, [name]: type === 'checkbox' ? checked : value }));
    setErrors((previous) => ({ ...previous, [name]: undefined }));
    setServerMessage(null);
  }

  function validateAll() {
    const nextErrors = {};
    const nameLength = form.full_name.trim().length;
    if (nameLength < 6) nextErrors.full_name = 'اكتب الاسم الكامل كما يظهر في الهوية.';
    if (!/^\S+@\S+\.\S+$/.test(form.email)) nextErrors.email = 'أدخل بريدًا إلكترونيًا صحيحًا.';
    if (!/^[0-9+\-\s()]{8,20}$/.test(form.phone)) nextErrors.phone = 'أدخل رقم جوال صحيحًا مع رمز الدولة عند الحاجة.';
    ['birth_day', 'birth_month', 'birth_year'].forEach((key) => {
      if (!form[key]) nextErrors[key] = 'أكمل تاريخ الميلاد.';
    });
    if (form.birth_day && form.birth_month && form.birth_year) {
      const date = new Date(Number(form.birth_year), Number(form.birth_month) - 1, Number(form.birth_day));
      if (date.getFullYear() !== Number(form.birth_year) || date.getMonth() !== Number(form.birth_month) - 1 || date.getDate() !== Number(form.birth_day)) {
        nextErrors.birth_day = 'اختر تاريخ ميلاد صالحًا.';
      }
    }
    if (!form.city.trim()) nextErrors.city = 'أدخل المدينة.';
    if (!form.nationality.trim()) nextErrors.nationality = 'أدخل الجنسية.';
    if (!form.position) nextErrors.position = 'اختر الوظيفة المطلوبة.';
    if (!form.availability) nextErrors.availability = 'اختر حالة التفرغ.';
    if (!form.applied_before) nextErrors.applied_before = 'اختر الإجابة.';
    if (!form.education) nextErrors.education = 'اختر المؤهل العلمي.';
    if (!form.major.trim()) nextErrors.major = 'أدخل التخصص العلمي.';
    if (!form.experience) nextErrors.experience = 'اختر سنوات الخبرة.';
    if (!cvFile) nextErrors.cv_file = 'أضف سيرتك الذاتية بصيغة PDF.';
    if (cvFile && cvFile.size > 5 * 1024 * 1024) nextErrors.cv_file = 'حجم الملف يتجاوز 5 ميجابايت.';
    if (cvFile && cvFile.type !== 'application/pdf' && !cvFile.name.toLowerCase().endsWith('.pdf')) nextErrors.cv_file = 'السيرة الذاتية يجب أن تكون بصيغة PDF.';
    if (!form.privacy_consent) nextErrors.privacy_consent = 'يلزم قبول استخدام البيانات لأغراض التوظيف.';
    return nextErrors;
  }

  function moveToStep(index) {
    setCurrentStep(index);
    setFurthestStep((value) => Math.max(value, index));
    requestAnimationFrame(() => {
      panelHeadingRef.current?.focus();
      document.querySelector('.application-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  }

  function handleNext() {
    const allErrors = validateAll();
    const stepErrors = Object.fromEntries(Object.entries(allErrors).filter(([key]) => stepFields[currentStep].includes(key)));
    if (Object.keys(stepErrors).length) {
      setErrors((previous) => ({ ...previous, ...stepErrors }));
      return;
    }
    moveToStep(currentStep + 1);
  }

  function selectFile(file) {
    if (!file) return;
    setCvFile(file);
    setErrors((previous) => ({ ...previous, cv_file: undefined }));
  }

  async function handleSubmit(event) {
    event.preventDefault();
    const allErrors = validateAll();
    if (Object.keys(allErrors).length) {
      setErrors(allErrors);
      const firstBadStep = stepFields.findIndex((fields) => fields.some((field) => allErrors[field]));
      if (firstBadStep >= 0) moveToStep(firstBadStep);
      return;
    }

    setIsSubmitting(true);
    setServerMessage(null);
    try {
      let token = csrfToken;
      if (!token) {
        const tokenResponse = await fetch('/api/apply.php', { credentials: 'same-origin' });
        const tokenData = await tokenResponse.json();
        token = tokenData.csrf_token;
        setCsrfToken(token);
      }
      const payload = new FormData();
      Object.entries(form).forEach(([key, value]) => {
        if (key !== 'privacy_consent' || value === true) payload.append(key, value === true ? '1' : value);
      });
      payload.append('csrf_token', token);
      payload.append('cv_file', cvFile);
      const response = await fetch('/api/apply.php', { method: 'POST', body: payload, credentials: 'same-origin' });
      const data = await response.json();
      if (!response.ok || !data.success) throw new Error(data.message || 'تعذر إرسال الطلب الآن.');
      setSubmitted(true);
      setServerMessage({ type: 'success', text: data.message });
    } catch (error) {
      setServerMessage({ type: 'error', text: error.message || 'تعذر الاتصال بالخادم. حاول مرة أخرى لاحقًا.' });
    } finally {
      setIsSubmitting(false);
    }
  }

  const summary = [
    ['الاسم', form.full_name || '—'],
    ['البريد الإلكتروني', form.email || '—'],
    ['الوظيفة المطلوبة', optionLabel(positions, form.position)],
    ['المدينة', form.city || '—'],
    ['المؤهل', form.education || '—'],
    ['الخبرة', optionLabel(experienceOptions, form.experience)],
  ];

  return (
    <div className="app">
      <header className="topbar">
        <div className="topbar-inner">
          <a className="brand" href="/" aria-label="نجيك - الرئيسية"><span className="brand-mark">ن</span><span>نجيك</span></a>
          <span className="topbar-label">بوابة الفرص المهنية</span>
        </div>
      </header>

      <main>
        <section className="hero" aria-labelledby="page-title">
          <div className="hero-inner">
            <div><p className="eyebrow">اصنع أثرًا معنا</p><h1 id="page-title">مكانك القادم قد يكون بيننا.</h1><p className="hero-copy">نبحث عن أشخاص يؤمنون بأن الخدمة الممتازة تبدأ بفريق استثنائي. شاركنا خبرتك وطموحك، ودعنا نتعرّف عليك.</p></div>
            <div className="hero-facts"><div className="fact"><strong>3–5 دقائق</strong><span>الوقت المتوقع لإكمال الطلب</span></div><div className="fact"><strong>PDF فقط</strong><span>السيرة الذاتية بحد أقصى 5 MB</span></div></div>
          </div>
        </section>

        <div className="page-shell">
          <aside className="journey-card" aria-labelledby="journey-title">
            <h2 id="journey-title">ماذا بعد التقديم؟</h2><p>رحلة واضحة من الطلب إلى القرار.</p>
            <ol>
              {[['استلام الطلب', 'نسجّل بياناتك وسيرتك الذاتية.'], ['مراجعة الفريق', 'نراجع ملاءمة خبراتك للفرص.'], ['التواصل والمقابلة', 'نتواصل مع المرشحين الأنسب.'], ['القرار', 'نشاركك الخطوة التالية بوضوح.']].map(([title, text], index) => <li key={title}><span className="journey-number">{index + 1}</span><div><strong>{title}</strong><span>{text}</span></div></li>)}
            </ol>
            <div className="privacy-note">تُستخدم بياناتك لأغراض التوظيف فقط، ولا نطلب منك أي رسوم خلال عملية التوظيف.</div>
          </aside>

          <section className="application-card" aria-labelledby="application-title">
            {submitted ? (
              <div className="success-screen" role="status">
                <div className="success-icon"><CheckIcon /></div><p className="panel-kicker">تم إرسال الطلب</p><h2>شكرًا لاهتمامك بالانضمام إلى نجيك.</h2><p>{serverMessage?.text}</p><a className="button button-primary" href="/">العودة إلى الرئيسية</a>
              </div>
            ) : (
              <>
                <div className="application-head">
                  <div className="application-head-row"><div><h2 id="application-title">طلب الانضمام</h2><p>أكمل البيانات التالية، ثم راجعها قبل الإرسال.</p></div><span className="time-badge"><ClockIcon />3–5 دقائق</span></div>
                  <nav className="stepper" aria-label="خطوات نموذج التوظيف">
                    {steps.map((step, index) => <button key={step.short} type="button" className={`step-button ${index === currentStep ? 'is-active' : ''} ${index < currentStep ? 'is-complete' : ''}`} onClick={() => index <= furthestStep && moveToStep(index)} disabled={index > furthestStep} aria-current={index === currentStep ? 'step' : undefined}><span className="step-bar" /><span className="step-label">{step.short}</span></button>)}
                  </nav>
                </div>

                {serverMessage?.type === 'error' && <div className="alert alert-error" role="alert">{serverMessage.text}</div>}

                <form className="application-form" onSubmit={handleSubmit} noValidate>
                  <section className="step-panel" aria-labelledby={`step-${currentStep}-title`}>
                    <div className="panel-heading"><span className="panel-kicker">الخطوة {currentStep + 1} من 4</span><h3 id={`step-${currentStep}-title`} tabIndex="-1" ref={panelHeadingRef}>{steps[currentStep].title}</h3><p>{steps[currentStep].description}</p></div>

                    {currentStep === 0 && <div className="form-grid">
                      <div className="full"><Field label="الاسم الكامل (رباعي)" name="full_name" error={errors.full_name}><TextInput name="full_name" value={form.full_name} onChange={updateField} error={errors.full_name} autoComplete="name" maxLength="120" /></Field></div>
                      <Field label="البريد الإلكتروني" name="email" error={errors.email} help="سنستخدمه للتواصل بخصوص طلبك."><TextInput className="ltr" type="email" name="email" value={form.email} onChange={updateField} error={errors.email} placeholder="name@example.com" autoComplete="email" /></Field>
                      <Field label="رقم الجوال" name="phone" error={errors.phone}><TextInput className="ltr" type="tel" name="phone" value={form.phone} onChange={updateField} error={errors.phone} placeholder="+966 5X XXX XXXX" autoComplete="tel" /></Field>
                      <div className="field full"><span className="group-label">تاريخ الميلاد <span className="required">*</span></span><div className="date-grid">
                        <select name="birth_day" aria-label="اليوم" value={form.birth_day} onChange={updateField} aria-invalid={Boolean(errors.birth_day)}><option value="">اليوم</option>{Array.from({ length: 31 }, (_, index) => <option key={index + 1} value={index + 1}>{index + 1}</option>)}</select>
                        <select name="birth_month" aria-label="الشهر" value={form.birth_month} onChange={updateField} aria-invalid={Boolean(errors.birth_month)}><option value="">الشهر</option>{months.map((month, index) => <option key={month} value={index + 1}>{month}</option>)}</select>
                        <select name="birth_year" aria-label="السنة" value={form.birth_year} onChange={updateField} aria-invalid={Boolean(errors.birth_year)}><option value="">السنة</option>{years.map((year) => <option key={year} value={year}>{year}</option>)}</select>
                      </div>{errors.birth_day && <small className="field-error">{errors.birth_day}</small>}</div>
                      <Field label="المدينة" name="city" error={errors.city}><TextInput name="city" value={form.city} onChange={updateField} error={errors.city} placeholder="مثال: جدة" autoComplete="address-level2" /></Field>
                      <Field label="الجنسية" name="nationality" error={errors.nationality}><TextInput name="nationality" value={form.nationality} onChange={updateField} error={errors.nationality} autoComplete="country-name" /></Field>
                    </div>}

                    {currentStep === 1 && <div className="form-grid">
                      <div className="full"><Field label="المسمى الوظيفي المتقدم له" name="position" error={errors.position}><SelectInput name="position" value={form.position} onChange={updateField} error={errors.position} placeholder="اختر الوظيفة" options={positions} /></Field></div>
                      <div className="full"><Field label="الوظيفة الحالية" name="current_job" optional><TextInput name="current_job" value={form.current_job} onChange={updateField} placeholder="اكتب مسماك الحالي إن وجد" /></Field></div>
                      <Field label="حالة التفرغ" name="availability" error={errors.availability}><SelectInput name="availability" value={form.availability} onChange={updateField} error={errors.availability} placeholder="اختر حالة التفرغ" options={availabilityOptions} /></Field>
                      <Field label="هل سبق التقديم لدينا؟" name="applied_before" error={errors.applied_before}><SelectInput name="applied_before" value={form.applied_before} onChange={updateField} error={errors.applied_before} placeholder="اختر الإجابة" options={appliedBeforeOptions} /></Field>
                    </div>}

                    {currentStep === 2 && <div className="form-grid">
                      <Field label="أعلى مؤهل علمي" name="education" error={errors.education}><SelectInput name="education" value={form.education} onChange={updateField} error={errors.education} placeholder="اختر المؤهل" options={educationOptions.map((value) => [value, value])} /></Field>
                      <Field label="التخصص العلمي" name="major" error={errors.major}><TextInput name="major" value={form.major} onChange={updateField} error={errors.major} placeholder="مثال: علوم الحاسب" /></Field>
                      <div className="full"><Field label="سنوات الخبرة" name="experience" error={errors.experience}><SelectInput name="experience" value={form.experience} onChange={updateField} error={errors.experience} placeholder="اختر سنوات الخبرة" options={experienceOptions} /></Field></div>
                    </div>}

                    {currentStep === 3 && <>
                      <div className="field"><label htmlFor="cv_file">السيرة الذاتية <span className="required">*</span></label><label className={`upload-box ${isDragging ? 'is-dragging' : ''}`} onDragEnter={(event) => { event.preventDefault(); setIsDragging(true); }} onDragOver={(event) => event.preventDefault()} onDragLeave={() => setIsDragging(false)} onDrop={(event) => { event.preventDefault(); setIsDragging(false); selectFile(event.dataTransfer.files[0]); }}><input id="cv_file" type="file" accept="application/pdf,.pdf" onChange={(event) => selectFile(event.target.files[0])} /><span><span className="upload-icon"><UploadIcon /></span><span className="upload-title">اسحب ملف PDF هنا أو اضغط للاختيار</span><span className="upload-hint">ملف واحد بصيغة PDF، بحد أقصى 5 MB</span></span></label>{cvFile && !errors.cv_file && <p className="file-status">تم اختيار: {cvFile.name} ({(cvFile.size / 1048576).toFixed(1)} MB)</p>}{errors.cv_file && <small className="field-error">{errors.cv_file}</small>}</div>
                      <div className="review-card"><h4>ملخص طلبك</h4><div className="review-grid">{summary.map(([label, value]) => <div key={label}><small>{label}</small><strong>{value}</strong></div>)}</div></div>
                      <label className="consent"><input type="checkbox" name="privacy_consent" checked={form.privacy_consent} onChange={updateField} /><span>أوافق على استخدام بياناتي وسيرتي الذاتية لغرض تقييم طلب التوظيف والتواصل معي بشأن الفرص المناسبة.</span></label>{errors.privacy_consent && <small className="field-error">{errors.privacy_consent}</small>}
                      <button className="submit-button" type="submit" disabled={isSubmitting}>{isSubmitting ? 'جارٍ إرسال طلبك…' : 'إرسال طلب التوظيف'}</button>
                    </>}
                  </section>

                  <div className="form-navigation"><button className="button button-secondary" type="button" onClick={() => moveToStep(currentStep - 1)} hidden={currentStep === 0}><ArrowIcon direction="right" />السابق</button>{currentStep < steps.length - 1 && <button className="button button-primary" type="button" onClick={handleNext}>التالي<ArrowIcon /></button>}</div>
                </form>
              </>
            )}
          </section>
        </div>
      </main>
      <footer className="footer"><div className="footer-inner"><p><strong>نجيك لبابك التجارية</strong> — نصنع تجربة خدمة أقرب وأسهل.</p><p>© {new Date().getFullYear()} جميع الحقوق محفوظة</p></div></footer>
    </div>
  );
}

export default App;
