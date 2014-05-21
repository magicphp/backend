<!DOCTYPE html>
<html>
<head>
<title>{$app.title} &middot; Login</title>
<meta charset="UTF-8" />
<meta name="publisher" content="MagicPHP" />
<link rel="stylesheet" href="{$cache.css}" type="text/css" />
<link rel="icon" type="image/vnd.microsoft.icon" href="{$route.root}favicon.ico" />
<link rel="apple-touch-icon" href="{$route.root}favicon.ico" />
<link rel="shortcut icon" href="{$route.root}favicon.ico" />
</head>
<body>
    <div class="baLoginBox">
        <form method="post" action="{$route.root}login">
            <input name="login_user" type="text" placeholder="Usuário / E-mail" />
            <input name="login_password" type="password" placeholder="Senha" />
            <input type="submit" value="Entrar" /> 
        </form>
    </div>
</body>
</html>