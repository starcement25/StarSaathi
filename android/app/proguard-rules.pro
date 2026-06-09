# Add project specific ProGuard rules here.
# By default, the flags in this file are appended to flags specified
# in /usr/local/Cellar/android-sdk/24.3.3/tools/proguard/proguard-android.txt
# You can edit the include path and order by changing the proguardFiles
# directive in build.gradle.
#
# For more details, see
#   http://developer.android.com/guide/developing/tools/proguard.html

# ─────────────────────────────────────────
# FIX 1: Keep WebView & JS Bridge classes
# Prevents R8/ProGuard from stripping WebView
# internals that break image loading in release
# ─────────────────────────────────────────
-keep class android.webkit.** { *; }
-keep class com.android.org.chromium.** { *; }
-keepclassmembers class * extends android.webkit.WebViewClient {
    public void *(android.webkit.WebView, java.lang.String, android.graphics.Bitmap);
    public boolean *(android.webkit.WebView, java.lang.String);
}
-keepclassmembers class * extends android.webkit.WebChromeClient {
    public void *(android.webkit.WebView, java.lang.String);
}


# ─────────────────────────────────────────
# FIX 2: Keep SSL/TLS classes
# R8 can strip these in release, breaking
# HTTPS image loading silently
# ─────────────────────────────────────────
-keep class javax.net.ssl.** { *; }
-keep class java.security.** { *; }
-keep class sun.security.** { *; }
-dontwarn javax.net.ssl.**
-dontwarn java.security.**
-dontwarn sun.security.**


# ─────────────────────────────────────────
# FIX 3: Keep OkHttp (used by React Native
# for all network calls including WebView)
# ─────────────────────────────────────────
-keep class okhttp3.** { *; }
-keep interface okhttp3.** { *; }
-dontwarn okhttp3.**
-dontwarn okio.**
-keep class okio.** { *; }


# ─────────────────────────────────────────
# FIX 4: Keep React Native WebView module
# ─────────────────────────────────────────
-keep class com.reactnativecommunity.webview.** { *; }
-dontwarn com.reactnativecommunity.webview.**


# ─────────────────────────────────────────
# FIX 5: Keep cookie manager classes
# (Required for session-based image auth)
# ─────────────────────────────────────────
-keep class android.webkit.CookieManager { *; }
-keep class android.webkit.CookieSyncManager { *; }


# ─────────────────────────────────────────
# FIX 6: Keep JavaScript interface bridges
# Prevents JS<->Native communication from
# breaking in release builds
# ─────────────────────────────────────────
-keepattributes JavascriptInterface
-keepclassmembers class * {
    @android.webkit.JavascriptInterface <methods>;
}


# ─────────────────────────────────────────
# FIX 7: Keep network-related Android classes
# ─────────────────────────────────────────
-keep class android.net.** { *; }
-keep class android.net.http.** { *; }
-dontwarn android.net.http.**


# ─────────────────────────────────────────
# FIX 8: Preserve annotations and signatures
# Required so SSL/network reflection works
# ─────────────────────────────────────────
-keepattributes Signature
-keepattributes *Annotation*
-keepattributes EnclosingMethod
-keepattributes InnerClasses


# ─────────────────────────────────────────
# FIX 9: Keep Conscrypt (modern TLS engine)
# Prevents TLS handshake failures in release
# ─────────────────────────────────────────
-keep class org.conscrypt.** { *; }
-dontwarn org.conscrypt.**
-keep class com.android.org.conscrypt.** { *; }
-dontwarn com.android.org.conscrypt.**


# ─────────────────────────────────────────
# FIX 10: Prevent obfuscation of URL/HTTP classes
# ─────────────────────────────────────────
-keep class java.net.URL { *; }
-keep class java.net.HttpURLConnection { *; }
-keep class javax.net.ssl.HttpsURLConnection { *; }


# ─────────────────────────────────────────
# General React Native rules (keep existing)
# ─────────────────────────────────────────
-keep,allowobfuscation @interface com.facebook.proguard.annotations.DoNotStrip
-keep,allowobfuscation @interface com.facebook.proguard.annotations.KeepGettersAndSetters
-keep @com.facebook.proguard.annotations.DoNotStrip class *
-keepclassmembers class * {
    @com.facebook.proguard.annotations.DoNotStrip *;
}
-keepclassmembers @com.facebook.proguard.annotations.KeepGettersAndSetters class * {
    void set*(***);
    *** get*();
}
-keep class * implements com.facebook.react.bridge.JavaScriptModule { *; }
-keep class * implements com.facebook.react.bridge.NativeModule { *; }
-keepclassmembers,includedescriptorclasses class * { native <methods>; }
-keepclassmembers class *  { @com.facebook.react.uimanager.annotations.ReactProp <methods>; }
-keepclassmembers class *  { @com.facebook.react.uimanager.annotations.ReactPropGroup <methods>; }
-dontwarn com.facebook.react.**
-keep class com.facebook.** { *; }