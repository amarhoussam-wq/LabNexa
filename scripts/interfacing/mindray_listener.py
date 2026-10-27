import serial
import time
import re
import requests
import json

# ==========================================
# CONFIGURATION
# ==========================================
# 1. The COM port your Mindray is connected to
COM_PORT = 'COM3' 
BAUD_RATE = 9600

# 2. Your Laravel API URL
API_URL = 'http://127.0.0.1:8000/api/v1/interfacing/mindray/bc3000'

# ==========================================

def parse_mindray_data(raw_text):
    """
    Basic parser for Mindray BC-series text/ASTM outputs over Serial.
    Adjust regexes based on the exact structure of your BC-3000 printout.
    """
    results = {}
    sample_id = None
    
    # Try to find sample ID (Common formats: "ID: 1002" or "Sample ID:1002" or "ID 1002")
    id_match = re.search(r'(?i)(?:sample id|id|seq)[\s:=]+([A-Za-z0-9_-]+)', raw_text)
    if id_match:
        sample_id = id_match.group(1).strip()
        
    # Example regex to catch parameters like: WBC 5.6 10^9/L or WBC = 5.6
    # It looks for the parameter name, optional symbols, and a float value
    parameters = ['WBC', 'RBC', 'HGB', 'HCT', 'MCV', 'MCH', 'MCHC', 'PLT', 'LYM%', 'MID%', 'GRAN%']
    
    for param in parameters:
        # Matches: WBC 5.6 or WBC = 5.6 or WBC: 5.6
        match = re.search(rf'{param}[\s:=]+([\d\.]+)', raw_text, re.IGNORECASE)
        if match:
            results[param] = float(match.group(1))

    return sample_id, results

def send_to_laravel(sample_id, results):
    """
    Sends the parsed JSON payload to the Laravel backend.
    """
    payload = {
        "sample_id": sample_id,
        "results": results
    }
    
    print(f"\n[+] Sending Data to Laravel for Sample ID: {sample_id}")
    print(json.dumps(payload, indent=2))
    
    try:
        response = requests.post(API_URL, json=payload, timeout=5)
        if response.status_code == 200:
            print("[✓] Successfully saved to database!")
        else:
            print(f"[X] Backend Error {response.status_code}: {response.text}")
    except Exception as e:
        print(f"[!] Failed to connect to API: {str(e)}")

def start_listening():
    print(f"Starting Mindray BC-3000 Listener on port {COM_PORT}...")
    try:
        ser = serial.Serial(COM_PORT, BAUD_RATE, timeout=1)
        print("Connected to serial port successfully. Waiting for results...\n")
        
        buffer = ""
        last_receive_time = time.time()
        
        while True:
            if ser.in_waiting > 0:
                data = ser.read(ser.in_waiting).decode('ascii', errors='ignore')
                buffer += data
                last_receive_time = time.time()
                
            # If we haven't received data for 2 seconds but buffer has content, assume end of transmission
            if buffer and (time.time() - last_receive_time) > 2.0:
                print("\n" + "="*40)
                print("Received New Test Result Block:")
                print("="*40)
                # Parse
                sample_id, results = parse_mindray_data(buffer)
                
                if sample_id and results:
                    send_to_laravel(sample_id, results)
                else:
                    print("Could not parse a valid Sample ID or Results from block.")
                    print("Raw Text Dump:\n", buffer)
                    
                # Clear buffer for next patient
                buffer = ""
                
            time.sleep(0.1)
                
    except serial.SerialException as e:
        print(f"Error opening serial port {COM_PORT}: {str(e)}")
        print("Make sure the port is correct and not used by another program.")
    except KeyboardInterrupt:
        print("\nListener stopped by user.")

if __name__ == '__main__':
    start_listening()
