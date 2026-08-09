<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Your Chandla register PDF</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f1ea;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;-webkit-font-smoothing:antialiased;">
    <span style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#f4f1ea;max-height:0;max-width:0;opacity:0;overflow:hidden;">
        Chandla register PDF for <?php echo e($event->title); ?> — cash, cover, and gift sections attached.
    </span>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f1ea;">
        <tr>
            <td align="center" style="padding:28px 14px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;margin:0 auto;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color:#1a3646;border-radius:14px 14px 0 0;padding:26px 28px;text-align:center;border-bottom:4px solid #b8860b;">
                            <?php
                                $logoFile = null;
                                foreach (['images/logo.jpeg', 'images/logo.png', 'images/chandla-logo.png', 'images/chandla-logo.jpg'] as $img) {
                                    if (file_exists(public_path($img))) {
                                        $logoFile = public_path($img);
                                        break;
                                    }
                                }
                            ?>
                            <?php if(isset($message) && $logoFile): ?>
                                <img src="<?php echo e($message->embed($logoFile)); ?>" alt="Chandla Book" width="150" style="display:block;margin:0 auto 14px;border:0;height:auto;max-width:150px;">
                            <?php else: ?>
                                <img src="<?php echo e(url('images/logo.jpeg')); ?>" alt="Chandla Book" width="150" style="display:block;margin:0 auto 14px;border:0;height:auto;max-width:150px;">
                            <?php endif; ?>
                            <p style="margin:0;font-size:11px;font-weight:700;color:rgba(255,255,255,0.88);letter-spacing:0.14em;text-transform:uppercase;">Chandla register export</p>
                            <p style="margin:10px 0 0;font-size:22px;font-weight:700;color:#ffffff;line-height:1.25;letter-spacing:-0.02em;">Your PDF is ready</p>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="background-color:#ffffff;padding:32px 28px 24px;border-left:1px solid #e8e2d8;border-right:1px solid #e8e2d8;">
                            <?php if(isset($user)): ?>
                                <p style="margin:0 0 18px;font-size:15px;line-height:1.55;color:#334155;">
                                    Hello <?php echo e(strtok(trim((string) ($user->name ?? '')), ' ') ?: 'there'); ?>,
                                </p>
                            <?php endif; ?>
                            <p style="margin:0 0 18px;font-size:16px;line-height:1.6;color:#334155;">
                                Your <strong style="color:#1a3646;">chandla register PDF</strong> for
                                <strong style="color:#1a3646;"><?php echo e($event->title); ?></strong> is attached to this email.
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#faf8f3;border-radius:12px;border:1px solid #e8dcc8;margin:0 0 22px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0 0 10px;font-size:11px;font-weight:700;color:#946f22;text-transform:uppercase;letter-spacing:0.08em;">Event snapshot</p>
                                        <p style="margin:0 0 6px;font-size:15px;color:#1a3646;"><strong>Date:</strong> <?php echo e($event->event_date->format('l, F j, Y')); ?></p>
                                        <?php if($event->event_time): ?>
                                            <p style="margin:0 0 6px;font-size:15px;color:#334155;"><strong>Time:</strong> <?php echo e($event->event_time->format('h:i A')); ?></p>
                                        <?php endif; ?>
                                        <?php if($event->venue): ?>
                                            <p style="margin:0;font-size:15px;color:#334155;"><strong>Venue:</strong> <?php echo e($event->venue); ?></p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 14px;font-size:13px;line-height:1.65;color:#64748b;">
                                The attachment usually includes <strong style="color:#475569;">financial summary</strong>, denomination tally,
                                and separate sections for <strong style="color:#475569;">cash</strong>, <strong style="color:#475569;">cover</strong>, and <strong style="color:#475569;">gifts</strong> — formatted like a traditional ledger for printing or archive.
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:8px auto 0;">
                                <tr>
                                    <td style="border-radius:10px;background-color:#b8860b;">
                                        <a href="<?php echo e(route('client.events.show', $event->id)); ?>" target="_blank" rel="noopener noreferrer" style="font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;display:inline-block;padding:14px 28px;border-radius:10px;">
                                            Open event in Chandla Book
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:22px 0 0;font-size:14px;line-height:1.55;color:#64748b;">
                                Thank you for trusting Chandla Book with your collections.<br>
                                <span style="color:#94a3b8;">— Team Chandla Book</span>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8fafc;padding:18px 28px 26px;border-radius:0 0 14px 14px;border:1px solid #e8e2d8;border-top:none;text-align:center;">
                            <p style="margin:0 0 8px;font-size:12px;color:#64748b;line-height:1.5;">
                                Questions? Reply from the email linked to your account or sign in at<br>
                                <a href="<?php echo e(url('/client/login')); ?>" style="color:#1a3646;font-weight:600;text-decoration:none;"><?php echo e(url('/client/login')); ?></a>
                            </p>
                            <p style="margin:0;font-size:11px;color:#94a3b8;line-height:1.45;">
                                This email was sent automatically because you downloaded your event PDF.<br>
                                Please do not reply to this message unless your mailbox accepts replies.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/emails/event-pdf.blade.php ENDPATH**/ ?>