---
description: how to build the patient app apk
---

Follow these steps to build the latest version of the Patient App on your machine:

### 1. Prerequisites
Ensure you have the following installed on your computer:
- **Flutter SDK**: [Install Flutter](https://docs.flutter.dev/get-started/install)
- **Android Studio**: With "Android SDK" and "Command-line Tools" installed.
- **Java (JDK)**: Version 17 is recommended for modern Flutter apps.

### 2. Prepare the Code
Open your terminal in the `labo_dz_app` directory and run:

```powershell
# Get all necessary packages
flutter pub get
```

### 3. Build the APK
// turbo
Run the following command to generate the release APK:

```powershell
flutter build apk --release --no-tree-shake-icons
```

### 4. Locate the Result
Once the command finishes, your new APK will be located at:
`[Project_Folder]\labo_dz_app\build\app\outputs\flutter-apk\app-release.apk`

---
### 💡 How to Trigger Automatic Updates in the Future
When you make changes to the app and want patients to receive a "Update Available" prompt:
1. Open `labo_dz_app\pubspec.yaml` and increment the `version` (e.g., from `1.0.1+1` to `1.0.2+1`).
2. Build the new APK using the steps above.
3. Upload the new APK to your server.
4. Update the `$version` value in `app\Http\Controllers\Api\PatientApiController.php` to match the new version (`1.0.2`).
