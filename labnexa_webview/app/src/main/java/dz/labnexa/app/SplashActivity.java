package dz.labnexa.app;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.view.animation.AlphaAnimation;
import android.widget.ImageView;
import android.widget.TextView;

@SuppressLint("CustomSplashScreen")
public class SplashActivity extends Activity implements Runnable {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);

        ImageView logo = findViewById(R.id.splash_logo);
        TextView tagline = findViewById(R.id.splash_tagline);

        AlphaAnimation fadeIn = new AlphaAnimation(0.0f, 1.0f);
        fadeIn.setDuration(700);
        fadeIn.setFillAfter(true);
        if (logo != null) logo.startAnimation(fadeIn);
        if (tagline != null) tagline.startAnimation(fadeIn);

        new Handler().postDelayed(this, 1500);
    }

    @Override
    public void run() {
        Intent intent = new Intent(this, MainActivity.class);
        if (getIntent() != null && getIntent().getData() != null) {
            intent.setData(getIntent().getData());
        }
        startActivity(intent);
        finish();
        overridePendingTransition(android.R.anim.fade_in, android.R.anim.fade_out);
    }
}
