<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family:Arial,sans-serif; background:#f4f4f4; margin:0; padding:0; }
  .wrap { max-width:520px; margin:40px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.1); }
  .header { background:#091c3d; color:#fff; padding:28px 40px; text-align:center; }
  .header h1 { margin:0; font-size:1.3rem; }
  .body { padding:32px 40px; color:#333; line-height:1.7; text-align:center; }
  .otp-box { background:#f8f9fa; border:2px dashed #f5951b; border-radius:12px; padding:24px; margin:24px 0; }
  .otp-code { font-size:2.5rem; font-weight:900; letter-spacing:10px; color:#091c3d; }
  .footer { text-align:center; padding:16px; color:#888; font-size:.82rem; background:#f8f9fa; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>🔐 One-Time Password</h1>
  </div>
  <div class="body">
    <p>Dear <strong>{{ $voterName }}</strong>,</p>
    <p>Use the code below to complete your login. It expires in <strong>10 minutes</strong>.</p>

    <div class="otp-box">
      <div class="otp-code">{{ $otp }}</div>
    </div>

    <p style="color:#888;font-size:.85rem;">If you did not attempt to log in, please ignore this email and consider changing your password immediately.</p>
  </div>
  <div class="footer">Mzumbe University e-Voting System<br>This is an automated message. Do not reply.</div>
</div>
</body>
</html>
