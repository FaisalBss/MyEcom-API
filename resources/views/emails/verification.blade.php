<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفعيل الحساب</title>
</head>
<body style="font-family: Arial, sans-serif; text-align: right;">

    <h1>أهلاً بك، {{ $user->name }}!</h1>
    <p>شكراً لتسجيلك. الرجاء الضغط على الرابط لتفعيل حسابك:</p>

    <a href="{{ $verificationLink }}" style="padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
        اضغط هنا للتفعيل
    </a>

    <p style="margin-top: 20px;">
        إذا لم يعمل الرابط، قم بنسخ ولصق العنوان التالي:
        <br>
        <small>{{ $verificationLink }}</small>
    </p>

</body>
</html>
