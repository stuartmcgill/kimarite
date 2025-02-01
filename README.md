# Overview

This is a [Laravel](https://laravel.com/)/[Vue](https://vuejs.org/) project using
[PrimeVue](https://primevue.org/) and [vue-chartjs](https://vue-chartjs.org/)
for the UI.

The data comes from https://www.sumo-api.com/.

![Kimarite graph screenshot](https://github.com/stuartmcgill/kimarite/blob/main/public/screenshot.jpg)

# Local development

In one terminal:

```
sail up -d
php artisan serve
```

In another: `npm run dev`

Browse to http://127.0.0.1:8000/

# Production install

```
ssh ... @stuartmcgill.org
cd domains/sumo.stuartmcgill.org/kimarite
git fetch
# Replace tag with release number
git checkout 1.0.0
composer install --no-dev --optimize-autoloader
npm ci
npm run build
npm install --omit=dev
```
