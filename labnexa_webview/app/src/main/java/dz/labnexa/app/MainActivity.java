package dz.labnexa.app;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.net.Uri;
import android.net.http.SslError;
import android.os.Build;
import android.os.Bundle;
import android.os.Message;
import android.view.View;
import android.webkit.*;
import android.widget.ProgressBar;

import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

/**
 * MainActivity – Full-screen WebView wrapper for LabNexa.
 *
 *  • Opens the patient portal by default
 *  • Handles deep-links: labnexa://booking → /#booking
 *                         labnexa://results → /access?tab=patient#results
 *  • Swipe-to-refresh supported
 *  • File chooser for prescription uploads
 *  • Back-press = browser back (not app exit)
 */
public class MainActivity extends Activity {

    // ─── Change this to your production domain ───────────────────────────────
    private static final String BASE_URL =
            "http://zsw8k8c4k8o4kw008skssgkc.194.34.232.105.sslip.io";
    // ─────────────────────────────────────────────────────────────────────────

    private static final String PATIENT_PORTAL = BASE_URL + "/access?tab=patient";
    private static final String BOOKING_PAGE   = BASE_URL + "/#booking";

    private WebView webView;
    private ProgressBar progressBar;
    private SwipeRefreshLayout swipeRefresh;
    private ValueCallback<Uri[]> fileChooserCallback;
    private static final int FILE_CHOOSER_RC = 101;
    private static final int STORAGE_PERM_RC = 201;

    // ─────────────────────────────────────────────────────────────────────────

