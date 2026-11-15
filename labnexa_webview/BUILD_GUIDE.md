# LabNexa WebView App — Build Guide

## ما هذا؟
تطبيق Android خفيف الوزن (~2.5-4 MB) يعمل كـ WebView wrapper يفتح موقع LabNexa.

| خاصية | Flutter APK الحالي | هذا التطبيق |
|--------|-------------------|-------------|
| الحجم | 54 MB | **~2.5-4 MB** |
| المحرك | Flutter engine (50 MB lib) | Android System WebView (مدمج في الجهاز) |
| التصميم | يعتمد Flutter | يعتمد موقع LabNexa (نفس الموقع) |
| التحديثات | يحتاج رفع APK جديد | الموقع يتحدث تلقائياً |
| الوظائف | حجز + نتائج | حجز + نتائج + كل الموقع |

## متطلبات البناء
1. **Android Studio** (Hedgehog أو أحدث) — [تحميل](https://developer.android.com/studio)
2. **Android SDK** — API 21 (Lollipop) إلى API 34
3. **JDK 17** (مدمج مع Android Studio الحديث)

## خطوات البناء

### 1. افتح المشروع في Android Studio
```
File > Open > ابحث عن: C:\Users\DELL\Downloads\website\test\Labo_dz\labnexa_webview
```

### 2. تأكيد عنوان الموقع (اختياري)
افتح `app/src/main/java/dz/labnexa/app/MainActivity.java` وعدل هذا السطر:
```java
private static final String BASE_URL =
    "http://zsw8k8c4k8o4kw008skssgkc.194.34.232.105.sslip.io";
```
أبدله بالدومين الخاص بك إذا تغيّر.

### 3. بناء APK للإنتاج (Release)
```
Build > Generate Signed Bundle / APK > APK > Release
```
أو من Terminal:
```bash
./gradlew assembleRelease
```
الملف الناتج: `app/build/outputs/apk/arm64-v8a/release/app-arm64-v8a-release.apk`

> **حجم متوقع:** 2.5–3.5 MB بدلاً من 54 MB

### 4. رفعه على الموقع
بعد البناء:
1. انسخ ملف APK إلى `public/labnexa_v1.2.0.apk`
2. عدّل في `PatientApiController.php`:
   ```php
   'version' => '1.2.0',
   'download_url' => $this->getFullUrl($request, 'labnexa_v1.2.0.apk'),
   ```

## البنية الكاملة للمشروع
```
labnexa_webview/
├── app/
│   ├── src/main/
│   │   ├── java/dz/labnexa/app/
│   │   │   ├── SplashActivity.java   ← شاشة البداية (1.8 ثانية)
│   │   │   └── MainActivity.java     ← WebView الرئيسي
│   │   ├── res/
│   │   │   ├── layout/
│   │   │   │   ├── activity_splash.xml
│   │   │   │   └── activity_main.xml
│   │   │   ├── drawable/
│   │   │   │   ├── bg_splash.xml     ← تدرج الألوان (gradient)
│   │   │   │   └── ic_splash_logo.xml ← شعار vector
│   │   │   ├── values/
│   │   │   │   ├── strings.xml
│   │   │   │   └── styles.xml
│   │   │   └── mipmap-*/
│   │   │       └── ic_launcher.xml
│   │   └── AndroidManifest.xml
│   ├── build.gradle
│   └── proguard-rules.pro
├── build.gradle
└── settings.gradle
```

## الميزات المدمجة
- ✅ شاشة splash مع تحريك (fade-in)
- ✅ Swipe-to-refresh لتحديث الصفحة
- ✅ شريط تقدم (ProgressBar أزرق رفيع)
- ✅ زر Back = رجوع في المتصفح (يسأل عند الخروج)
- ✅ دعم رفع الملفات (وصفات طبية)
- ✅ صفحة offline عند انقطاع الإنترنت
- ✅ Deep-links: `labnexa://booking` و `labnexa://results`
- ✅ فتح روابط خارجية في المتصفح تلقائياً
- ✅ User-agent يحتوي على "LabNexaApp/1.2.0"
