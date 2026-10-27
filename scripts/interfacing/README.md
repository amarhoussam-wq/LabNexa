# Mindray BC-3000 / BC-Series Interfacing Middleware

This script acts as a bridge between your Mindray Hematology Analyzer and your Labo.dz Laravel Application.
It listens to the serial (RS-232) output of the machine, extracts the WBC, RBC, HGB, PLT, etc., and pushes them securely into the patient's electronic record automatically.

## Requirements
Make sure you have Python installed on your Windows machine.
1. Open Command Prompt or PowerShell as Administrator.
2. Install the required Python libraries:
   ```bash
   pip install pyserial requests
   ```

## Configuration
1. Connect the Mindray Analyzer to your PC using a Serial-to-USB cable.
2. Open Windows "Device Manager" -> "Ports (COM & LPT)" to find out which COM port was assigned to the cable (e.g., `COM3`, `COM4`).
3. Open `mindray_listener.py` in a text editor like Notepad.
4. Locate the line `COM_PORT = 'COM3'` and change it to match your actual port number.
5. If your Mindray machine is configured to use a different Baud Rate (default is usually 9600), change `BAUD_RATE = 9600` accordingly. You can find your machine's baud rate in the analyzer's "Setup -> Host Communication" menu.

## How to Run
Once configured, simply double-click the `mindray_listener.py` file to start it, or run it through the terminal:
```bash
python mindray_listener.py
```

Keep the black console window open while the lab is operating. Every time the Mindray finishes analyzing a blood sample, you will see the data appear in the console, and it will be immediately transferred to your Laravel website.

## Testing
To test if the connection to the Laravel API is working without waiting for a real blood sample:
1. Make sure your local Laravel application is running (`php artisan serve`).
2. You can use Postman to send a `POST` request to `http://127.0.0.1:8000/api/v1/interfacing/mindray/bc3000`
3. Send this JSON Payload:
```json
{
    "sample_id": "YOUR_PATIENT_RESERVATION_ID",
    "results": {
        "WBC": 6.5,
        "RBC": 4.8,
        "HGB": 14.0
    }
}
```
If it succeeds, it will inject these numbers directly into the patient's record on your website!
