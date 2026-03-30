<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 5px; }
        .header { background: #f8f9fa; padding: 10px 20px; border-bottom: 2px solid #D4AF37; margin-bottom: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #666; }
        .message { background: #fdfcf8; padding: 15px; border-left: 4px solid #D4AF37; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0; color: #1F2833;">New Website Inquiry</h2>
        </div>
        
        <div class="field">
            <span class="label">From:</span> {{ $data['name'] }} ({{ $data['email'] }})
        </div>
        
        <div class="field">
            <span class="label">Subject:</span> {{ $data['subject'] }}
        </div>
        
        <div class="field">
            <span class="label">Message:</span>
            <div class="message">{{ $data['message'] }}</div>
        </div>

        <p style="font-size: 11px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px;">
            This email was sent via the contact form on tm7backgammon.com
        </p>
    </div>
</body>
</html>
