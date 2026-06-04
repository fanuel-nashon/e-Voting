<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
  .wrap { max-width:600px; margin:40px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.1); }
  .header { background:#091c3d; color:#fff; padding:32px 40px; text-align:center; }
  .header h1 { margin:0; font-size:1.5rem; }
  .badge { display:inline-block; background:#f5951b; color:#fff; padding:6px 18px; border-radius:20px; font-weight:bold; margin-top:12px; }
  .body { padding:32px 40px; color:#333; line-height:1.7; }
  table { width:100%; border-collapse:collapse; margin:20px 0; }
  th { background:#091c3d; color:#fff; padding:10px 14px; text-align:left; font-size:.9rem; }
  td { padding:10px 14px; border-bottom:1px solid #e9ecef; font-size:.9rem; }
  tr:last-child td { border-bottom:none; }
  .winner-row td { background:#f0fff4; font-weight:600; }
  .footer { text-align:center; padding:20px; color:#888; font-size:.85rem; background:#f8f9fa; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>🗳 e-Voting System</h1>
    <span class="badge">Official Election Results</span>
  </div>

  <div class="body">
    <p>Dear Student,</p>
    <p>The <strong>{{ $electionTitle }}</strong> has concluded. Below are the official results as verified by the Election Commission.</p>

    @foreach($results as $positionName => $candidates)
    <h3 style="color:#091c3d;margin-top:28px;border-bottom:2px solid #f5951b;padding-bottom:6px;">
      {{ $positionName }}
    </h3>
    <table>
      <thead>
        <tr>
          <th>Candidate</th>
          <th>Votes</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($candidates as $c)
        <tr class="{{ $c['won'] ? 'winner-row' : '' }}">
          <td>{{ $c['name'] }}</td>
          <td>{{ $c['votes'] }}</td>
          <td>{{ $c['won'] ? '🏆 Elected' : '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endforeach

    <p style="color:#888;font-size:.9rem;margin-top:24px;">
      These results are final and have been verified by the Election Commission. Thank you for participating in the democratic process.
    </p>
  </div>

  <div class="footer">
    e-Voting System &mdash; Student Union Elections<br>
    This is an automated message. Please do not reply.
  </div>
</div>
</body>
</html>
