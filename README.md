# MaFilmo — Personal Film Journal

MaFilmo is a personal film tracking web application built with Laravel 12 and PHP 8.4. It allows users to search for movies using the TMDB API, manage personal watchlists, rate and comment on films they have seen, and view personalised viewing statistics on a dashboard.

## Features

- Real-time movie search via the TMDB API (AJAX with 250ms debounce)
- Add films to a **Seen** or **Watchlist** collection
- 1 to 5 star rating system with hover visual feedback
- Personal comments per film
- Film detail page : synopsis, cast, director, genres, runtime, TMDB rating
- Dashboard with personal statistics : favourite genre, average rating
- Responsive design — mobile, tablet and desktop
- French-language interface

## Tech stack

| Technology            | Version |
| --------------------- | ------- |
| PHP                   | 8.4     |
| Laravel               | 12.x    |
| MySQL                 | 8.0     |
| Bootstrap             | 5.3     |
| JavaScript (Vanilla)  | ES6+    |
| TMDB API              | v3      |
| Docker / Laravel Sail | —       |
| Node.js / Vite        | —       |

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed
- WSL2 enabled (Windows) with Ubuntu
- A [TMDB](https://www.themoviedb.org/) account to obtain a free API key

## Local development setup

### 1. Clone the repository

```bash
git clone [repository-url]
cd mafilmo
```

### 2. Configure the environment

```bash
cp .env.example .env
```

Open the `.env` file and configure the following variables :

```ini
# Database — uncomment and fill in
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

# TMDB API — add manually (not present in .env.example)
TMDB_API_KEY=your_tmdb_api_key
TMDB_BASE_URL=https://api.themoviedb.org/3
TMDB_IMAGE_URL=https://image.tmdb.org/t/p/w500
```

> **Note** : a free TMDB API key can be obtained at [themoviedb.org/settings/api](https://www.themoviedb.org/settings/api)

### 3. Start the containers

```bash
./vendor/bin/sail up -d
```

### 4. Install PHP dependencies

```bash
./vendor/bin/sail composer install
```

### 5. Generate the application key

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Run database migrations

```bash
./vendor/bin/sail artisan migrate
```

### 7. Access the application

Open [http://localhost:8000](http://localhost:8000) in your browser.

---

## Environment variables

| Variable         | Description                             | Example                           |
| ---------------- | --------------------------------------- | --------------------------------- |
| `APP_KEY`        | Laravel encryption key (auto-generated) | `base64:...`                      |
| `APP_ENV`        | Application environment                 | `local`                           |
| `APP_DEBUG`      | Debug mode                              | `true`                            |
| `DB_CONNECTION`  | Database driver                         | `mysql`                           |
| `DB_HOST`        | MySQL host (internal Docker network)    | `mysql`                           |
| `DB_PORT`        | MySQL internal port                     | `3306`                            |
| `DB_DATABASE`    | Database name                           | `laravel`                         |
| `DB_USERNAME`    | MySQL username                          | `sail`                            |
| `DB_PASSWORD`    | MySQL password                          | `password`                        |
| `TMDB_API_KEY`   | TMDB API key (**required**)             | `4c940b...`                       |
| `TMDB_BASE_URL`  | TMDB API base URL                       | `https://api.themoviedb.org/3`    |
| `TMDB_IMAGE_URL` | TMDB image base URL                     | `https://image.tmdb.org/t/p/w500` |

---

## Production deployment (Railway)

The application is deployed on [Railway](https://railway.app), which builds and runs the Docker container automatically on each push to the `main` branch.

### Deployment pipeline

```
git push origin main
        │
        v
Railway detects the push
        │
        v
Docker build (Dockerfile)
  ├── Install system dependencies
  ├── Install PHP dependencies (Composer)
  └── Build front-end assets (npm)
        │
        v
Container startup (CMD)
  ├── php artisan config:clear
  ├── php artisan cache:clear
  ├── php artisan migrate --force
  └── php artisan serve --host=0.0.0.0 --port=8000
```

### Railway environment variables

Environment variables must be configured **manually** in the Railway dashboard before the first deployment. They are only available at **container runtime**, not during the Docker build phase.

> **Important** : a misspelled variable name (e.g. `TMBD_API_KEY` instead of `TMDB_API_KEY`) produces no error — it simply returns `null`, causing silent failures in API calls. Always verify variable names against the `.env.example` reference file.

---

## Project structure

```
mafilmo/
├── app/
│   ├── Http/Controllers/     # DashboardController, SearchController, ListController...
│   └── Services/             # TmdbService — TMDB API calls
├── database/
│   └── migrations/           # Database schema
├── public/
│   └── js/mafilmo.js         # Centralised JavaScript (AJAX, debounce, star rating)
├── resources/
│   └── views/                # Blade templates (layout, dashboard, search, lists...)
├── routes/
│   └── web.php               # Route definitions
├── Dockerfile                # Production image
└── .env.example              # Environment variables template
```

---

## Security

- CSRF protection on all forms (`@csrf`)
- Authentication via Laravel Breeze
- `auth` middleware on all protected routes
- Systematic XSS escaping (`{{ }}` in Blade, `escapeHtml()` in JavaScript)
- SQL injection prevention via Eloquent ORM prepared statements
- Passwords hashed with bcrypt (`BCRYPT_ROUNDS=12`)
- `.env` file excluded from version control (`.gitignore`)
- TMDB API key stored as an environment variable

---

## License

Project developed as part of the **Développeur Web et Web Mobile** professional qualification (DWWM — Level 5).
