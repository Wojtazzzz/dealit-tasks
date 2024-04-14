
# DealIT - Tasks app

Rest Api containing essential operations to manage your tasks.
SQLite-based for simplicity. Created for the recruitment process.

## Installation

Copy repo

```bash
gh repo clone Wojtazzzz/dealit-tasks
```

Create env variables and fill with own credentials (consider MAILGUN variables if you want to work with emails)

```bash
cd dealit-tasks && cp .env.example .env
```

Install dependencies

```bash
composer install
```

Run queue (only mails are queued)

```bash
php artisan queue:work
```

## API Reference
With example payloads

### Register

```http
POST /api/auth/register
```

```json
{
  "name": "jane.doe",
  "email": "jane.doe@gmail.com",
  "password": "Test123!"
}
```

### Login
Returns token which should be passed as header in next requests:
`Authorization: Bearer {$token}`

```http
POST /api/auth/login
```

```json
{
  "email": "jane.doe@gmail.com",
  "password": "Test123!"
}
```

### Get tasks

```http
GET /api/tasks
```

### Get task

```http
GET /api/tasks/{id}
```

### Create task

```http
POST /api/tasks
```

```json
{
  "title": "Make a bed",
  "description": "Ensure every corner is crisp and every pillow is plumped for inviting retreat.",
  "status": "open" // "open", "closed" or "during"
}
```

### Update task

```http
PUT /api/tasks/{id}
```

```json
{
  "title": "Make a bed",
  "description": "Ensure every corner is crisp and every pillow is plumped for inviting retreat.",
  "status": "open" // "open", "closed" or "during"
}
```

### Delete task

```http
DELETE /api/tasks/{id}
```
