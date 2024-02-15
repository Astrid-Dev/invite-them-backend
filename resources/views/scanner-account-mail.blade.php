<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{'Scanneur ajouté à l\'événement '.$event->name}}</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
        }
    </style>
</head>

<body>
<div class="container">

    <div class="message">
        <p>Bonjour, {{$user->pseudo}}</p>
        <p>Vous avez été ajouté en tant que scanneur à l'événement {{$event->name}}.<br/>
            Vous pouvez désormais scanner les billets d'invitation des invités à cet événement.
        </p>
        <p>Retrouvez ci-dessous vos identifiants de connexion :</p>
        <p><strong>Pseudo :</strong> {{$userIsNewer ? $user->pseudo : 'Votre pseudo habituel'}}</p>
        <p><strong>Mot de passe :</strong> {{$userIsNewer ? $password : 'Votre mot de passe habituel'}}</p>
        <p><strong>Code :</strong> {{$event->code}}</p>
        <p>Vous pouvez vous connecter à votre compte en cliquant sur le lien suivant : <a href="{{env('FRONTEND_URL')}}/auth/login?e={{$event->code}}">Se connecter</a></p>
    </div>

</div>
</body>

</html>
