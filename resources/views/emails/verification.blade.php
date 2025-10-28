<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفعيل الحساب</title>
</head>
<body style="font-family: Arial, sans-serif; text-align: right;">

    <h1>أهلاً بك، {{ $user->name }}!</h1>
    <p>شكراً لتسجيلك. استخدم الكود التالي لتفعيل حسابك:</p>

    <div style="padding: 15px; background-color: #f4f4f4; border-radius: 5px; text-align: center; margin: 20px 0;">
        <h2 style="margin: 0; letter-spacing: 5px; color: #333;">
            {{ $otp }} </h2>
    </div>

    <p>هذا الكود صالح لمدة 10 دقائق.</p>

</body>
</html>
