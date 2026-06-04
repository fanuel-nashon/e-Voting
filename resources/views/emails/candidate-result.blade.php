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
  .result-box { background:#f8f9fa; border-left:4px solid #091c3d; padding:16px 20px; border-radius:4px; margin:20px 0; }
  .result-box.won { border-color:#198754; background:#f0fff4; }
  .result-box.lost { border-color:#6c757d; }
  .btn { display:inline-block; background:#091c3d; color:#fff; text-decoration:none; padding:12px 28px; border-radius:6px; font-weight:bold; margin:20px 0; }
  .footer { text-align:center; padding:20px; color:#888; font-size:.85rem; background:#f8f9fa; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>🗳 e-Voting System</h1>
    <span class="badge">{{ $acceptance->won ? 'Election Results — You Won!' : 'Election Results' }}</span>
  </div>

  <div class="body">
    <p>Dear <strong>{{ $acceptance->candidate->name }}</strong>,</p>

    <p>The voting period for the <strong>Student Union Election</strong> has officially ended. We are pleased to share the results for the position you contested.</p>

    <div class="result-box {{ $acceptance->won ? 'won' : 'lost' }}">
      <strong>Position:</strong> {{ $acceptance->position->name }}<br>
      <strong>Votes Received:</strong> {{ $acceptance->votes_received }}<br>
      <strong>Outcome:</strong>
      @if($acceptance->won)
        <span style="color:#198754;font-weight:bold;">🏆 Winner</span>
      @else
        <span style="color:#6c757d;">Runner-up / Not elected</span>
      @endif
    </div>

    @if($acceptance->won)
    <p>Congratulations! You have been elected. Please click the button below to formally <strong>accept your position</strong>. Your response is required within the stated deadline.</p>

    <p style="text-align:center;">
      <a href="{{ url('/acceptance/' . $acceptance->token) }}" class="btn">Accept / Acknowledge Result</a>
    </p>
    @else
    <p>Thank you for participating in this election. Although you were not elected this time, your willingness to serve the student community is deeply appreciated.</p>

    <p style="text-align:center;">
      <a href="{{ url('/acceptance/' . $acceptance->token) }}" class="btn">Acknowledge Result</a>
    </p>
    @endif

    <p style="color:#888;font-size:.9rem;">This link expires when the acceptance deadline passes. If you have any concerns, please contact the Election Commission.</p>
  </div>

  <div class="footer">
    e-Voting System &mdash; Student Union Elections<br>
    This is an automated message. Please do not reply.
  </div>
</div>
</body>
</html>
