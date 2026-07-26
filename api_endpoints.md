# Chandla Book App API Integration List

This document outlines all the RESTful API endpoints that have been integrated into the Flutter mobile application's data repositories. The base URL for all these endpoints is configured as `https://skylighttech.in/chandlaApp/api/v1`.

### Authentication & Profile (AuthRepository)
- `POST /auth/login` - Login with email/password.
- `POST /auth/register` - Register a new account.
- `POST /auth/google/login` - Social login with Google.
- `POST /auth/facebook/login` - Social login with Facebook.
- `POST /auth/apple/login` - Social login with Apple.
- `POST /auth/phone/send-otp` - Request OTP for phone verification.
- `POST /auth/phone/verify-otp` - Verify received OTP.
- `POST /auth/forgot-password` - Request password reset link.
- `GET /auth/me` - Fetch the currently authenticated user's profile data.
- `GET /user/stats` - Fetch user statistics (total events, contacts, subscription status).
- `PUT /user/profile` - Update user profile information.
- `POST /auth/change-password` - Update the user's password.
- `POST /auth/logout` - Logout and invalidate the token.

### Events (EventRepository)
- `GET /events` - Fetch a paginated list of events (supports filtering and search).
- `GET /events/upcoming` - Fetch a list of upcoming active events.
- `GET /events/archived` - Fetch a list of archived events.
- `GET /events/{id}` - Fetch details of a specific event.
- `GET /events/{id}/stats` - Fetch statistics for a specific event (total collected, spent, etc).
- `POST /events` - Create a new event (supports Multipart/FormData for image uploads).
- `POST /events/{id}` - Update an existing event (using `_method=PUT` for FormData compatibility).
- `DELETE /events/{id}` - Delete an event.
- `POST /events/{id}/archive` - Archive an event.
- `POST /events/{id}/unarchive` - Restore an archived event.
- `POST /events/{id}/duplicate` - Clone an event's details to a new one.

### Ledger / Chandlas (ChandlaRepository)
- `GET /chandlas?event_id={id}` - Fetch a paginated list of ledger entries (requires `event_id`).
- `GET /chandlas/{id}` - Fetch details of a specific ledger entry.
- `GET /chandlas/stats?event_id={id}` - Fetch ledger statistics (Total amount, Cash, Online).
- `POST /chandlas` - Create a new ledger entry.
- `PUT /chandlas/{id}` - Update an existing ledger entry.
- `DELETE /chandlas/{id}` - Delete a ledger entry.

### Contacts (ContactRepository)
- `GET /contacts` - Fetch a paginated list of contacts.
- `GET /contacts/{id}` - Fetch details of a specific contact.
- `POST /contacts` - Create a new contact.
- `PUT /contacts/{id}` - Update an existing contact.
- `DELETE /contacts/{id}` - Delete a contact.

### Payment & Subscription Packs (TransactionRepository)
- `GET /packs` - List all available premium/subscription packs.
- `POST /packs/{slug}/order` - Create a new Razorpay/payment order for a pack.
- `POST /packs/{slug}/verify` - Verify the payment signature and activate the pack.
- `GET /transactions` - Fetch user's transaction history.
- `GET /transactions/{txnNumber}` - Fetch detailed invoice/receipt for a transaction.

### Digital Invitations (InvitationRepository)
- `GET /invitations` - Fetch paginated list of invitations.
- `GET /invitations/{id}` - Fetch a specific invitation.
- `POST /invitations` - Create a new digital invitation.
- `PUT /invitations/{id}` - Update an invitation.
- `DELETE /invitations/{id}` - Delete an invitation.

### Marriage Invitations (MarriageInvitationRepository)
- `GET /marriage-invitations` - Fetch paginated list of marriage invitations.
- `GET /marriage-invitations/{id}` - Fetch a specific marriage invitation.
- `POST /marriage-invitations` - Create a new marriage invitation.
- `PUT /marriage-invitations/{id}` - Update a marriage invitation.

### Notifications (NotificationRepository)
- `POST /notifications/device/register` - Register FCM token for push notifications.
- `GET /notifications` - Fetch paginated list of notifications.
- `PUT /notifications/{id}/read` - Mark a specific notification as read.
- `POST /notifications/mark-all-read` - Mark all notifications as read.
- `DELETE /notifications/clear-all` - Delete all notifications.

### Reports (ReportRepository)
- `GET /reports/dashboard` - Fetch dashboard summary report.
- `GET /reports/events` - Fetch events overview report.
- `GET /reports/entries` - Fetch ledger entries summary report.

### Push Notifications (New Module - Direct API)
The base URL for these endpoints is `https://skylighttech.in/chandlaApp/api` (without the `/v1` prefix).
- `POST /device-token` - Register or update a device FCM token (preventing duplicates).
- `GET /notifications` - Fetch a paginated list of notifications received by the authenticated user.
- `GET /notifications/unread-count` - Get count of unread notifications for the authenticated user.
- `POST /notifications/{id}/read` - Mark a specific notification as read.
- `POST /notifications/read-all` - Mark all notifications for the user as read.
- `DELETE /notifications/{id}` - Delete a notification from the authenticated user's history.
- `POST /admin/notifications/send` - Send FCM push notifications and log to database (Admin Only).

