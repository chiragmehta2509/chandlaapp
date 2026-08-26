<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matrimonial plan — verify UPI</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Find Partner (Matrimonial) plan — action required</h2>
    <p style="margin-top: 0;">A user paid via <strong>Google Pay / UPI / PhonePe</strong> and submitted a transaction reference. Confirm the amount in your UPI app before approving in Admin.</p>

    <table cellpadding="6" cellspacing="0" border="0" style="border-collapse: collapse; margin-top: 12px;">
        <tr>
            <td><strong>Plan</strong></td>
            <td><?php echo e($planDef['label']); ?> <span style="color:#6b7280">(₹<?php echo e(number_format($planDef['price_inr'], 0)); ?>)</span></td>
        </tr>
        <tr>
            <td><strong>User</strong></td>
            <td><?php echo e($user->name); ?> <?php if($user->email): ?>&lt;<?php echo e($user->email); ?>&gt;<?php endif; ?> <?php if($user->phone): ?> · <?php echo e($user->phone); ?><?php endif; ?></td>
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
        <a href="<?php echo e($adminUrl); ?>" style="background: #1A3646; color: #fff; padding: 10px 16px; border-radius: 8px; text-decoration: none; display: inline-block;">Open in admin panel</a>
    </p>
    <p style="color: #6b7280; font-size: 13px; margin-top: 16px;">In <strong>Admin → Payments</strong>, open this record and use <strong>Mark Completed</strong> only after you verify the payment. The user’s <strong>Find Partner</strong> plan will then activate.</p>
</body>
</html>
<?php /**PATH /home/chandlabook/public_html/resources/views/emails/admin-matrimonial-plan-pending.blade.php ENDPATH**/ ?>