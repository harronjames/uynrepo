# Simple blog application based on Laravel

The goal of this repository is to showcase good [Laravel](https://laravel.com) development practices with a simple application.

_Read this `README.md` in [other languages](./translations/Translations.md)._

>[!IMPORTANT]
>This project is under development. Not all functionality is finished and much can still be improved. If you want to help with the development of the project, you can select an [issue](https://github.com/gomzyakov/laravel-blog/issues), do it and open a PR.

## Features

- 📚 Creating and editing posts
- 🥑 Categories
- 🔥 Popular posts
- 🎉 Admin panel
- 🔧 Manage users, posts, categories and tags
- 👥 Roles: reader and administrator
- 🔐 Personal account
- 💬 Comments and likes
- 🖋️ Post`s visual editor

## Preview

![Laravel blog main page](docs/screenshot-main-page.png)

## Requesting features

Open a new [issue](https://github.com/gomzyakov/laravel-blog/issues) to request a feature (or if you find a bug).

## How to run blog locally? 

Clone the project:

```bash
git clone git@github.com:gomzyakov/laravel-blog.git
```

I believe you already have Docker installed. If not, just do it on [Mac](https://docs.docker.com/desktop/install/mac-install/), [Windows](https://docs.docker.com/desktop/install/windows-install/) or [Linux](https://docs.docker.com/desktop/install/linux-install/).

Copy the environment settings:

```bash
cp .env.local .env
```

Build and start the `laravel-blog` environment with:

```bash
docker compose up -d --build
```

And open http://127.0.0.1:8000 in your favorite browser. Happy using Laravel Blog!

>On first start the app container will automatically install Composer dependencies, ensure the Laravel `.env` exists, generate the application key, and run database migrations.

## How to get inside the container?

Access to the Docker container:

```bash
docker exec -ti laravel-blog-app bash
```

## License

This is open-sourced software licensed under the [MIT License](https://github.com/gomzyakov/php-code-style/blob/main/LICENSE).


[![GitHub release](https://img.shields.io/github/release/gomzyakov/laravel-blog.svg)](https://github.com/gomzyakov/laravel-blog/releases/latest)
[![license](https://img.shields.io/badge/License-MIT-green.svg)](https://github.com/gomzyakov/laravel-blog/blob/development/LICENSE)
[![codecov](https://codecov.io/gh/gomzyakov/laravel-blog/branch/main/graph/badge.svg?token=4CYTVMVUYV)](https://codecov.io/gh/gomzyakov/laravel-blog)
