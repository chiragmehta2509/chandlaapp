<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Password Changed - Chandla Book</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Georgia,'Times New Roman',serif;-webkit-font-smoothing:antialiased;">
    <span style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#f1f5f9;max-height:0;max-width:0;opacity:0;overflow:hidden;">
        Your Chandla Book password was recently changed.
    </span>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f1f5f9;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;margin:0 auto;">
                    <!-- Top bar -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 45%,#4c1d95 100%);background-color:#312e81;border-radius:12px 12px 0 0;padding:28px 32px;text-align:center;">
                            <img src="{{ asset('images/chandla-logo.png') }}" width="160" alt="Chandla Book" style="display:block;margin:0 auto 12px auto;max-width:160px;height:auto;border:0;outline:none;text-decoration:none;">
                            <p style="margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:22px;font-weight:700;color:#ffffff;letter-spacing:-0.02em;">Chandla Book</p>
                            <p style="margin:8px 0 0 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:13px;color:rgba(255,255,255,0.85);font-weight:500;">Security Alert</p>
                        </td>
                    </tr>
                    <!-- Body card -->
                    <tr>
                        <td style="background-color:#ffffff;padding:36px 32px 28px 32px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                            <p style="margin:0 0 8px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:15px;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;">Password Changed</p>
                            <h1 style="margin:0 0 20px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:26px;font-weight:700;color:#0f172a;line-height:1.25;letter-spacing:-0.02em;">Hello, {{ $user->name }}</h1>
                            <p style="margin:0 0 18px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:1.65;color:#334155;">
                                This is a quick note to let you know that the password for your <strong style="color:#1e1b4b;">Chandla Book</strong> account was successfully changed.
                            </p>
                            <p style="margin:0 0 24px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:1.65;color:#334155;">
                                If you made this change, you don't need to do anything further. 
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#fff5f5;border-radius:10px;border:1px solid #fecaca;margin:0 0 28px 0;">
                                <tr>
                                    <td style="padding:20px 22px;">
                                        <p style="margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:14px;color:#991b1b;line-height:1.5;">
                                            <strong>Didn't change your password?</strong><br>
                                            If you didn't request this change, please recover your account by resetting your password immediately, or contact our support team.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:0 auto;">
                                <tr>
                                    <td style="border-radius:8px;background-color:#4f46e5;">
                                        <a href="{{ url('/client/login') }}" target="_blank" style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;display:inline-block;padding:14px 32px;border-radius:8px;">Sign in</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Bottom -->
                    <tr>
                        <td style="background-color:#f8fafc;padding:22px 32px;border:1px solid #e2e8f0;border-top:0;border-radius:0 0 12px 12px;text-align:center;">
                            <p style="margin:0 0 6px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:13px;color:#64748b;">
                                With warm regards,<br>
                                <strong style="color:#0f172a;">Team Chandla Book</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 16px 8px 16px;text-align:center;">
                            <p style="margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:11px;color:#94a3b8;">&copy; {{ date('Y') }} Chandla Book. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
