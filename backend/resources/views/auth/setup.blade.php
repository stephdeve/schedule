<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initialisation Administrateur</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; background:#f9fafb; }
        .card { max-width: 560px; margin: 3rem auto; background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,.05); padding: 2rem; }
        .title { font-weight: 600; font-size: 1.25rem; margin-bottom: 1rem; }
        label { display:block; color:#6b7280; font-size:.9rem; margin:.5rem 0 .25rem; }
        input, select { width:100%; border:1px solid #e5e7eb; padding:.75rem .9rem; border-radius: .5rem; }
        .btn { background:#111827; color:#fff; border:0; padding:.75rem 1rem; border-radius:.5rem; width:100%; cursor:pointer; margin-top:1rem; }
        .err { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; padding:.75rem; border-radius:.5rem; margin-bottom:1rem; }
        .muted { color:#6b7280; font-size:.9rem; }
    </style>
</head>
<body>
<div class="card">
    <div class="title">Initialisation de l'administrateur</div>
    <p class="muted" style="margin-bottom:1rem;">Aucun utilisateur n'a été trouvé. Créez le compte administrateur initial pour démarrer.</p>
    @if($errors->any())
        <div class="err">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('setup.submit') }}">
        @csrf
        <label for="nom_complet">Nom complet</label>
        <input id="nom_complet" name="nom_complet" type="text" value="{{ old('nom_complet') }}" required>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>

        <label for="departement">Département (optionnel)</label>
        <input id="departement" name="departement" type="text" value="{{ old('departement') }}">

        <label for="password">Mot de passe</label>
        <input id="password" name="password" type="password" required>

        <label for="password_confirmation">Confirmer le mot de passe</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required>

        <button class="btn" type="submit">Créer l'administrateur</button>
    </form>
</div>
</body>
</html>
