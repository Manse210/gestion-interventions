<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1e293b; }
  .header { background: #0F172A; color: white; padding: 15px 20px; margin: -20px -20px 20px; }
  .header h1 { margin: 0; font-size: 18px; }
  .header p { margin: 5px 0 0; font-size: 11px; opacity: .8; }
  table.info { width: 100%; border-collapse: collapse; margin: 15px 0; }
  table.info td { padding: 6px 10px; border: 1px solid #e2e8f0; font-size: 11px; }
  table.info td:first-child { font-weight: bold; width: 30%; background: #f8fafc; }
  h2 { font-size: 14px; margin: 20px 0 8px; border-bottom: 2px solid #FF9800; padding-bottom: 4px; }
  .section { margin: 10px 0; line-height: 1.6; }
  .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>
<div class="header">
  <h1>RAPPORT D'INTERVENTION</h1>
  <p>ATECH Cybersécurité — {{ $ticket->ref }}</p>
</div>

<table class="info">
  <tr><td>Référence</td><td>{{ $ticket->ref }}</td></tr>
  <tr><td>Titre</td><td>{{ $ticket->titre }}</td></tr>
  <tr><td>Client</td><td>{{ $ticket->client?->name ?? $ticket->client_name ?? '—' }}</td></tr>
  <tr><td>Catégorie</td><td>{{ $ticket->categorie }}</td></tr>
  <tr><td>Priorité</td><td>{{ $ticket->priorite }}</td></tr>
  <tr><td>Date du rapport</td><td>{{ $report->created_at }}</td></tr>
  <tr><td>Signé par</td><td>{{ $report->signed_by }}</td></tr>
</table>

<h2>Travaux effectués</h2>
<div class="section">{{ nl2br($report->travaux) }}</div>

<h2>Détails opérationnels</h2>
<table class="info">
  <tr><td>Durée</td><td>{{ $report->duree_heures }}h {{ $report->duree_minutes }}m</td></tr>
  <tr><td>Matériel utilisé</td><td>{{ $report->materiel ?: '—' }}</td></tr>
</table>

@if($report->observations)
<h2>Observations</h2>
<div class="section">{{ nl2br($report->observations) }}</div>
@endif

@if($report->recommandations)
<h2>Recommandations</h2>
<div class="section">{{ nl2br($report->recommandations) }}</div>
@endif

<div class="footer">
  ATECH Cybersécurité — Confidentiel<br>
  Page {PAGE_NUM} sur {PAGE_COUNT}
</div>
</body>
</html>
