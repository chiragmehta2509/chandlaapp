<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Welcome to Chandla Book</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Georgia,'Times New Roman',serif;-webkit-font-smoothing:antialiased;">
    <span style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#f1f5f9;max-height:0;max-width:0;opacity:0;overflow:hidden;">
        Thank you for joining Chandla Book — manage events, collections, and reports in one place.
    </span>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f1f5f9;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;margin:0 auto;">
                    <!-- Top bar -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 45%,#4c1d95 100%);background-color:#312e81;border-radius:12px 12px 0 0;padding:28px 32px;text-align:center;">
                            @php
                                $logoFile = null;
                                foreach (['images/logo.jpeg', 'images/logo.png', 'images/chandla-logo.png', 'images/chandla-logo.jpg'] as $img) {
                                    if (file_exists(public_path($img))) {
                                        $logoFile = public_path($img);
                                        break;
                                    }
                                }
                            @endphp
                            @if(isset($message) && $logoFile)
                                <img src="{{ $message->embed($logoFile) }}" width="160" alt="Chandla Book" style="display:block;margin:0 auto 12px auto;max-width:160px;height:auto;border:0;outline:none;text-decoration:none;">
                            @else
                                <img src="{{ url('images/logo.jpeg') }}" width="160" alt="Chandla Book" style="display:block;margin:0 auto 12px auto;max-width:160px;height:auto;border:0;outline:none;text-decoration:none;">
                            @endif
                            <p style="margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:22px;font-weight:700;color:#ffffff;letter-spacing:-0.02em;">Chandla Book</p>
                            <p style="margin:8px 0 0 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:13px;color:rgba(255,255,255,0.85);font-weight:500;">Smart event &amp; collection management</p>
                        </td>
                    </tr>
                    <!-- Body card -->
                    <tr>
                        <td style="background-color:#ffffff;padding:36px 32px 28px 32px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                            <p style="margin:0 0 8px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:15px;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;">Welcome</p>
                            <h1 style="margin:0 0 20px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:26px;font-weight:700;color:#0f172a;line-height:1.25;letter-spacing:-0.02em;">We’re glad you’re here, {{ $user->name }}</h1>
                            <p style="margin:0 0 18px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:1.65;color:#334155;">
                                Thank you for registering with <strong style="color:#1e1b4b;">Chandla Book</strong>. Your account is active and ready to use. We’re honoured to help you stay organised for weddings, community events, and every occasion that matters.
                            </p>
                            <p style="margin:0 0 24px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:1.65;color:#334155;">
                                From the dashboard you can create events, track <strong>chandla</strong> and cash, share payment QR codes, and download reports when you need them.
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin:0 0 28px 0;">
                                <tr>
                                    <td style="padding:20px 22px;">
                                        <p style="margin:0 0 12px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;">You can get started with</p>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding:6px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:15px;color:#334155;vertical-align:top;width:24px;">&#10003;</td>
                                                <td style="padding:6px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:15px;color:#334155;line-height:1.5;">Creating your first <strong>event</strong> and setting date &amp; venue</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:15px;color:#334155;vertical-align:top;width:24px;">&#10003;</td>
                                                <td style="padding:6px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:15px;color:#334155;line-height:1.5;">Recording <strong>collections &amp; UPI / GPay</strong> in one ledger</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:15px;color:#334155;vertical-align:top;width:24px;">&#10003;</td>
                                                <td style="padding:6px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:15px;color:#334155;line-height:1.5;">Exporting a <strong>PDF report</strong> when the event is done</td>
                                            </tr>
                                        </table>
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
                            <p style="margin:14px 0 0 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;font-size:12px;color:#94a3b8;line-height:1.5;">
                                This message was sent because you created an account with us. If you did not expect this email, you may ignore it.
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
