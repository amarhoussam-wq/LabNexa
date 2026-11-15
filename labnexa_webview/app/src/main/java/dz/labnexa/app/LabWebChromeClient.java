package dz.labnexa.app;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.Uri;
import android.view.View;
import android.webkit.JsResult;
import android.webkit.ValueCallback;
import android.webkit.WebChromeClient;
import android.webkit.WebView;

public class LabWebChromeClient extends WebChromeClient implements DialogInterface.OnClickListener {
    private final MainActivity activity;
    private JsResult currentResult;

    public LabWebChromeClient(MainActivity activity) {
        this.activity = activity;
    }

    @Override
    public void onProgressChanged(WebView view, int newProgress) {
        if (activity.progressBar != null) {
            activity.progressBar.setVisibility(View.VISIBLE);
            activity.progressBar.setProgress(newProgress);
            if (newProgress >= 100) {
                activity.progressBar.setVisibility(View.GONE);
            }
        }
    }

    @Override
    public boolean onShowFileChooser(WebView view, ValueCallback<Uri[]> callback, FileChooserParams params) {
        activity.fileChooserCallback = callback;
        Intent chooser = new Intent(Intent.ACTION_GET_CONTENT);
        chooser.addCategory(Intent.CATEGORY_OPENABLE);
        chooser.setType("*/*");
        chooser.putExtra(Intent.EXTRA_ALLOW_MULTIPLE, false);
        activity.startActivityForResult(Intent.createChooser(chooser, "اختر ملف"), MainActivity.FILE_CHOOSER_RC);
        return true;
    }

    @Override
    public boolean onJsAlert(WebView v, String url, String msg, JsResult result) {
        this.currentResult = result;
        new AlertDialog.Builder(activity)
            .setMessage(msg)
            .setPositiveButton("موافق", this)
            .create().show();
        return true;
    }

    @Override
    public void onClick(DialogInterface dialog, int which) {
        if (currentResult != null) {
            currentResult.confirm();
            currentResult = null;
        }
    }
}
