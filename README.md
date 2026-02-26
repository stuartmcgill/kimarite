# Overview

This is a [Laravel](https://laravel.com/)/[Vue](https://vuejs.org/) project using
[PrimeVue](https://primevue.org/) and [vue-chartjs](https://vue-chartjs.org/)
for the UI.

The data comes from https://www.sumo-api.com/.

![Kimarite graph screenshot](https://github.com/stuartmcgill/kimarite/blob/main/public/screenshot.jpg)

# Local development

## Installation

```shell
npm ci
npm run build
cp .env.example .env
```

Adjust `.env` according to your local database setup (e.g. if not using `sail`).

```shell
composer install
sail up -d
php artisan migrate
```

Browse to http://127.0.0.1:8000/ and you should see a page without any data.

## User creation

At first the database will be empty. To populate the kimarite data you will need to be logged in as a Laravel user.
To create a user on the development database you can run:

```shell
php artisan db:seed --class=UserSeeder
```

This will create a user called `admin@example.org`, password `admin`.

## Running the app

In one terminal:

```shell
php artisan serve
```

In another:

```shell
npm run dev
```

## Copying the data from Sumo API

Go to http://127.0.0.1:8000/refresh and login (e.g. as the user you created earlier). Click 'Rebuild'. This will take some time (30 minutes)
or so to rebuild the database.

After it's finished your local environment should look the same as the live site.

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
