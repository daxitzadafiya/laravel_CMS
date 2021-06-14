<html>
<head>
</head>
<body>

    <p>
        <span>送信日時：{{ date('Y/m/d h:i') }}</span><br>
        <span>ユーザー名：{{ $user->last_name }}{{ $user->first_name }}</span><br>
    </p>
    <p>
        <span>Notification Title：{{ $content->title }}</span><br>
        <span>Notification Description：{{ $content->description }}</span><br>
        <span>Type：{{ $content->type_id }}</span><br>
    </p>

    <p>
        Click here to check the notification: {{ env('FRONTEND_SITE', 'https://cp-front.motocle8.com/') . 'notifications/' . $content->id }}
    </p>

    ------<br>
    ※このメールはCPWebアプリのコンタクトフォームから送信されました。
</body>
</html>
