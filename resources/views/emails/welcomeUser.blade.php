<html>
<head>
</head>
<body>
    <p>Hello {{ $user['last_name'] . ' ' . $user['first_name'] }}, </p>
    <p>Welcome to CP</p>

    @if (! empty($user['password_email']))
        <p>Your password - {{ $user['password'] }}</p>
    @endif
</body>
</html>
