# Proguard rules for LabNexa WebView App
# Keep WebView JavaScript interface classes (none needed here)
-keepclassmembers class * {
    @android.webkit.JavascriptInterface <methods>;
}
# Keep all Activity classes
-keep class dz.labnexa.app.** { *; }
