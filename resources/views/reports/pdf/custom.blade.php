<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; margin: 0; padding: 20px; }
    h1   { font-size: 16px; margin: 0 0 2px; }
    .meta { font-size: 9px; color: #888; margin-bottom: 14px; }
    h2  { font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #666; margin: 0 0 6px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    thead th { background: #f0f1f3; padding: 5px 7px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: .03em; border-bottom: 2px solid #c8ccd0; }
    tbody td { padding: 5px 7px; border-bottom: 1px solid #e9ecef; font-size: 9px; }
    tbody tr:nth-child(even) td { background: #fafafa; }
    .generated { font-size: 8px; color: #aaa; margin-top: 12px; }
</style>
</head>
<body>

<h1>{{ $savedReport->name }}</h1>
<div class="meta">
    Source: {{ ucfirst($savedReport->config['source'] ?? '') }}
    &nbsp;·&nbsp; {{ $result['count'] }} row{{ $result['count'] !== 1 ? 's' : '' }}
    &nbsp;·&nbsp; Generated {{ now()->format('d F Y, H:i') }}
    &nbsp;·&nbsp; {{ ucfirst($orientation) }}
</div>

@if($result['count'] > 0)
<table>
    <thead>
        <tr>
            @foreach($result['headers'] as $h)
            <th>{{ $h }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($result['rows'] as $row)
        <tr>
            @foreach($row as $cell)
            <td>{{ $cell }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p style="color:#888;">No results found for this report.</p>
@endif

<div class="generated">Focus — {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
