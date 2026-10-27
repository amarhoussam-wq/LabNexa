<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 10px; }
        .header { background-color: #2c3e50; color: #ffffff; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 30px; background-color: #fdfdfd; }
        .credentials-box { background-color: #f8f9fa; border: 1px dashed #2c3e50; padding: 20px; border-radius: 8px; margin: 25px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #3498db; color: #ffffff !important; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 15px; }
        .bilingual-split { display: flex; gap: 20px; }
        .lang-section { flex: 1; }
        hr { border: 0; border-top: 1px solid #eee; margin: 25px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>LabNexa - 📱 Application Mobile</h2>
        </div>
        <div class="content">
            <div class="lang-section" style="text-align: right; direction: rtl;">
                <h3>أهلاً بك {{ $patient->name }}،</h3>
                <p>لقد تم تفعيل وصولك لتطبيق المختبر بنجاح. يمكنك الآن متابعة نتائج تحاليلك وحجز مواعيدك مباشرة من هاتفك.</p>
                <div class="credentials-box">
                    <strong>بيانات الدخول:</strong><br>
                    اسم المستخدم (الإيميل): <code>{{ $email }}</code><br>
                    كلمة المرور: <code>{{ $password }}</code>
                </div>
                <p>يرجى تغيير كلمة المرور بعد تسجيل دخولك الأول لضمان أمان حسابك.</p>
            </div>

            <hr>

            <div class="lang-section" style="text-align: left; direction: ltr;">
                <h3>Bonjour {{ $patient->name }},</h3>
                <p>Votre accès à l'application du laboratoire a été activé avec succès. Vous pouvez désormais suivre vos résultats et prendre rendez-vous depuis votre mobile.</p>
                <div class="credentials-box">
                    <strong>Identifiants de connexion :</strong><br>
                    Email : <code>{{ $email }}</code><br>
                    Mot de passe : <code>{{ $password }}</code>
                </div>
                <p>Veuillez changer votre mot de passe après votre première connexion pour sécuriser votre compte.</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/access') }}" class="btn">Accéder au Portail / الدخول للموقع</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} LabNexa. Tous droits réservés / جميع الحقوق محفوظة.
        </div>
    </div>
</body>
</html>
