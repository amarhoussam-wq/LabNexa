# Stability & Reliability Guide (Coolify)

To ensure the project remains online and stable during the 2-month ministerial review, follow these configuration steps in your hosting provider and Coolify dashboard.

## 1. Coolify Configuration Settings

### Restart Policy
- **Setting:** `Always` or `Unless Stopped`.
- **Reason:** This ensures that if the server reboots (due to hosting renewal or power cycling), your project containers will start up automatically without manual intervention.

### Health Checks
- **Setting:** Path: `/`, Interval: `30s`, Timeout: `10s`.
- **Reason:** Coolify will automatically monitor if the site is "up". If it stops responding, Coolify will attempt to restart the container for you.

### Resource Allocation (CPU/RAM)
- Ensure the VPS has at least **2GB of RAM** and **2 vCPUs**.
- In Coolify, do not set hard memory limits unless necessary, or set them high enough (e.g., 1.5GB) to prevent "OOM (Out Of Memory) Kills".

## 2. Database & Data Safety

### Automated Backups
- **Action:** Visit the "Backups" tab in Coolify for your Database resource.
- **Frequency:** Set to `Daily`.
- **Retention:** Keep at least 7 days of backups.
- **Reason:** If the committee accidentally deletes data or a crash occurs, you can restore everything within minutes.

### SSD Space
- Ensure at least **20% free disk space** on your VPS. Linux systems can behave unpredictably or crash if the disk is full (often from logs).

## 3. Post-Renewal Checklist
When your hosting provider renews the VPS, perform these checks:
1.  **SSL Status:** Verify the site has a padlock (HTTPS). If not, click "Renew" in the Coolify SSL settings.
2.  **DNS Check:** If the server IP changed (rare for renewals), update your domain A records immediately.

## 4. Emergency Contacts
If the committee reports an error, always check the **Deployments** tab in Coolify. If you see a red "Failed" status, check the logs by clicking on the deployment logs icon.
