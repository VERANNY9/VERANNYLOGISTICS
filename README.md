# Veranny Logistics

Veranny Logistics is a PHP website and internal operations portal for logistics, produce trading, orders, staff, trips, meetings, and finance records. The public website is served by `index.php`; the protected staff portal is served by `admin.php`.

The application currently stores records in JSON files and uploaded media in `uploads/`. This is suitable for a small deployment, but the files must be backed by a persistent Railway Volume or they will be lost when a container is recreated.

## Run locally

Requirements:

- PHP 8.2 or newer
- Apache, or PHP's built-in server for a basic local preview

From this directory:

```bash
php -S localhost:8080
```

Open <http://localhost:8080/>. The staff portal is at <http://localhost:8080/admin.php>.

For production-like local testing with Docker:

```bash
docker build -t veranny-logistics .
docker run --rm -p 8080:8080 -e PORT=8080 veranny-logistics
```

## Deploy to Railway

1. Push this project to a GitHub repository.
2. In Railway, create a new project and choose **Deploy from GitHub repo**.
3. Select the repository. Railway will detect the included `Dockerfile`.
4. In **Settings → Networking**, generate a public domain.
5. Add a Railway Volume mounted at `/var/www/html/uploads` if uploaded images/documents must survive deploys. Add another volume or external database before treating the JSON records as business-critical.
6. Deploy and open the generated domain.

Railway supplies `PORT` automatically. The included entrypoint configures Apache to use it; no manual port setting is normally required.

## Admin access

Use the existing CEO login or the email and password of an account already present in `users.json`. The current CEO login is `VerannyLogistics` with password `Buhembe@12`. Do not commit real passwords or private customer data to a public repository.

## Important production notes

- JSON files are not a safe substitute for a database under concurrent traffic. Move users, orders, finance, trips, and meetings to PostgreSQL before scaling.
- Railway's filesystem is ephemeral without a Volume. A Volume mounted only at `uploads/` protects media, but JSON records still need persistent storage.
- Restrict registration and add CSRF protection before exposing the admin portal publicly.
- Uploaded files should be validated by MIME type and extension, renamed server-side, and served from a non-executable location.
- Rotate any credentials that may previously have been shared or committed.

## Project files

| File | Purpose |
| --- | --- |
| `index.php` | Public website and order form |
| `admin.php` | Login and staff operations portal |
| `users.json`, `staff.json` | Accounts and staff profiles |
| `orders.json` | Public order submissions |
| `finance.json`, `trips.json`, `meetings.json` | Portal records |
| `uploads/` | Uploaded images, documents, and video |
| `Dockerfile` | Railway deployment image |
