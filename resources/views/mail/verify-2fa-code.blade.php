<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Code 2FA</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #333; text-align: center;">Vérification à deux facteurs</h2>
        
        <p style="color: #666; font-size: 16px;">Bonjour {{ $userName }},</p>
        
        <p style="color: #666; font-size: 14px;">
            Vous avez demandé une connexion à votre compte. Voici votre code de vérification à deux facteurs:
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <div style="background-color: #f0f0f0; padding: 20px; border-radius: 8px; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #007bff;">
                {{ $code }}
            </div>
        </div>
        
        <p style="color: #666; font-size: 14px;">
            <strong>IMPORTANT:</strong> Ce code expirera dans 15 minutes. Si vous n'avez pas demandé cette vérification, ignorez ce message.
        </p>
        
        <p style="color: #999; font-size: 12px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
            Cet email a été envoyé automatiquement. Veuillez ne pas répondre à cet email.
        </p>
    </div>
</body>
</html>
