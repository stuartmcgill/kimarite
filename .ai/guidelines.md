# Project guidelines

This document is intended to help AI assistants (and humans) understand the codebase well enough to make changes safely and consistently.

## What this project is

A visualisation app showing historical trends in *kimarite* (winning techniques) in professional sumo wrestling.
Data is fetched from [sumo-api.com](https://www.sumo-api.com/), agrregated, and then available for display as interactive charts.

## Tech stack

| Layer | Technology |
|---|---|
| Backend | PHP / Laravel |
| Frontend | Vue 3 (Composition API) |
| UI components | PrimeVue |
| Charts | vue-chartjs (Chart.js wrapper) |
| Asset bundling | Vite |
| Local dev environment | Laravel Sail (Docker) |
| PHP code style | Laravel Pint |
| JS/TS formatting | Prettier |
| Tests | PHPUnit |

## Repository layout

```
app/                  Laravel application code (controllers, services, models, etc.)
bootstrap/            Laravel bootstrap files — do not modify unless you know why
config/               Laravel config files
database/             Migrations and seeders
public/               Web root (compiled assets land here after `npm run build`)
resources/
  js/                 Vue components, TypeScript, entry points
  views/              Laravel Blade templates (likely minimal — mostly a Vue SPA)
routes/               Laravel route definitions
tests/                PHPUnit test suites
```

## Development workflow

```bash
# Start the Docker environment and dev server
sail up -d
php artisan serve

# In a second terminal — starts Vite with hot module replacement
npm run dev
```

Browse to http://127.0.0.1:8000/

## Code conventions

### PHP
- Follow the **PSR-12** style enforced by **Laravel Pint**. Run `./vendor/bin/pint` to auto-format before committing.
- Use Laravel conventions: Eloquent models, controllers, service classes for business logic.
- Keep controllers thin. Data fetching and transformation logic belongs in service classes, not controllers.

### Vue / TypeScript
- Use the **Composition API** (`<script setup>`) for all Vue components.
- TypeScript is used — add types rather than using `any`.
- Format with **Prettier** (`npx prettier --write resources/`). The `.prettierrc` at the root defines the rules.
- PrimeVue components are the preferred source for UI elements (buttons, dropdowns, data tables, etc.) — avoid adding a separate component library.
- Charts are built with **vue-chartjs**. Extend existing chart components rather than reaching for another charting library.

### General
- Keep components focused on a single responsibility.
- Avoid committing `.env` files. Use `.env.example` to document any new environment variables.

## Data flow

The app fetches sumo bout data from the [Sumo API](https://www.sumo-api.com/). The general flow is:

1. Laravel backend fetches, aggregates, and saves data from the external API to its own database.
2. Controllers expose the processed data as JSON responses.
3. Vue components consume these API endpoints and render charts.

## Adding a new chart or view

1. Add a Laravel route in `routes/` pointing to a controller method.
2. Implement the controller method (and any new service logic in `app/`).
3. Create a Vue component in `resources/js/` that calls the endpoint and renders a chart using vue-chartjs.
4. Wire the component into the app's navigation/routing as appropriate.

## Running tests

```bash
php artisan test
```

Add tests for any new service/controller logic. The test suite lives in `tests/`.

## Things to avoid

- Do not introduce a new UI component library alongside PrimeVue.
- Do not introduce a new charting library alongside vue-chartjs/Chart.js.
- Do not store secrets in code or commit `.env` files.
- Do not skip `pint` and `prettier` — inconsistent formatting makes diffs harder to review.
