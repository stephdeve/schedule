<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Publication du cours</title>
</head>
<body>
    <h2>Publication du cours: {{ $cours->nom_cours }}</h2>
    @if(!empty($destinataire))
        <p>Bonjour {{ $destinataire->nom_complet }},</p>
    @endif
    <p>
        Le cours <strong>{{ $cours->nom_cours }}</strong> est programmé le <strong>{{ $cours->date }}</strong>
        de <strong>{{ $cours->heure_debut }}</strong> à <strong>{{ $cours->heure_fin }}</strong> en salle <strong>{{ $cours->salle }}</strong>.
    </p>
    <p>Semestre: {{ $cours->semestre }}</p>
    <p>Professeur: {{ $cours->professeur->nom_complet ?? '-' }} ({{ $cours->professeur->email ?? '' }})</p>
    <hr>
    <small>Message automatique - Ne pas répondre</small>
</body>
</html>
