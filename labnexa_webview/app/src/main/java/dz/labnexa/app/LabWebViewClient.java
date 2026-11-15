package dz.labnexa.app;

import android.content.Intent;
import android.graphics.Bitmap;
import android.net.Uri;
import android.net.http.SslError;
import android.view.View;
import android.webkit.SslErrorHandler;
import android.webkit.WebResourceRequest;
import android.webkit.WebView;
import android.webkit.WebViewClient;

public class LabWebViewClient extends WebViewClient {
    private final MainActivity activity;

    public LabWebViewClient(MainActivity activity) {
        this.activity = activity;
    }

    @Override
    public void onPageStarted(WebView view, String url, Bitmap favicon) {
        if (activity.progressBar != null) {
            activity.progressBar.setVisibility(View.VISIBLE);
            activity.progressBar.setProgress(0);
        }
    }

    @Override
    public void onPageFinished(WebView view, String url) {
        if (activity.progressBar != null) {
            activity.progressBar.setVisibility(View.GONE);
        }
    }

    @Override
    public void onReceivedError(WebView view, int errorCode, String description, String failingUrl) {
        if (failingUrl != null && failingUrl.startsWith(MainActivity.BASE_URL)) {
            String html = "<!DOCTYPE html><html dir='rtl'><head><meta charset='utf-8'>"
                + "<meta name='viewport' content='width=device-width,initial-scale=1'>"
                + "<style>body{margin:0;display:flex;flex-direction:column;align-items:center;"
                + "justify-content:center;height:100vh;font-family:sans-serif;"
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
            view.loadData(html, "text/html; charset=utf-8", "utf-8");
        }
    }

    @Override
    public void onReceivedSslError(WebView view, SslErrorHandler handler, SslError error) {
        handler.proceed();
    }

    @Override
    public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest req) {
        String url = req.getUrl().toString();
        if (url.startsWith(MainActivity.BASE_URL)) {
            return false;
        }
        if (url.startsWith("http://") || url.startsWith("https://")) {
            activity.startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse(url)));
            return true;
        }
        if (url.startsWith("tel:") || url.startsWith("mailto:") || url.startsWith("whatsapp:")) {
            try {
                activity.startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse(url)));
            } catch (Exception ignored) {}
            return true;
        }
        return false;
    }
}
