<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payment recorded — <?php echo e($event->title); ?></title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;-webkit-font-smoothing:antialiased;">
    <span style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#f1f5f9;max-height:0;max-width:0;opacity:0;overflow:hidden;">
        <?php echo e($chandla->giver_name); ?> · INR <?php echo e(number_format($chandla->amount, 2)); ?> · <?php echo e($event->title); ?> — review in your dashboard.
    </span>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f1f5f9;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;margin:0 auto;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 45%,#4c1d95 100%);background-color:#312e81;border-radius:12px 12px 0 0;padding:24px 28px;text-align:center;">
                            <img src="https://chandlabook.in/images/chandla-logo.png" width="140" height="auto" alt="Chandla Book" style="display:block;margin:0 auto 10px auto;max-width:140px;height:auto;border:0;">
                            <p style="margin:0;font-size:20px;font-weight:700;color:#ffffff;letter-spacing:-0.02em;">Chandla Book</p>
                            <p style="margin:6px 0 0 0;font-size:12px;color:rgba(255,255,255,0.88);font-weight:500;">Payment notification</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#ffffff;padding:32px 28px 24px 28px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                            <p style="margin:0 0 8px 0;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">New entry</p>
                            <h1 style="margin:0 0 12px 0;font-size:24px;font-weight:700;color:#0f172a;line-height:1.3;letter-spacing:-0.02em;">A guest payment was recorded</h1>
                            <p style="margin:0 0 28px 0;font-size:16px;line-height:1.6;color:#475569;">
                                Someone submitted a payment for <strong style="color:#1e1b4b;"><?php echo e($event->title); ?></strong>. Review the details below and confirm the entry in your ledger if needed.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;overflow:hidden;">
                                <tr>
                                    <td style="padding:18px 20px;border-bottom:1px solid #e2e8f0;">
                                        <p style="margin:0;font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;font-weight:700;">Amount</p>
                                        <p style="margin:6px 0 0 0;font-size:28px;font-weight:700;color:#059669;letter-spacing:-0.02em;">₹<?php echo e(number_format($chandla->amount, 2)); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="padding:14px 20px;border-bottom:1px solid #e2e8f0;width:38%;vertical-align:top;">
                                                    <span style="font-size:12px;color:#64748b;font-weight:600;">Event</span>
                                                </td>
                                                <td style="padding:14px 20px;border-bottom:1px solid #e2e8f0;vertical-align:top;">
                                                    <span style="font-size:15px;color:#0f172a;font-weight:600;"><?php echo e($event->title); ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:14px 20px;border-bottom:1px solid #e2e8f0;vertical-align:top;">
                                                    <span style="font-size:12px;color:#64748b;font-weight:600;">Guest name</span>
                                                </td>
                                                <td style="padding:14px 20px;border-bottom:1px solid #e2e8f0;vertical-align:top;">
                                                    <span style="font-size:15px;color:#1e293b;"><?php echo e($chandla->giver_name); ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:14px 20px;border-bottom:1px solid #e2e8f0;vertical-align:top;">
                                                    <span style="font-size:12px;color:#64748b;font-weight:600;">Date</span>
                                                </td>
                                                <td style="padding:14px 20px;border-bottom:1px solid #e2e8f0;vertical-align:top;">
                                                    <span style="font-size:15px;color:#1e293b;"><?php echo e($chandla->received_date->format('F j, Y')); ?></span>
                                                </td>
                                            </tr>
                                            <?php if(!empty($chandla->gpay_transaction_id)): ?>
                                            <tr>
                                                <td style="padding:14px 20px;vertical-align:top;">
                                                    <span style="font-size:12px;color:#64748b;font-weight:600;">Reference</span>
                                                </td>
                                                <td style="padding:14px 20px;vertical-align:top;">
                                                    <span style="font-size:14px;color:#0f172a;font-family:ui-monospace,Consolas,monospace;word-break:break-all;"><?php echo e($chandla->gpay_transaction_id); ?></span>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
                                <tr>
                                    <td style="background-color:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:14px 18px;">
                                        <p style="margin:0;font-size:14px;color:#92400e;line-height:1.5;">
                                            <strong style="color:#b45309;">Status:</strong> Logged in your chandla list — please verify the payment in your app when you can.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <?php $chandlaUrl = route('client.chandlas.show', $chandla); ?>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:28px auto 0 auto;">
                                <tr>
                                    <td style="border-radius:8px;background-color:#4f46e5;">
                                        <a href="<?php echo e($chandlaUrl); ?>" target="_blank" style="font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;display:inline-block;padding:14px 28px;border-radius:8px;">View entry in dashboard</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:18px 0 0 0;font-size:13px;color:#64748b;line-height:1.5;">
                                Or open: <a href="<?php echo e($chandlaUrl); ?>" style="color:#4f46e5;word-break:break-all;"><?php echo e($chandlaUrl); ?></a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f8fafc;padding:20px 28px;border:1px solid #e2e8f0;border-top:0;border-radius:0 0 12px 12px;text-align:center;">
                            <p style="margin:0;font-size:13px;color:#64748b;">
                                You’re receiving this because you are the organiser for this event in Chandla Book.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 16px 8px 16px;text-align:center;">
                            <p style="margin:0;font-size:11px;color:#94a3b8;">&copy; <?php echo e(date('Y')); ?> Chandla Book</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH /home/chandlabook/public_html/resources/views/emails/payment-submitted.blade.php ENDPATH**/ ?>