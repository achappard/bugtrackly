# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## About

BugTrackly is a bug tracking application for freelancers, built on Laravel 13 (PHP 8.3+) with Inertia.js v3 and Vue 3 (no separate SPA/API — controllers return Inertia responses that render Vue pages directly).

## Common commands

Local dev machine note: the default `php` on PATH may resolve to a version below 8.3 (check with `php -v`); if so, invoke `composer`/`php artisan` with an explicit PHP 8.3+ binary instead of relying on PATH.

```bash
# Install
composer install
npm install

# Local dev (serves app + queue listener + logs + Vite, concurrently)
composer run dev

# Frontend only
npm run dev       # Vite dev server
npm run build     # Production build

# Database
php artisan migrate
php artisan bugtrackly:default_user   # interactive: creates the first admin user
php artisan db:seed                   # seeds fake data + default admin (a@a.com / a)

# Tests (PHPUnit, no Pest)
php artisan test
php artisan test --filter=TestName
vendor/bin/phpunit tests/Feature/Auth/AuthenticationTest.php

# Code style
vendor/bin/pint          # Laravel Pint (PHP formatting)

# IDE helper (regenerate _ide_helper.php after changing bindings/facades)
php artisan ide-helper:generate
```

Production deployment is handled via `Envoy.blade.php` (`envoy run deploy` / `envoy run deploy_vue`) — it builds assets locally, syncs them by rsync, and resets the remote repo to a tagged release. Not used in normal dev workflow.

## Architecture

### Backend: Repository pattern behind interfaces

Almost all data access goes through a `Repository` bound to an `Interface`, registered in `app/Providers/AppServiceProvider::register()`. When adding a new domain concept, add both the interface and implementation under `app/Repositories/<Domain>/` and bind it there — controllers type-hint the interface, never the concrete repository.

Existing domains: `Bugs`, `BugInfos`, `Projects`, `Users`, `Locales`, `RolesPersmissions`, `TicketCategory`.

### Custom roles/permissions (not Spatie)

Roles and permissions are a bespoke system, not a package:
- `RolesPersmissionsRepositoryInterface` loads roles with their permissions.
- `User` gets `hasRole()` / `hasPermissionTo()` via `App\Trait\User\HasRoleAndPersmissionTrait`.
- `PersmissionsServiceProvider::boot()` dynamically registers a `Gate::define()` for every permission returned by the repository, plus a few hand-written gates (`view-project`, `view-bug`, `view-bug-comment`) that check ownership/relationship rather than a flat permission.
- Route middleware uses these directly: `can:manage-users`, `can:access-settings`, `can:view-project,project`, `can:view-bug,project,bug`, etc. (see `routes/web.php`).

When adding a new permission, it must exist in whatever the `RolesPersmissionsRepository` reads from (DB-backed) — the gate is generated automatically from it, no manual `Gate::define` needed unless it's a relationship-based check like the ones above.

### i18n spans backend and frontend

Translation strings live in `lang/<locale>/bugtrackly.php` (PHP arrays), not Laravel's default `lang/<locale>.json` flow for app content. The chain:

1. `App\Http\Middleware\HandleInertiaRequests::share()` calls `TranslationHelper::getTranslations($locale)` (falls back to `en` if the file is missing) and shares the whole array as the `translations` Inertia prop.
2. On the Vue side, `resources/js/Helpers/translations.js` exposes `trans(key)`, `trans_params(key, ...values)`, and `trans_choice(key, count)` which read from `usePage().props.translations`. `trans_choice` implements its own Laravel-style pluralization parser (`{n}` exact match, `[n,*]` range match, `|` separated choices) — it does not call the backend.

Locale switching itself goes through `LocaleController@setLocale` (`POST /set-locale`) and `App\Http\Middleware\SetLocale`.

When adding user-facing strings, add the key to both `lang/fr/bugtrackly.php` and `lang/en/bugtrackly.php`, then use `trans()`/`trans_choice()` in Vue — don't hardcode text in components.

### Frontend structure

- `resources/js/Pages/` — Inertia page components, one per controller action that renders a page (mirrors route structure, e.g. `Pages/SingleProject`, `Pages/Bug`, `Pages/Settings`).
- `resources/js/Layouts/` — shared page shells.
- `resources/js/Components/` — reusable UI (`ui/`), layout pieces (`layout/`), and transitions.
- `resources/js/Store/` — Vuex store; domain modules under `Store/modules/` (`bug.js`, `current_project.js`, `navigation.js`, `projects_management.js`, `users_management.js`).
- `resources/js/Helpers/` — cross-cutting frontend utilities (translations, datatable helpers, date/number formatting, Bootstrap tooltips, etc.), imported directly into components rather than being global mixins.

### Domain model

Core entities: `Project` ↔ `Bug` ↔ `BugComment` (+ `BugCommentFile` attachments), `BugLog` (audit trail), `TicketCategory`, `User`. Bug and project creation/comment events (`BugCreated`, `BugCommentCreated`) are dispatched to listeners in `app/Listeners/` which trigger notifications in `app/Notifications/Bug/` (e.g. `BugCreatedNotification`, `BugCommentCreatedNotification`) — follow this event → listener → notification chain rather than sending notifications directly from controllers.

User and project photo handling is factored into reusable traits (`App\Trait\User\HasProfilePhoto`, `App\Trait\Project\HasProjectPhoto`) that read their storage disk/path from `config/bugtrackly.php` (`BUGTRACKLY_*` env vars) rather than hardcoding `public` disk paths.

### Config

App-specific settings (support email, avatar/project photo storage disk & path, gravatar toggle, etc.) live in `config/bugtrackly.php`, documented inline and overridable via `BUGTRACKLY_*` env vars — check there before adding new hardcoded paths or emails.
