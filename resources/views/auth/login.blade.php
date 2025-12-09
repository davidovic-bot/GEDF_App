<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRS - Connexion</title>

   <!-- Nouvelle police -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>

    /* Reset propre */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Playfair+Display", serif !important;
    }

    body {
        display: flex;
        height: 100vh;
        overflow: hidden;
    }

    /* === Partie gauche === */
    .left-side {
        flex: 0.75;
        background: linear-gradient(to bottom, #000715ff, #000919ff);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
    }

    .left-side h1 {
        font-size: 50px;
        color: #ffcc00;
        margin-bottom: 10px;
    }

    .left-side p {
        font-size: 12px;
        letter-spacing: 2px;
    }

    /* Barre tricolore */
    .loading-bar {
        margin-top: 25px;
        width: 450px;
        height: 1px;
        display: flex;
        border-radius: 3px;
        overflow: hidden;
    }
    .loading-bar div { flex: 1; }
    .green { background: #009639; }
    .yellow { background: #ffd700; }
    .blue { background: #007bff; }

    /* Barre verticale */
    .separator {
        width: 4px;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .separator div { flex: 1; }
    .separator .green { background: #009639; }
    .separator .yellow { background: #ffd700; }
    .separator .blue { background: #007bff; }

    /* === Partie droite === */
    .right-side {
        flex: 0.55;
        background: linear-gradient(180deg, #ffffffff 0%, #fcfcfcff 100%);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-container {
        width: 340px;
        text-align: center;
    }

    .logo-dgi {
        width: 330px;
        margin-bottom: 25px;
    }

    h2.titre-login {
        font-size: 24px !important;
        font-weight: normal !important;
        margin-bottom: 25px;
        color: #070a14ff !important;
    }

    /* === CHAMPS INPUT EXACTEMENT COMME LA VERSION DE DROITE === */

    .input-container {
        width: 100%;
        margin-bottom: 25px;
    }

    .input-container label {
        font-size: 15px;
        color: #444;
        display: block;
        margin-bottom: 6px;
        text-align: left !important;
    }

    /* --- FORÇAGE ABSOLU DU STYLE DES INPUTS --- */

    .input-container input,
    .styled-input,
     input[type="email"],
     input[type="password"] {
       all: unset !important;
       display: block !important;
       width: 100% !important;

       background: transparent !important;
       border: none !important;
       border-bottom: 1px solid #ccc !important;

       padding: 10px 5px !important;
       font-size: 16px !important;
       color: #333 !important;

       box-shadow: none !important;
    }

    /* FOCUS */
    .input-container input:focus,
    .styled-input:focus {
       border-bottom: 1px solid #003399 !important;
       outline: none !important;
    }
    

    /* === BOUTON === */
    .btn-connexion {
        width: 100%;
        padding: 29px 0;
        margin-top: 15px;
        background: #010d26ff;
        color: white;
        border: none;
        border-radius: 50px;
        letter-spacing: 2px;
        font-size: 11px !important;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-connexion:hover { background: #000a1f; }

    /* Lien */
    .forgot-password {
        font-size: 14px;
        color: #666;
        text-decoration: none;
        display: block;
        text-align: right;
        margin-top: 10px;
    }

    /* === FORÇAGE TOTAL CONTRE TAILWIND === */

    input,
    .styled-input,
    .input-container input[type="email"],
    .input-container input[type="password"] {
       all: unset !important;
       display: block !important;
       width: 100% !important;

       background: transparent !important;
       border: none !important;
       border-bottom: 1px solid #ccc !important;

       padding: 12px 5px !important;
       font-size: 16px !important;
       color: #333 !important;

       text-align: left !important;

       box-shadow: none !important;
       outline: none !important;
    }

    input:focus,
    .styled-input:focus,
    .input-container input:focus {
       border-bottom: 1px solid #003399 !important;
       outline: none !important;
       box-shadow: none !important;
    }

    .error-message {
        color: #cc0000;
        font-size: 16px;
        margin: 10px 0;
        text-align: left;
    }


</style>
</head>

<body>
    <!-- Partie gauche -->
    <div class="left-side">
        <div style="text-align: center; margin-bottom: 10px;">
    <img src="{{ asset('images/logo_drs.png') }}" alt="Logo DRS" style="width: 500px; margin: 0 auto 5px; display: block;">
</div>
        <p class="slogan">GESTION DES DEPENSES FISCALES</p>
        <div class="loading-bar">
            <div class="green"></div>
            <div class="yellow"></div>
            <div class="blue"></div>
        </div>
    </div>

    <!-- Barre tricolore verticale -->
    <div class="separator">
        <div class="green"></div>
        <div class="yellow"></div>
        <div class="blue"></div>
    </div>

    <!-- Partie droite -->
    <div class="right-side">
        <div class="login-container">
            <img src="{{ asset('images/dgi_logo.jpg') }}" alt="Logo DGI" class="logo-dgi">
            <h2 class="titre-login">Connectez-vous sur votre espace</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

        <div class="input-container">
            <label for="email">Mail</label>
            <input type="email" id="email" name="email" class="styled-input" placeholder="Saisir votre mail" autocomplete="off" required>
       </div>

       <div class="input-container">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" class="styled-input" placeholder="Saisir votre mot de passe" autocomplete="off" required>
       </div>

       @if ($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <button type="submit" class="btn-connexion">
            CONNEXION
        </button>

            <a href="{{ route('password.request') }}" class="forgot-password">Mot de passe oublié ?</a>
        </form> 
        </div>
    </div>
</body>
</html>