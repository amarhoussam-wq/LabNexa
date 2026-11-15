package dz.labnexa.app;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.net.Uri;
import android.net.http.SslError;
import android.os.Build;
import android.os.Bundle;
import android.view.View;
import android.webkit.JsResult;
import android.webkit.SslErrorHandler;
import android.webkit.ValueCallback;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ProgressBar;

public class MainActivity extends Activity implements DialogInterface.OnClickListener {

    public static final String LOCAL_APP_URL = "file:///android_asset/www/index.html";
    public static final String BASE_URL =
            "http://zsw8k8c4k8o4kw008skssgkc.194.34.232.105.sslip.io";

    public static final String APP_URL        = LOCAL_APP_URL;
    public static final String PATIENT_PORTAL = LOCAL_APP_URL;
    public static final String BOOKING_PAGE   = LOCAL_APP_URL;

    public WebView webView;
    public ProgressBar progressBar;
    public ValueCallback<Uri[]> fileChooserCallback;
    public static final int FILE_CHOOSER_RC = 101;
    public static final int STORAGE_PERM_RC = 201;

    @SuppressLint("SetJavaScriptEnabled")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        progressBar = (ProgressBar) findViewById(R.id.progress_bar);
        webView = (WebView) findViewById(R.id.webview);

        setupWebView();

        webView.loadUrl(LOCAL_APP_URL);
    }

    @SuppressLint("SetJavaScriptEnabled")
    private void setupWebView() {
        WebSettings s = webView.getSettings();
        s.setJavaScriptEnabled(true);
        s.setDomStorageEnabled(true);
        s.setAllowFileAccess(true);
        s.setAllowFileAccessFromFileURLs(true);
        s.setAllowUniversalAccessFromFileURLs(true);
        s.setMixedContentMode(WebSettings.MIXED_CONTENT_ALWAYS_ALLOW);
        s.setCacheMode(WebSettings.LOAD_DEFAULT);
        s.setLoadWithOverviewMode(true);
        s.setUseWideViewPort(true);
        s.setBuiltInZoomControls(false);
        s.setDisplayZoomControls(false);
        s.setUserAgentString(s.getUserAgentString() + " LabNexaApp/1.3.0");

        webView.setWebViewClient(new LabWebViewClient(this));
        webView.setWebChromeClient(new LabWebChromeClient(this));
    }

    @Override
    public void onClick(DialogInterface dialog, int which) {
        if (which == DialogInterface.BUTTON_POSITIVE) {
            finish();
        }
    }

    @Override
    public void onBackPressed() {
        if (webView != null && webView.canGoBack()) {
            webView.goBack();
        } else {
            new AlertDialog.Builder(this)
                .setTitle("LabNexa")
                .setMessage("هل تريد الخروج من التطبيق؟")
                .setPositiveButton("خروج", this)
                .setNegativeButton("إلغاء", (DialogInterface.OnClickListener) null)
                .show();
        }
    }

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
}
