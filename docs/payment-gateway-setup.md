# Verified eSewa and Khalti checkout

The session checkout supports eSewa ePay v2 and Khalti Web Checkout (KPG-2). A client-provided transaction ID is never accepted as proof of payment. Payments stay `pending` until the provider verifies completion server-to-server; only then does the app mark the payment completed and approve the booking. Fonepay and the old unverified card/bank options are disabled.

## 1. Apply the database migration

For an existing database, run the migration once. It makes course references optional for standalone session bookings and adds a JSON association field used to safely connect verified provider transactions to the booking. The migration is [2026_11_05_000001_allow_session_payment_without_course.sql](../database/migrations/2026_11_05_000001_allow_session_payment_without_course.sql). New database setups should use the updated canonical [payments schema](../database/schema/17_payments.sql).

## 2. Configure sandbox merchant credentials

Copy `config/payment.local.php.example` to `config/payment.local.php`. The destination file is ignored by Git. Set:

- `environment` to `sandbox`
- `base_url` to the complete local project origin, e.g. `http://localhost/SkillShare-Hub`
- `esewa_product_code` and `esewa_secret_key` from eSewa's merchant sandbox
- `khalti_secret_key` from the Khalti sandbox merchant dashboard
- `npr_per_usd` to the NPR rate used by the app's displayed USD session prices

Use merchant-issued sandbox credentials only. Do not copy production keys into a local development environment or commit/share `payment.local.php`.

Alternatively configure `SKILLSHARE_PAYMENT_ENV`, `SKILLSHARE_APP_URL`, `SKILLSHARE_ESEWA_PRODUCT_CODE`, `SKILLSHARE_ESEWA_SECRET_KEY`, `SKILLSHARE_KHALTI_SECRET_KEY`, and `SKILLSHARE_NPR_PER_USD` in the server environment. Environment values override the local config file.

## 3. Test and deploy

1. Ensure PHP cURL is enabled and outbound HTTPS is allowed.
2. Test the complete redirect/return flow using the providers' sandbox credentials and test accounts.
3. Set `environment` to `production` only after merchant approval and successful sandbox testing; configure the production product code/secret keys and a public HTTPS `base_url`.
4. Check server logs if a provider is unavailable. The app deliberately does not mark a payment paid when an API lookup fails or returns a mismatched status, amount, order, or signature.

eSewa callbacks have their HMAC signature checked and then use the eSewa status API. Khalti callbacks are confirmed with Khalti's payment lookup endpoint; only `Completed` and non-refunded payments pass.

Official docs: [eSewa ePay](https://developer.esewa.com.np/pages/Epay) · [Khalti Web Checkout](https://docs.khalti.com/khalti-epayment/).
