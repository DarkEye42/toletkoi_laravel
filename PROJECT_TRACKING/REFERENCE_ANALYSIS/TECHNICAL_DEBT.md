# ToLetKoi Technical Debt — From Both Reference Projects

## Project A (Node.js) — Technical Debt

### Critical
1. **No ORM** — Raw SQL queries everywhere; no migration system
2. **No TypeScript** — Both backend and frontend are plain JavaScript
3. **No tests** — `package.json` scripts say `echo 'Tests placeholder'`
4. **No environment validation** — Server starts even with missing env vars
5. **Duplicated admin auth** — Both `auth.js` and `adminAuth.js` define same middleware

### High
6. **No foreign key cascades** — DELETE operations require manual cleanup
7. **JSON columns for images** — `images_json` duplicated in listings AND property_images
8. **No database connection pooling tuning** — `connectionLimit: 10` hardcoded
9. **In-memory cache lost on restart** — No Redis
10. **No pagination cursors** — Offset-based pagination slow for large datasets

### Medium
11. **Inconsistent error responses** — Some `{ message }`, others `{ success, error }`
12. **No error boundary on frontend** — Limited error handling in client components
13. **Missing npm scripts** — No `lint`, `format`, or `typecheck`
14. **Mixed `'use client'` and server components** — Suboptimal rendering
15. **No state management** — Only React Context

### Low
16. **No form library** — Manual state management in forms
17. **Incomplete dashboard pages** — Several routes may have placeholder content
18. **No WebSocket/real-time** — No Socket.io for messaging
19. **No API versioning** — No `/api/v1/` prefix
20. **No API documentation** — No Swagger/OpenAPI

## Project B (PHP) — Technical Debt

### Critical
1. **SQL Injection in `app/` layer** — Direct string interpolation
2. **MD5 passwords in mobile API** — No salt, insecure
3. **Hardcoded Barikoi API key** in source code
4. **No CORS restrictions** on API endpoints
5. **Mixed mysqli and PDO** — Inconsistent database access

### High
6. **No MVC, no routing** — Flat file architecture
7. **Two separate codebases** — `app/` vs root with different standards
8. **No Composer, no autoloading** — Manual includes everywhere
9. **Global variables everywhere** — `$con`, `$pdo`, `$user_result`
10. **No database indexing** — Beyond primary keys

### Medium
11. **No CSRF on some pages** — Inconsistent protection
12. **Test files committed** — test.php, test2.php, test3.php, test4.php
13. **error_log files committed** — Should be gitignored
14. **.env committed** — Contains secrets (blocked by .htaccess)
15. **No rate limiting on login** — Brute force vulnerable

### Low
16. **No caching layer** — Every request hits database
17. **No CDN** — Static assets served from origin
18. **Large SQL dump in repo** — Should be separate
19. **No lazy loading** — Images load all at once
20. **jQuery AJAX without debouncing** — Chat performance issues

## Laravel Project — Avoid These Issues

### Architecture (Avoid Project B's mistakes)
- ✅ Use Laravel MVC with proper routing
- ✅ Use Composer for dependency management
- ✅ Use Eloquent ORM (not raw SQL)
- ✅ Use Laravel migrations (not manual SQL)
- ✅ Use dependency injection (not globals)
- ✅ Use .env for configuration (not hardcoded)
- ✅ Use Laravel's built-in features

### Security (Avoid Both Projects' mistakes)
- ✅ Use Laravel's CSRF protection
- ✅ Use Eloquent parameterized queries
- ✅ Use bcrypt via Laravel Auth (not MD5)
- ✅ Use Laravel's CORS middleware
- ✅ Use Laravel's rate limiter
- ✅ Use Laravel's validation
- ✅ Use Laravel's authorization

### Testing (Avoid Both Projects' mistakes)
- ✅ Write PHPUnit tests from day one
- ✅ Test all API endpoints
- ✅ Test all models and relationships
- ✅ Test all middleware
- ✅ Test all authorization rules
- ✅ Test payment flows
- ✅ Test email/notification sending

### Code Quality (Avoid Both Projects' mistakes)
- ✅ Use consistent coding standards (PSR-12)
- ✅ Use PHPStan for static analysis
- ✅ Use Laravel Pint for formatting
- ✅ Use meaningful variable names
- ✅ Write documentation comments
- ✅ Use git properly (no committed secrets)

### Performance (Avoid Both Projects' mistakes)
- ✅ Use Redis for caching
- ✅ Use database indexes on foreign keys
- ✅ Use cursor-based pagination where needed
- ✅ Use queue jobs for heavy operations
- ✅ Use CDN for static assets
- ✅ Use lazy loading for images
- ✅ Use database connection pooling

### DevOps (Avoid Both Projects' mistakes)
- ✅ Use proper .gitignore
- ✅ Use CI/CD pipeline
- ✅ Use Docker for consistent environments
- ✅ Use environment-based configuration
- ✅ Use proper logging (not error_log)
- ✅ Use monitoring (Laravel Telescope)
- ✅ Use backups

## Prioritized Remediation Plan

### Phase 00-02: Foundation (Do It Right)
1. Set up Laravel with proper structure
2. Configure .env properly
3. Set up git with proper .gitignore
4. Configure Laravel Pint for code style
5. Set up PHPUnit

### Phase 01: Database (Do It Right)
1. Create all migrations with proper FKs
2. Add indexes on foreign keys
3. Use Eloquent models with relationships
4. Use soft deletes where needed
5. Seed reference data

### Phase 02: Backend (Do It Right)
1. Use Laravel controllers with proper authorization
2. Use Form Requests for validation
3. Use API Resources for responses
4. Use Laravel Sanctum for auth
5. Use rate limiting

### Phase 03-09: Features (Do It Right)
1. Write tests for every feature
2. Use queue jobs for heavy operations
3. Use events for decoupling
4. Use notifications for alerts
5. Use caching for performance
