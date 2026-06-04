<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family:Arial,sans-serif; background:#f4f4f4; margin:0; padding:0; }
  .wrap { max-width:600px; margin:40px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.1); }
  .header { background:#091c3d; color:#fff; padding:32px 40px; text-align:center; }
  .header h1 { margin:0; font-size:1.4rem; }
  .badge { display:inline-block; background:#198754; color:#fff; padding:6px 18px; border-radius:20px; font-weight:bold; margin-top:10px; }
  .body { padding:32px 40px; color:#333; line-height:1.7; }
  .cred-box { background:#f8f9fa; border-left:4px solid #091c3d; padding:16px 20px; border-radius:4px; margin:20px 0; font-size:.95rem; }
  .cred-box strong { color:#091c3d; }
  .otp-val { font-size:1.5rem; font-weight:bold; letter-spacing:4px; color:#f5951b; }
  .btn { display:inline-block; background:#091c3d; color:#fff; text-decoration:none; padding:12px 28px; border-radius:6px; font-weight:bold; margin:20px 0; }
  .footer { text-align:center; padding:20px; color:#888; font-size:.82rem; background:#f8f9fa; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>🗳 Mzumbe University e-Voting</h1>
    <span class="badge">Registration Approved ✓</span>
  </div>
  <div class="body">
    <p>Dear <strong>{{ $voterName }}</strong>,</p>
    <p>Your voter registration has been <strong>approved</strong> by the Election Commission. You can now log in and cast your vote.</p>

    <div class="cred-box">
      <div><strong>Faculty:</strong> {{ $faculty }}</div>
      <div><strong>Programme:</strong> {{ $program }}</div>
      <hr style="border:none;border-top:1px solid #dee2e6;margin:12px 0;">
      <div><strong>Login Email:</strong> {{ $email }}</div>
      <div><strong>Password:</strong> <span class="otp-val">{{ $plainPassword }}</span></div>
    </div>

    <p style="color:#c53030;font-size:.88rem;">⚠ Please change your password after your first login. Keep these credentials confidential.</p>

    <p>When logging in, you will also be asked to enter a <strong>One-Time Password (OTP)</strong> sent to this email for additional security.</p>

    <p style="text-align:center;">
      <a href="{{ url('/') }}" class="btn">Go to Voting Portal</a>
    </p>
  </div>
  <div class="footer">Mzumbe University — Student Union Elections<br>This is an automated message. Do not reply.</div>
</div>
</body>
</html>
