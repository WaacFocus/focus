<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; }
    body  { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; margin: 0; padding: 20px; }

    .report-header { background: #0C3D38; color: #fff; padding: 14px 18px; border-radius: 5px; margin-bottom: 18px; }
    .report-header p  { margin: 0; font-size: 8px; color: rgba(255,255,255,.55); text-transform: uppercase; letter-spacing: .05em; }
    .report-header h1 { margin: 4px 0 0; font-size: 16px; color: #fff; }

    h1 { display: none; } /* suppress duplicate from markdown */
    h2 { font-size: 11px; font-weight: bold; color: #fff; background: #0C3D38; padding: 5px 9px; border-radius: 4px; margin: 16px 0 6px; page-break-after: avoid; }
    h2:first-of-type { margin-top: 0; }
    hr { border: none; border-top: 1px solid #dee2e6; margin: 4px 0 14px; }
    h3 { font-size: 8px; text-transform: uppercase; letter-spacing: .05em; color: #555; border-left: 3px solid #17B4A7; padding-left: 6px; margin: 10px 0 4px; }
    ul { padding-left: 14px; margin: 0 0 6px; }
    li { font-size: 9px; color: #333; margin-bottom: 3px; line-height: 1.5; }
    li strong { color: #0C3D38; }
    p  { font-size: 9px; color: #555; margin: 0 0 6px; }

    .generated { font-size: 8px; color: #aaa; margin-top: 18px; }
</style>
</head>
<body>

<div class="report-header">
    <p>Focus — Accounting Practice</p>
    <h1>Version Log</h1>
</div>

{!! $html !!}

<div class="generated">Focus — Generated {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
