<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>You've been added to a Chandla Book account</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.06);max-width:600px;width:100%;">
                <tr>
                    <td style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 45%,#4c1d95 100%);padding:28px;text-align:center;">
                        @if(file_exists(public_path('images/chandla-logo.png')))
                            <img src="{{ $message->embed(public_path('images/chandla-logo.png')) }}" width="160" alt="Chandla Book" style="display:block;margin:0 auto 12px auto;max-width:160px;height:auto;border:0;">
                        @endif
                        <p style="margin:0;color:#e0e7ff;font-size:13px;letter-spacing:0.08em;text-transform:uppercase;font-weight:600;">Family viewer access</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px 32px 16px 32px;">
                        <h1 style="margin:0 0 12px 0;font-size:22px;color:#0f172a;">Welcome, {{ $member->name }}</h1>
                        <p style="margin:0 0 16px 0;line-height:1.6;color:#374151;font-size:15px;"><strong>{{ $parent->name }}</strong> has added you as a family viewer on their Chandla Book account. You can sign in to view their events, ledger, contacts, and downloads.</p>
                        <p style="margin:0 0 24px 0;line-height:1.6;color:#374151;font-size:15px;">Family viewers are read-only — you can browse and download, but cannot add or edit entries.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 32px 24px 32px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;">
                            <tr>
                                <td style="padding:18px 20px;">
                                    <p style="margin:0 0 8px 0;font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;font-weight:700;">Your login</p>
                                    <p style="margin:0 0 4px 0;font-size:14px;color:#0f172a;"><strong>Email:</strong> {{ $member->email }}</p>
                                    @if($member->phone)
                                        <p style="margin:0 0 4px 0;font-size:14px;color:#0f172a;"><strong>Mobile:</strong> {{ $member->phone }}</p>
                                    @endif
                                    <p style="margin:8px 0 0 0;font-size:14px;color:#0f172a;"><strong>Temporary password:</strong> <span style="font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;background:#fff;border:1px solid #cbd5e1;padding:3px 8px;border-radius:6px;">{{ $tempPassword }}</span></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 32px 28px 32px;text-align:center;">
                        <a href="{{ route('client.login') }}" style="display:inline-block;background:#b8860b;color:#fff;padding:13px 28px;border-radius:10px;text-decoration:none;font-weight:600;font-size:15px;">Sign in to Chandla Book</a>
                        <p style="margin:14px 0 0 0;font-size:13px;color:#64748b;">You'll be asked to set a new password on your first sign-in.</p>
                    </td>
                </tr>
                <tr>
                    <td style="background:#f8fafc;padding:18px 32px;border-top:1px solid #e2e8f0;text-align:center;">
                        <p style="margin:0;font-size:12px;color:#64748b;line-height:1.6;">If you weren't expecting this email, you can safely ignore it. Your account is only active when you sign in.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
