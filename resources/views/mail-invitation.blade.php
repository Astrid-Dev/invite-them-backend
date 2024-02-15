<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitation au Mariage de Simon et Prisca</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
        }
    </style>
</head>

<body>
    @isset($message)
        <img src="{{$message?->embed(public_path('first_page.jpg'))}}" alt="Couverture de l'invitation">
    @endisset

    <p>Le <strong>samedi 27 juillet 2024</strong>
        à <strong>13 heures</strong>, deux étoiles vont s’unir sous les cieux de
        la belle ville de <strong>Makénéné</strong> et de sa <strong>paroisse St-Joseph</strong>.
        Leur amour brille de mille feux et ils veulent le graver
        à jamais dans leur cœur et dans leur essence.</p>
    <p><strong>Simon</strong> et <strong>Prisca</strong> vous invitent à partager cette féerie de l’amour avec
        eux. Les réjouissances se poursuivront à leur domicile
        le même jour au quartier <strong>Carrière</strong> à partir de <strong>16h</strong>
        précises.</p>
    <p>Mais avant, ils vont communier avec les ancêtres par
        la cérémonie de la dot le <strong>vendredi 26 juillet 2024</strong> dès
        <strong>16h</strong>, au domicile des parents de Prisca au quartier
        <strong>Hôpital de Makénéné</strong>.</p>
    <p>Nous espérons que vous accepterez d’être les témoins
        de la concrétisation de cet amour, drapé d’une tenue
        chic et glamour.</p>
    <p>Pour la circonstance, veuillez laisser vos appareils
        photos dans le fond de vos poches et vos sacoches,
        puisqu’ils ont prévu un photographe pour vous
        permettre de profiter pleinement de la cérémonie.</p>
    <p>Pour que tout soit parfait dans les moindres détails, les
        amoureux ont besoin d’une réponse avant le 01er
        juillet.</p>
    <p>Vous pourrez confirmer votre présence à tout moment via le lien suivant :
        <a target="_blank" href="{{$guest->presence_confirmation_url}}">{{$guest->presence_confirmation_url}}</a>
    </p>
    <br/>
    <p>Ci-joint votre billet d'invitation !</p>
</body>

</html>
