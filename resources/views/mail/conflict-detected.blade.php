<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conflict Alert</title>
    <style>
        body { font-family: 'Helvetica Neue', sans-serif; background: #f9fafb; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
        .header { background: #111827; padding: 32px; text-align: center; }
        .header h1 { color: #d4af37; font-size: 22px; margin: 0; letter-spacing: 0.05em; }
        .header p { color: #9ca3af; font-size: 13px; margin: 8px 0 0; }
        .body { padding: 32px; }
        .alert-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; }
        .alert-box p { color: #991b1b; font-size: 14px; margin: 0; font-weight: 600; }
        .match-info { background: #f9fafb; border-radius: 8px; padding: 20px; border: 1px solid #e5e7eb; }
        .match-info table { width: 100%; border-collapse: collapse; }
        .match-info td { padding: 8px 0; font-size: 14px; color: #374151; }
        .match-info td:first-child { font-weight: 700; color: #111827; width: 40%; }
        .vs { text-align: center; padding: 12px 0; font-size: 18px; font-weight: 900; color: #d4af37; letter-spacing: 0.1em; }
        .cta { margin-top: 28px; text-align: center; }
        .cta a { background: #111827; color: #d4af37; text-decoration: none; font-weight: 700; font-size: 13px; padding: 12px 24px; border-radius: 8px; letter-spacing: 0.075em; text-transform: uppercase; display: inline-block; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 20px 32px; text-align: center; }
        .footer p { color: #9ca3af; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>⚠️ TM7 Conflict Alert</h1>
            <p>{{ ucfirst($tournamentType) }} Tournament — Admin Action Required</p>
        </div>

        <div class="body">
            <div class="alert-box">
                <p>Both players in a match have claimed victory. Please review and verify the correct winner.</p>
            </div>

            <div class="match-info">
                <table>
                    <tr>
                        <td>Tournament</td>
                        <td>{{ ucfirst($tournamentType) }} Bracket</td>
                    </tr>
                    <tr>
                        <td>Round</td>
                        <td>{{ $match->round }}</td>
                    </tr>
                    <tr>
                        <td>Match #</td>
                        <td>{{ $match->id }}</td>
                    </tr>
                </table>

                <div class="vs">
                    <strong>{{ $p1Name }}</strong>
                    &nbsp;vs&nbsp;
                    <strong>{{ $p2Name }}</strong>
                </div>

                <table>
                    <tr>
                        <td>Status</td>
                        <td><strong style="color:#dc2626;">CONFLICT — Both Claimed Win</strong></td>
                    </tr>
                </table>
            </div>

            <div class="cta">
                <a href="{{ config('app.url') }}/admin/stats/{{ $tournamentType === 'team' ? 'team-bracket' : 'individual-bracket' }}">
                    Resolve in Admin Dashboard →
                </a>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated notification from the TM7 Backgammon Club platform.<br>Do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