    @SuppressLint({"SetJavaScriptEnabled", "InflateParams"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        progressBar   = findViewById(R.id.progress_bar);
        swipeRefresh  = findViewById(R.id.swipe_refresh);
        webView       = findViewById(R.id.webview);

        setupWebView();

        swipeRefresh.setColorSchemeColors(0xFF0066FF, 0xFF10B981);
        swipeRefresh.setOnRefreshListener(() -> webView.reload());

        // Handle deep-links
        String startUrl = resolveStartUrl(getIntent());
        webView.loadUrl(startUrl);
    }

    // ── Deep-link resolver ────────────────────────────────────────────────────
    private String resolveStartUrl(Intent intent) {
        if (intent != null && intent.getData() != null) {
            Uri deepLink = intent.getData();
            String host = deepLink.getHost();
            if ("booking".equals(host))  return BOOKING_PAGE;
            if ("results".equals(host))  return PATIENT_PORTAL + "#results";
        }
        return PATIENT_PORTAL;
    }

    // ── WebView setup ─────────────────────────────────────────────────────────
    @SuppressLint("SetJavaScriptEnabled")
    private void setupWebView() {
        WebSettings s = webView.getSettings();
        s.setJavaScriptEnabled(true);
        s.setDomStorageEnabled(true);
        s.setAllowFileAccess(true);
        s.setMixedContentMode(WebSettings.MIXED_CONTENT_ALWAYS_ALLOW);
        s.setCacheMode(WebSettings.LOAD_DEFAULT);
        s.setLoadWithOverviewMode(true);
        s.setUseWideViewPort(true);
        // Disable zoom controls – the site is already mobile-responsive
        s.setBuiltInZoomControls(false);
        s.setDisplayZoomControls(false);
        // User-agent keeps "LabNexaApp" so the server can detect the app
        s.setUserAgentString(s.getUserAgentString() + " LabNexaApp/1.2.0");

        webView.setWebViewClient(new WebViewClient() {

            @Override
            public void onPageStarted(WebView view, String url, Bitmap favicon) {
                progressBar.setVisibility(View.VISIBLE);
                progressBar.setProgress(0);
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                progressBar.setVisibility(View.GONE);
                swipeRefresh.setRefreshing(false);
                // Inject CSS to hide desktop-only elements if needed
                view.evaluateJavascript(
                    "document.documentElement.style.setProperty('--app-mode','webview');",
                    null);
            }

            @Override
            public void onReceivedError(WebView view, int errorCode,
                                        String description, String failingUrl) {
                // Show offline HTML only on main page loads, not sub-resources
                if (failingUrl != null && failingUrl.startsWith(BASE_URL)) {
                    view.loadData(offlinePage(), "text/html; charset=utf-8", null);
                }
            }

            @Override
            public void onReceivedSslError(WebView view, SslErrorHandler handler,
                                           SslError error) {
                // Accept SSL issues (useful during Coolify staging)
                handler.proceed();
            }

            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest req) {
                String url = req.getUrl().toString();
                // Keep all LabNexa URLs inside the WebView
                if (url.startsWith(BASE_URL)) return false;
                // Open external URLs in the system browser
                if (url.startsWith("http") || url.startsWith("https")) {
                    startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse(url)));
                    return true;
                }
                // Handle tel:, mailto:, etc.
                if (url.startsWith("tel:") || url.startsWith("mailto:") ||
                    url.startsWith("whatsapp:")) {
                    try { startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse(url))); }
                    catch (Exception ignored) {}
                    return true;
                }
                return false;
            }
        });

        webView.setWebChromeClient(new WebChromeClient() {

            @Override
            public void onProgressChanged(WebView view, int newProgress) {
                progressBar.setProgress(newProgress);
                if (newProgress == 100) progressBar.setVisibility(View.GONE);
            }

            // File chooser (prescription uploads, PDFs)
            @Override
            public boolean onShowFileChooser(WebView view,
                                              ValueCallback<Uri[]> callback,
                                              FileChooserParams params) {
                fileChooserCallback = callback;
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
                    requestPermissions(new String[]{
                        Manifest.permission.READ_MEDIA_IMAGES
                    }, STORAGE_PERM_RC);
                } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                    requestPermissions(new String[]{
                        Manifest.permission.READ_EXTERNAL_STORAGE
                    }, STORAGE_PERM_RC);
                }
                Intent chooser = new Intent(Intent.ACTION_GET_CONTENT);
                chooser.addCategory(Intent.CATEGORY_OPENABLE);
                chooser.setType("*/*");
                chooser.putExtra(Intent.EXTRA_ALLOW_MULTIPLE, false);
                startActivityForResult(Intent.createChooser(chooser,
                    "اختر ملف"), FILE_CHOOSER_RC);
                return true;
            }

            // Allow alert / confirm dialogs from the web page
            @Override
            public boolean onJsAlert(WebView v, String url, String msg,
                                     JsResult result) {
                new AlertDialog.Builder(MainActivity.this)
                    .setMessage(msg)
                    .setPositiveButton("موافق", (d, w) -> result.confirm())
                    .setOnCancelListener(d -> result.cancel())
                    .create().show();
                return true;
            }
        });
    }

    // ── Back button = browser back (exit only when no history left) ───────────
    @Override
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            new AlertDialog.Builder(this)
                .setTitle("LabNexa")
                .setMessage("هل تريد الخروج من التطبيق؟")
                .setPositiveButton("خروج", (d, w) -> finish())
                .setNegativeButton("إلغاء", null)
                .show();
        }
    }

    // ── File chooser result ───────────────────────────────────────────────────
    @Override
    protected void onActivityResult(int req, int res, Intent data) {
        if (req == FILE_CHOOSER_RC) {
            if (fileChooserCallback == null) return;
            Uri[] results = null;
            if (res == Activity.RESULT_OK && data != null && data.getData() != null) {
                results = new Uri[]{data.getData()};
            }
            fileChooserCallback.onReceiveValue(results);
            fileChooserCallback = null;
        }
    }

    // ── Offline page (shown when device has no internet) ─────────────────────
    private String offlinePage() {
        return "<!DOCTYPE html><html dir='rtl'><head><meta charset='utf-8'>"
            + "<meta name='viewport' content='width=device-width,initial-scale=1'>"
            + "<style>body{margin:0;display:flex;flex-direction:column;align-items:center;"
            + "justify-content:center;height:100vh;font-family:Tajawal,sans-serif;"
            + "background:#F0F9FF;color:#0369A1;text-align:center;padding:20px}"
            + "h2{font-size:1.4rem;margin-bottom:8px}p{color:#64748B;font-size:1rem}"
            + "button{margin-top:24px;background:#0066FF;color:#fff;border:none;"
            + "padding:12px 32px;border-radius:12px;font-size:1rem;cursor:pointer}"
            + "</style></head><body>"
            + "<div style='font-size:4rem'>📡</div>"
            + "<h2>لا يوجد اتصال بالإنترنت</h2>"
            + "<p>تأكد من اتصالك بالإنترنت ثم حاول مجدداً.</p>"
            + "<button onclick='window.location.reload()'>إعادة المحاولة</button>"
            + "</body></html>";
    }
}
