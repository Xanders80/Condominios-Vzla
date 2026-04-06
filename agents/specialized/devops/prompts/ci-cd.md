# CI/CD Pipeline Configuration - DevOps

## GitHub Actions Pipeline for Condominios-Vzla

### 1. CI Pipeline (on push/PR)
```yaml
name: CI Pipeline
on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main, develop]

jobs:
  lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP 8.5
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.5'
          extensions: mbstring, dom, fileinfo, mysql
      - name: Install Dependencies
        run: composer install --prefer-dist --no-interaction
      - name: Run Pint
        run: ./vendor/bin/pint --test

  test:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: testing
          MYSQL_ROOT_PASSWORD: root
        ports:
          - 3306:3306
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP 8.5
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.5'
          extensions: mbstring, dom, fileinfo, mysql
      - name: Install Dependencies
        run: composer install --prefer-dist --no-interaction
      - name: Setup Environment
        run: |
          cp .env.example .env
          php artisan key:generate
      - name: Run Migrations
        run: php artisan migrate --force
      - name: Run Tests
        run: php artisan test

  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: '20'
      - name: Install Dependencies
        run: npm ci
      - name: Build Assets
        run: npm run build
```

### 2. CD Pipeline (on main merge)
```yaml
  deploy:
    needs: [lint, test, build]
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Deploy to Production
        run: |
          # SSH to server
          # Pull latest code
          # composer install --no-dev --optimize-autoloader
          # php artisan migrate --force
          # php artisan config:cache
          # php artisan route:cache
          # php artisan view:cache
          # npm ci --production && npm run build
          # php artisan optimize
          # php artisan storage:link
```

### 3. Environment Configuration
- Development: .env.local
- Staging: .env.staging
- Production: .env.production
- Never commit .env files

### 4. Deployment Checklist
- [ ] All tests pass
- [ ] Lint passes (Pint)
- [ ] Build succeeds (Vite)
- [ ] Migrations run successfully
- [ ] Config/route/view caches cleared
- [ ] Storage linked
- [ ] Telescope pruned
- [ ] Logs rotated

### 5. Rollback Strategy
```bash
# Keep last 5 releases
# On failure:
git checkout <previous-commit>
php artisan migrate:rollback --force
php artisan config:cache
php artisan optimize
```
