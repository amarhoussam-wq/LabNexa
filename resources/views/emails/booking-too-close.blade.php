<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تنبيه: عذراً، يجب إعادة جدولة موعدكم</title>
    <style>
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #c53030; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; margin: -30px -30px 20px -30px; }
        .content { padding: 20px 0; }
        .appointment-details { background: #fff5f5; padding: 15px; border-radius: 8px; margin: 20px 0; border-right: 4px solid #c53030; }
        .important-note { background: #fffaf0; padding: 15px; border-radius: 5px; border: 1px solid #feebc8; margin: 20px 0; color: #744210; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #2c5282; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>تنبيه بخصوص موعد التحليل</h2>
        </div>

        <div class="content">
            <p>السيد/ة <strong>{{ $patientName }}</strong>،</p>

            <p>نعتذر منكم، ولكن تم إلغاء حجزكم الآلي للموعد المطلوب للأسباب الطبية التالية:</p>

            <div class="appointment-details">
                <p><strong>الموعد المرفوض:</strong> {{ $analysisDate }} الساعة {{ $analysisTime }}</p>
                <p><strong>التحاليل المعنية:</strong></p>
                <ul>
                    @foreach($fastingAnalyses as $analysis)
                        <li>{{ $analysis->name }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="important-note">
                <p>⚠️ <strong>سبب الإلغاء:</strong><br>
                {{ $rejectionReason ?? 'التحاليل تتطلب شروط تحضير خاصة تتعارض مع الوقت الذي اخترتموه.' }}
                </p>
            </div>

            <p>يرجى إعادة الحجز عبر الموقع وتحديد تاريخ ووقت يتوافق مع تعليمات شروط التحليل لضمان دقة نتائجكم الطبية.</p>
            
            <p style="text-align: center;">
                <a href="{{ url('/') }}" class="btn">العودة للموقع</a>
            </p>
        </div>

        <div class="footer">
            <p>مع تحيات، فريق LabNexa</p>
            <p>رقم الهاتف: 0111111153</p>
        </div>
    </div>
</body>
</html>
