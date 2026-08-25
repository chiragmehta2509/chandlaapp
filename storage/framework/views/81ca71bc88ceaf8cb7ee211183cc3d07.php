<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct GPay unlock — verify payment</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Direct GPay unlock — action required</h2>
    <p style="margin-top: 0;">A user submitted a UPI reference for the one-time <strong>Direct GPay QR</strong> unlock. Confirm the payment in your UPI app before approving.</p>

    <table cellpadding="6" cellspacing="0" border="0" style="border-collapse: collapse; margin-top: 12px;">
        <tr>
            <td><strong>Event</strong></td>
            <td><?php echo e($event->title); ?> <span style="color:#6b7280">(id <?php echo e($event->id); ?>)</span></td>
        </tr>
        <tr>
            <td><strong>User</strong></td>
            <td><?php echo e($user->name); ?> &lt;<?php echo e($user->email); ?>&gt;</td>
        </tr>
        <tr>
            <td><strong>Amount</strong></td>
            <td>INR <?php echo e(number_format($payment->amount, 2)); ?></td>
        </tr>
        <tr>
            <td><strong>UPI reference / Txn ID</strong></td>
            <td style="font-family: ui-monospace, monospace;"><?php echo e($payment->transaction_id); ?></td>
        </tr>
        <tr>
            <td><strong>Submitted</strong></td>
            <td><?php echo e($payment->created_at->format('M d, Y h:i A')); ?></td>
        </tr>
    </table>

    <p style="margin-top: 20px;">
        <a href="<?php echo e($adminUrl); ?>" style="background: #4f46e5; color: #fff; padding: 10px 16px; border-radius: 8px; text-decoration: none; display: inline-block;">Open in admin panel</a>
    </p>
    <p style="color: #6b7280; font-size: 13px; margin-top: 16px;">In Admin → Payments, open this record and use <strong>Mark Completed</strong> only after you verify the payment. Then the user will see <strong>Direct QR</strong> on their events.</p>
</body>
</html>
<?php /**PATH /home/chandlabook/public_html/resources/views/emails/admin-direct-gpay-pending.blade.php ENDPATH**/ ?>