$ErrorActionPreference = "Stop"

$javaHome = "C:\Program Files\Android\Android Studio\jbr"
$env:JAVA_HOME = $javaHome
$env:PATH = "$javaHome\bin;$env:PATH"

$androidJar = "C:\Users\DELL\AppData\Local\Android\Sdk\platforms\android-34\android.jar"
$buildTools = "C:\Users\DELL\AppData\Local\Android\Sdk\build-tools\34.0.0"
$aapt = "$buildTools\aapt.exe"
$d8 = "$buildTools\d8.bat"
$zipalign = "$buildTools\zipalign.exe"
$apksigner = "$buildTools\apksigner.bat"
$javac = "$javaHome\bin\javac.exe"
$keytool = "$javaHome\bin\keytool.exe"

$projDir = "C:\Users\DELL\Downloads\website\test\Labo_dz\labnexa_webview\app\src\main"
$manifest = "$projDir\AndroidManifest.xml"
$res = "$projDir\res"
$java = "$projDir\java\dz\labnexa\app"
$assetsDir = "$projDir\assets"

$workDir = "C:\Users\DELL\Downloads\website\test\Labo_dz\build_apk_tmp"
if (Test-Path $workDir) { Remove-Item -Recurse -Force $workDir }
New-Item -ItemType Directory -Path "$workDir\gen" -Force | Out-Null
New-Item -ItemType Directory -Path "$workDir\classes" -Force | Out-Null
New-Item -ItemType Directory -Path "$workDir\dex" -Force | Out-Null

Write-Host "1. Running aapt to generate R.java (Target SDK 34)..."
& $aapt package -f -m -J "$workDir\gen" -M "$manifest" -S "$res" -I "$androidJar" --min-sdk-version 24 --target-sdk-version 34

Write-Host "2. Compiling Java sources with JDK 17 javac..."
$javaFiles = @(
    "$workDir\gen\dz\labnexa\app\R.java",
    "$java\SplashActivity.java",
    "$java\MainActivity.java",
    "$java\LabWebViewClient.java",
    "$java\LabWebChromeClient.java"
)
& $javac -source 1.8 -target 1.8 -g -cp "$androidJar" -d "$workDir\classes" $javaFiles

Write-Host "3. Running D8 to create classes.dex (API 24 - 34)..."
$classFiles = (Get-ChildItem -Path "$workDir\classes" -Recurse -Filter "*.class").FullName
& cmd.exe /c "$d8 --lib ""$androidJar"" --min-api 24 --output ""$workDir\dex"" $($classFiles -join ' ')"

Write-Host "4. Packaging unaligned APK with assets (Target SDK 34)..."
& $aapt package -f -M "$manifest" -S "$res" -A "$assetsDir" -I "$androidJar" -F "$workDir\unaligned.apk" --min-sdk-version 24 --target-sdk-version 34

Write-Host "5. Adding classes.dex to APK..."
Set-Location "$workDir\dex"
& $aapt add "$workDir\unaligned.apk" "classes.dex"
Set-Location "C:\Users\DELL\Downloads\website\test\Labo_dz"

Write-Host "6. Running zipalign..."
& $zipalign -f -p 4 "$workDir\unaligned.apk" "$workDir\aligned.apk"

Write-Host "7. Signing APK (V1 + V2 + V3 Signature Scheme)..."
$keystore = "$workDir\debug.keystore"
& $keytool -genkeypair -v -keystore $keystore -alias androiddebugkey -storepass android -keypass android -keyalg RSA -keysize 2048 -validity 10000 -dname "CN=LabNexa,O=Medical,C=DZ"

$outApk = "C:\Users\DELL\Downloads\website\test\Labo_dz\public\labnexa_lite.apk"
& cmd.exe /c "$apksigner sign --ks ""$keystore"" --ks-pass pass:android --ks-key-alias androiddebugkey --key-pass pass:android --min-sdk-version 24 --v1-signing-enabled true --v2-signing-enabled true --v3-signing-enabled true --out ""$outApk"" ""$workDir\aligned.apk"""

# Also copy to labnexa_v1.1.4.apk and labnexa_v1.2.0.apk
Copy-Item $outApk "C:\Users\DELL\Downloads\website\test\Labo_dz\public\labnexa_v1.1.4.apk" -Force
Copy-Item $outApk "C:\Users\DELL\Downloads\website\test\Labo_dz\public\labnexa_v1.2.0.apk" -Force

Write-Host "=== BUILD SUCCESSFUL! ==="
$finalFile = Get-Item $outApk
Write-Host "APK Created at: $($finalFile.FullName)"
Write-Host "APK Size: $([math]::Round($finalFile.Length / 1MB, 2)) MB ($($finalFile.Length) bytes)"
