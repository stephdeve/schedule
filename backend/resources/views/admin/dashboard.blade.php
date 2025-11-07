<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; background:#f3f4f6; color:#111827; }
        .container { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }
        .grid { display:grid; gap:1rem; }
        .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .card { background:#fff; border-radius:12px; box-shadow: 0 8px 20px rgba(0,0,0,.05); overflow:hidden; }
        .card .hd { padding:1rem 1.25rem; border-bottom:1px solid #e5e7eb; font-weight:600; }
        .stat { padding:1.25rem; }
        .stat .lbl { color:#6b7280; font-size:.9rem; }
        .stat .val { font-size:1.6rem; font-weight:600; margin-top:.25rem; }
        table { width:100%; border-collapse: collapse; font-size:.95rem; }
        th, td { text-align:left; padding:.6rem 1rem; }
        thead th { color:#6b7280; font-weight:500; }
        tbody tr { border-top:1px solid #e5e7eb; }
        h1 { font-size:1.5rem; font-weight:600; margin-bottom:1rem; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
        .btn { background:#111827; color:#fff; border:0; padding:.55rem .9rem; border-radius:.5rem; cursor:pointer; text-decoration:none; }
        .muted { color:#6b7280; }
        .list { list-style:none; padding:0; margin:0; }
        .list li { padding:.35rem 0; border-top:1px solid #f3f4f6; }
    </style>
</head>
<body>
<div class="container">
    <div class="topbar">
        <h1>Dashboard Admin</h1>
        <form method="POST" action="{{ route('logout') }}">@csrf <button class="btn" type="submit">Se déconnecter</button></form>
    </div>

    <div class="grid grid-4" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
        <div class="card stat"><div class="lbl">Professeurs</div><div class="val">{{ $stats['professeurs'] ?? 0 }}</div></div>
        <div class="card stat"><div class="lbl">Étudiants</div><div class="val">{{ $stats['etudiants'] ?? 0 }}</div></div>
        <div class="card stat"><div class="lbl">Cours</div><div class="val">{{ $stats['cours'] ?? 0 }}</div></div>
        <div class="card stat"><div class="lbl">Notifications</div><div class="val">{{ $stats['notifications'] ?? 0 }}</div></div>
    </div>

    <div class="grid grid-2" style="grid-template-columns: repeat(2, minmax(0, 1fr)); margin-top:1rem;">
        <div class="card">
            <div class="hd">Derniers cours</div>
            <div style="overflow-x:auto">
                <table>
                    <thead><tr><th>Cours</th><th>Professeur</th><th>Date</th><th>Heure</th></tr></thead>
                    <tbody>
                    @forelse($cours as $c)
                        <tr>
                            <td>{{ $c->nom_cours }}</td>
                            <td>{{ $c->professeur->nom_complet ?? '-' }}</td>
                            <td>{{ $c->date }}</td>
                            <td>{{ $c->heure_debut }} - {{ $c->heure_fin }}</td>
                        </tr>
                    @empty
                        <tr><td class="muted" colspan="4">Aucun cours</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="hd">Dernières notifications</div>
            <div style="padding:1rem 1.25rem;">
                <ul class="list">
                @forelse($notifications as $n)
                    <li><strong>#{{ $n->id }}</strong> — {{ $n->message }} <span class="muted">({{ $n->type }})</span></li>
                @empty
                    <li class="muted">Aucune notification</li>
                @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="grid grid-2" style="grid-template-columns: repeat(2, minmax(0, 1fr)); margin-top:1rem;">
        <div class="card">
            <div class="hd">Professeurs (10 derniers)</div>
            <div style="padding:1rem 1.25rem;">
                <ul class="list">
                    @forelse($professeurs as $p)
                        <li>{{ $p->nom_complet }} <span class="muted">— {{ $p->email }}</span></li>
                    @empty
                        <li class="muted">Aucun professeur</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="card">
            <div class="hd">Étudiants (10 derniers)</div>
            <div style="padding:1rem 1.25rem;">
                <ul class="list">
                    @forelse($etudiants as $e)
                        <li>{{ $e->nom_complet }} <span class="muted">— {{ $e->email }}</span></li>
                    @empty
                        <li class="muted">Aucun étudiant</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
</body>
</html>
