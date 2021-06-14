<html>
<head>
</head>
<body>
    <p>
        ※このメールは自動送信です<br>
        ※お客様の対応をお願いします
    </p>

    <p>
        <span>送信日時：{{ date('Y/m/d h:i') }}</span><br>
        <span>ユーザーID：{{ $user->id }}</span><br>
        <span>ユーザー名：{{ $user->last_name }}{{ $user->first_name }}</span><br>
        <span>店舗名：{{ $user->company->display_name }}</span><br>
    </p>

    <p>
        <span>問い合わせ種別：{{ $content->type }}</span><br>
        <span>内容：{!! nl2br(e($content->message)) !!}</span><br>
        <span>送信前の滞在ページ：{{ $content->referrer_url }}</span><br>
    </p>

    ------<br>
    ※このメールはCPWebアプリのコンタクトフォームから送信されました。
</body>
</html>
