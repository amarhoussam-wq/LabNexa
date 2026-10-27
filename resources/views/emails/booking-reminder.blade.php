<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تذكير بموعد التحليل وتعليمات التحضير</title>
    <style>
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #3182ce; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; margin: -30px -30px 20px -30px; }
        .content { padding: 20px 0; }
        .appointment-details { background: #ebf8ff; padding: 15px; border-radius: 8px; margin: 20px 0; border-right: 4px solid #3182ce; }
        .prep-rules { background: #fffaf0; padding: 15px; border-radius: 5px; border: 1px solid #feebc8; margin: 20px 0; color: #744210; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #2c5282; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        ul { padding-right: 20px; }
        li { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>تذكير بموعد التحليل وتعليمات التحضير</h2>
        </div>

        <div class="content">
            <p>السيد/ة <strong>{{ $patientName }}</strong>،</p>

            <p>نود تذكيركم بأن موعد تحاليلكم الطبية في مختبرنا قد اقترب (يتبقى حوالي 12 ساعة).</p>

            <div class="appointment-details">
                <p><strong>التاريخ:</strong> {{ $analysisDate }}</p>
                <p><strong>الوقت:</strong> {{ $analysisTime }}</p>
            </div>

            <p>لضمان الحصول على نتائج دقيقة وموثوقة، <strong>يرجى الالتزام الصارم بالتعليمات الطبية التالية:</strong></p>

            <div class="prep-rules">
                <ul>
                    @foreach($smartMessage as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>

            <p>نحن في انتظاركم، ونتمنى لكم دوام الصحة والعافية.</p>
            
            <p style="text-align: center;">
                <a href="{{ url('/') }}" class="btn">زيارة موقع المختبر</a>
            </p>
        </div>

        <div class="footer">
            <p>مختبر labo.dz للتحاليل الطبية</p>
            <p>رقم الهاتف: 0111111153</p>
        </div>
    </div>
</body>
</html>
