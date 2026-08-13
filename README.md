# Viral Verdict

Viral Verdict is a review website for publishing product articles, ratings,
and affiliate links. The first release focuses on a simple public reading
experience and a protected editorial workflow.

## MVP

The public website has two main pages:

- **Home:** a list of published review articles, ordered with the most recently
  published first.
- **Article:** a dedicated page containing the complete review and product
  details.

Administrators can sign in to create and edit articles and to publish or
unpublish them. Draft and unpublished articles are never visible on the public
pages.

Each article can contain:

- product title and image;
- author;
- short summary;
- full description or review;
- rating or score;
- category and tags;
- affiliate link;
- creation, publication, and last-updated dates.

The detailed product rules, assumptions, acceptance criteria, and deferred work
are maintained in [`development/scope.md`](development/scope.md).

## Technology

- Laravel 13 on PHP 8.4+
- React 19 and Inertia.js 3
- Tailwind CSS 4 and Vite 8
- SQLite for local development
- Pest 5 and Larastan/PHPStan
- NativePHP Mobile 4 foundations, with a native product experience deferred
  from the web MVP

## Local development

Install the PHP and JavaScript dependencies, create the local environment, and
prepare the database:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
```

Then start the application with:

```bash
composer run dev
```

The repository includes the public article catalogue, dedicated review pages,
administrator authentication, image uploads, and the draft/publish editorial
workflow described in `development/scope.md`.

To create the existing local development account, run:

```bash
php artisan db:seed
```

The seeded credentials are `test@example.com` and `password`. They are for
local development only. The seeded user is an administrator and can manage
articles at `/admin/articles`.

Uploaded article images use the public filesystem disk. Create the local
storage link once with:

```bash
php artisan storage:link
```

For Laravel Herd configuration, see
[`development/herd.md`](development/herd.md).

## Quality checks

```bash
composer test
npm run lint:check
npm run format:check
npm run types:check
npm run build
```

## Mobile status

NativePHP remains available as a foundation, but native article browsing and
administration are not part of the first release. The remote Laravel server and
database will remain canonical if a native client is added later.
