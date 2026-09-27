# Laravel SSO

[![Latest Version on Packagist](https://img.shields.io/packagist/v/phattarachai/laravel-sso.svg?style=flat-square)](https://packagist.org/packages/phattarachai/laravel-sso)
[![Tests](https://img.shields.io/github/actions/workflow/status/phattarachai/laravel-sso/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/phattarachai/laravel-sso/actions/workflows/run-tests.yml?query=branch%3Amain)
[![Code Style](https://img.shields.io/github/actions/workflow/status/phattarachai/laravel-sso/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/phattarachai/laravel-sso/actions/workflows/fix-php-code-style-issues.yml?query=branch%3Amain)
[![PHP Version](https://img.shields.io/packagist/dependency-v/phattarachai/laravel-sso/php?style=flat-square&label=php&logo=php&logoColor=white)](https://packagist.org/packages/phattarachai/laravel-sso)
![Laravel Version](https://img.shields.io/badge/laravel-12%20%7C%2013-FF2D20?style=flat-square&logo=laravel&logoColor=white)
[![Total Downloads](https://img.shields.io/packagist/dt/phattarachai/laravel-sso.svg?style=flat-square)](https://packagist.org/packages/phattarachai/laravel-sso)

"Sign in with Google" for a fleet of single-owner Laravel apps, as a drop-in. It replaces each app's password
login with three routes, an email allowlist and one owner account. Every app shares one Google OAuth client, and
Google's own session makes signing in to the next app a single click.

- **Allowlist, no sign-up.** Every email in `SSO_ALLOWED_EMAILS` signs in as the same local user
  (`SSO_LOGIN_AS`). Any other account gets a 403, and no user row is ever created.
- **Remember forever.** Every login sets Laravel's remember cookie, which lasts until the browser caps it (about 400 days).
- **Local dev without Google.** Google refuses redirect URIs on `.test`, so on a dev box `/login` signs the owner
  straight in. It requires **both** `APP_ENV=local` **and** a local host (`*.test`, `localhost`, `127.0.0.1`), so
  a production box that was mistakenly set to `APP_ENV=local` still goes to Google.
- **Inertia-aware.** `/login` and `/logout` answer an Inertia visit with a `409` + `X-Inertia-Location`, so the
  browser makes a full-page visit instead of an XHR that can't follow a cross-origin redirect.
- **IdP-ready.** The Socialite driver is config (`SSO_DRIVER`). A self-hosted IdP that ships a Socialite driver
  replaces Google without touching the apps.

## Installation

```bash
composer require phattarachai/laravel-sso
php artisan sso:install
```

`sso:install` adds the keys below to `.env.example`, checks that the owner user exists, and prints the redirect
URI to register on the Google OAuth client (`<APP_URL>/auth/sso/callback`).

```dotenv
SSO_ALLOWED_EMAILS=you@gmail.com,you@your-company.com
SSO_LOGIN_AS=you@gmail.com          # optional; defaults to the first allowed email
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
```

Then remove the app's own `login` / `logout` routes, the password controller and the login page. The package
registers these routes itself:

| Method | URI | Name | |
|---|---|---|---|
| GET | `/login` | `login` | redirect to Google (or the local owner login); `?select=1` forces the account chooser |
| GET | `/auth/sso/callback` | `sso.callback` | allowlist check, log in the owner, `redirect()->intended()` |
| POST | `/logout` | `logout` | log out → `/signed-out` |
| GET | `/signed-out` | `sso.signed-out` | "You're signed out" page with a sign-in link |

The `auth` middleware already redirects guests to the route named `login`, so nothing else needs to change. API,
webhook and broadcasting routes are untouched: only the web session login changes.

## Google OAuth client

In the Google Cloud Console, create one OAuth client of type **Web application** and share it across every app:

- Consent screen: External, with the `openid`, `email` and `profile` scopes only. Those need no Google verification.
- Authorized redirect URIs: one line per app, e.g. `https://notes.example.app/auth/sso/callback`.

Google has no API or `gcloud` command for Web OAuth clients, so adding an app means adding its redirect URI in the Console.

## Configuration

`php artisan vendor:publish --tag=sso-config` publishes `config/sso.php`. It sets the driver, guard, home URL,
the local-login switch and the local host patterns. The rejected and signed-out pages are plain Blade with no
build step (`--tag=sso-views`).

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
