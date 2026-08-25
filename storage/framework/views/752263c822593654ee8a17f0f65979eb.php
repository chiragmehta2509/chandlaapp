<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Verify Your Account - Chandla Book</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Georgia,'Times New Roman',serif;-webkit-font-smoothing:antialiased;">
    <span style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#f1f5f9;max-height:0;max-width:0;opacity:0;overflow:hidden;">
        Please verify your account to complete your registration with Chandla Book.
    </span>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f1f5f9;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;margin:0 auto;">
                    <!-- Top bar -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 45%,#4c1d95 100%);background-color:#312e81;border-radius:12px 12px 0 0;padding:28px 32px;text-align:center;">
                            <img src="https://chandlabook.in/images/chandla-logo.png" width="160" alt="Chandla Book" style="display:block;margin:0 auto 12px auto;max-width:160px;height:auto;border:0;outline:none;text-decoration:none;">
                            <p style="margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:22px;font-weight:700;color:#ffffff;letter-spacing:-0.02em;">Chandla Book</p>
                            <p style="margin:8px 0 0 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:13px;color:rgba(255,255,255,0.85);font-weight:500;">Verify your account</p>
                        </td>
                    </tr>
                    <!-- Body card -->
                    <tr>
                        <td style="background-color:#ffffff;padding:36px 32px 28px 32px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                            <p style="margin:0 0 8px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:15px;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;">Finalize account set-up</p>
                            <h1 style="margin:0 0 20px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:26px;font-weight:700;color:#0f172a;line-height:1.25;letter-spacing:-0.02em;">Hi <?php echo e($name); ?>,</h1>
                            <p style="margin:0 0 18px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:1.65;color:#334155;">
                                Your new account registration has been initiated successfully.
                            </p>
                            <p style="margin:0 0 18px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:1.65;color:#334155;">
                                Please verify your email address to complete your profile.
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:24px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?php echo e($verification_url); ?>" style="background-color:#ffffff;border:1px solid #10b981;border-radius:8px;padding:12px 24px;display:inline-block;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;font-size:16px;font-weight:600;color:#10b981;text-decoration:none;">
                                            <span style="margin-right:8px;">&#8599;</span> Verify account
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 24px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:14px;line-height:1.65;color:#64748b;text-align:center;">
                                This link will expire in 15 minutes.
                            </p>
                        </td>
                    </tr>
                    <!-- Bottom -->
                    <tr>
                        <td style="background-color:#f8fafc;padding:22px 32px;border:1px solid #e2e8f0;border-top:0;border-radius:0 0 12px 12px;text-align:center;">
                            <p style="margin:0 0 6px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:13px;color:#64748b;">
                                With warm regards,<br>
                                <strong style="color:#0f172a;">Team Chandla Book</strong>
                            </p>
                            <p style="margin:14px 0 0 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:12px;color:#94a3b8;line-height:1.5;">
                                If you did not request this, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 16px 8px 16px;text-align:center;">
                            <p style="margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:11px;color:#94a3b8;">&copy; <?php echo e(date('Y')); ?> Chandla Book. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH /home/chandlabook/public_html/resources/views/emails/verify_link.blade.php ENDPATH**/ ?>