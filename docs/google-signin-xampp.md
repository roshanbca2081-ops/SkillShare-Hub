# Google Sign-In in XAMPP

## Private credentials file

Edit `config/social-auth.local.php` (the file in the project's `config` folder). Put your rotated Google Web OAuth client values in the existing keys:

- `google.client_id`
- `google.client_secret`

Do not paste credentials into chat, frontend JavaScript, or any tracked file. `.gitignore` excludes `config/social-auth.local.php` and the Windows double-extension variant `config/social-auth.local.php.php`.

## Exact localhost OAuth callback

This implementation derives the callback from the current `social-auth.php` URL. For this project, open it at `http://localhost/SkillShare-Hub/` and register this exact Google Authorized redirect URI:

`http://localhost/SkillShare-Hub/social-auth.php`

Do not add `?provider=google` or `?callback=google`. The initial authorization request uses `provider=google`, but the callback identifies Google from the session-bound OAuth state. The callback URL is sent unchanged in both the authorization request and token exchange.

## Verify readiness

Use the safe CLI checker from the project root:

`E:\xampp\php\php.exe scripts\check-google-oauth.php`

It prints only whether each value is present and the source filename; it never prints credential contents. Do not retry the browser flow until both are reported configured. If they are missing, confirm the local filename is exactly `config/social-auth.local.php`, the PHP keys are `client_id` and `client_secret`, and restart Apache after setting Apache environment variables.

## Test the flow

1. Ensure PHP cURL is enabled in the XAMPP `php.ini` used by Apache, then restart Apache.
2. Open `http://localhost/SkillShare-Hub/login.php` and choose **Continue with Google**.
3. Confirm Google opens without `redirect_uri_mismatch`; sign in with a Google account allowed by the consent-screen audience/test-user settings.
4. Confirm the browser returns to `social-auth.php`, then to the dashboard appropriate for the matched SkillShare Hub user's role.
5. For a new Google email, the app creates a fresher account. An existing account is linked only when Google returns a verified email. Confirm the account in the database only after sign-in; do not reset or delete user data.

The local config is currently created with empty values. The actual Client ID and rotated Client Secret must be entered locally before the diagnostic can report ready. Do not report the full flow as successful until the browser round trip and dashboard session have both been verified.
