# Implementation Plan: Parent Notifier Portal

## Overview

This plan implements a Laravel application for Scout expedition leaders to post updates (text, photos, map pins) to a shared feed that parents access via unique token links. The application targets shared cPanel hosting with synchronous email, local file storage, and MySQL. Implementation uses Laravel with Blade templates, Tailwind CSS, and Leaflet.js for maps.

## Tasks

- [x] 1. Set up Laravel project structure and configuration
  - [x] 1.1 Initialize Laravel project and install dependencies
    - Create a fresh Laravel project in the workspace root
    - Install Laravel Breeze (Blade stack) for authentication scaffolding
    - Install Tailwind CSS via Breeze's scaffolding
    - Install `innmind/black-box` for property-based testing
    - Configure `.env` for local MySQL (XAMPP), mail (log driver for dev), and filesystem (local)
    - Set session lifetime to 120 minutes in `config/session.php`
    - _Requirements: 9.2, 9.4, 1.2_

  - [x] 1.2 Configure storage and filesystem
    - Set up `storage/app/public` directory for photo uploads
    - Create the storage symlink (`php artisan storage:link`)
    - Configure `config/filesystems.php` with a `photos` disk pointing to `storage/app/public/photos`
    - _Requirements: 9.3_

  - [x] 1.3 Configure mail for synchronous sending
    - Set `QUEUE_CONNECTION=sync` in `.env`
    - Configure SMTP settings in `.env` (use `log` driver for development)
    - Ensure `config/mail.php` uses sync transport
    - _Requirements: 9.1_

- [x] 2. Create database migrations and Eloquent models
  - [x] 2.1 Create Leaders migration and model
    - Create `leaders` migration with: id, email (unique), password, display_name, is_admin (boolean, default false), timestamps
    - Create `Leader` model with fillable fields, password casting, and `hasMany` relationship to Posts
    - Configure Laravel's auth guard to use the `Leader` model instead of default `User`
    - Remove the default `users` migration if present
    - _Requirements: 1.1, 7.1, 7.4_

  - [x] 2.2 Create Posts migration and model
    - Create `posts` migration with: id, leader_id (FK to leaders, cascade on delete), content (TEXT), latitude (DECIMAL 10,7 nullable), longitude (DECIMAL 10,7 nullable), timestamps
    - Create `Post` model with fillable fields, `belongsTo` Leader, `hasMany` Photos, `hasMany` NotificationLogs
    - _Requirements: 2.1, 2.3_

  - [x] 2.3 Create Photos migration and model
    - Create `photos` migration with: id, post_id (FK to posts, cascade on delete), filename, path, sort_order (integer), created_at timestamp
    - Create `Photo` model with fillable fields and `belongsTo` Post relationship
    - _Requirements: 2.2_

  - [x] 2.4 Create Parents migration and model
    - Create `parents` migration with: id, email (unique), young_person_name, access_token (unique, string 64), is_revoked (boolean, default false), timestamps
    - Create `Guardian` model (avoiding PHP reserved word) mapped to `parents` table, with `hasMany` NotificationLogs
    - _Requirements: 3.5, 6.4_

  - [x] 2.5 Create NotificationLogs migration and model
    - Create `notification_logs` migration with: id, post_id (FK cascade), parent_id (FK set null on delete, nullable), status (enum: sent, failed), error_message (text nullable), created_at
    - Create `NotificationLog` model with `belongsTo` Post and `belongsTo` Guardian
    - _Requirements: 5.3_

  - [x] 2.6 Create Settings migration and seeder
    - Create `settings` migration with: id, key (unique string), value (text), updated_at
    - Create `Setting` model
    - Create a seeder that inserts default settings: `signup_enabled` = "0", `portal_name` = "Expedition Updates"
    - _Requirements: 6.1_

- [x] 3. Checkpoint - Verify migrations and models
  - Ensure all migrations run successfully (`php artisan migrate:fresh --seed`), ask the user if questions arise.

- [x] 4. Implement authentication system
  - [x] 4.1 Customize Laravel Breeze for Leader login
    - Modify Breeze's authentication to use the `Leader` model and `leaders` table
    - Customize the login view (`auth/login.blade.php`) with email (max 255 chars) and password (max 128 chars) fields
    - Ensure session expires after 2 hours of inactivity
    - Remove registration routes from Breeze (leaders are managed internally)
    - Remove password reset routes (not required)
    - _Requirements: 1.1, 1.2, 1.5, 1.6_

  - [x] 4.2 Implement login rate limiting
    - Configure rate limiting on the login POST route: 5 attempts per 15 minutes per email
    - Display a "temporarily locked" message when rate limit is exceeded
    - Ensure generic error message on invalid credentials (does not reveal which field is wrong)
    - Preserve email field value on failed login attempts
    - _Requirements: 1.3, 1.4_

  - [ ]* 4.3 Write feature tests for authentication
    - Test successful login creates session and redirects to dashboard
    - Test invalid credentials show generic error with email preserved
    - Test rate limiting activates after 5 failed attempts
    - Test logout destroys session
    - Test unauthenticated access redirects to login then back to intended page
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6_

- [x] 5. Implement core services
  - [x] 5.1 Implement TokenService
    - Create `App\Services\TokenService` with `generate()`, `validate(string $token)`, and `revoke(Guardian $parent)` methods
    - `generate()` produces 32+ character URL-safe alphanumeric tokens using `Str::random(64)`
    - `validate()` returns the Guardian model if token is active (exists and not revoked), or null otherwise
    - `revoke()` sets `is_revoked = true` on the parent record
    - _Requirements: 3.1, 3.2, 3.5, 6.7_

  - [ ]* 5.2 Write property tests for TokenService
    - **Property 1: Token generation invariants**
    - **Validates: Requirements 3.5**
    - **Property 2: Token access control**
    - **Validates: Requirements 3.1, 3.2**

  - [x] 5.3 Implement PhotoService
    - Create `App\Services\PhotoService` with `upload(array $files, Post $post)` and `delete(Photo $photo)` methods
    - Validate each file: max 10 MB, JPEG/PNG/WebP only
    - Store files to `photos` disk with unique filenames
    - Create Photo model records with sort_order
    - `delete()` removes filesystem file and database record
    - _Requirements: 2.2, 2.8_

  - [x] 5.4 Implement NotificationService
    - Create `App\Services\NotificationService` with `notifyAllParents(Post $post)` method
    - Query all parents where `is_revoked = false`
    - Loop through each parent and send email synchronously via `Mail::to()`
    - Wrap each send in try/catch: log success/failure to `notification_logs` table
    - Return a result object with counts of sent and failed
    - _Requirements: 5.1, 5.3, 9.1_

  - [x] 5.5 Implement PostService
    - Create `App\Services\PostService` with `create()`, `update()`, `delete()` methods
    - `create()`: validates content (1-5000 chars), stores post, calls PhotoService for uploads, calls NotificationService
    - `update()`: updates content/coordinates, handles photo additions/removals, preserves `created_at`
    - `delete()`: removes all associated photos from filesystem, deletes post (cascades to photos and notification_logs)
    - _Requirements: 2.1, 2.3, 2.5, 2.6_

  - [ ]* 5.6 Write property tests for post validation and data integrity
    - **Property 3: Post content validation**
    - **Validates: Requirements 2.7**
    - **Property 4: Post creation preserves data**
    - **Validates: Requirements 2.1, 2.3**
    - **Property 5: Edit preserves creation timestamp**
    - **Validates: Requirements 2.5**
    - **Property 6: Delete cascades to photos**
    - **Validates: Requirements 2.6**

  - [x] 5.7 Implement SignUpService
    - Create `App\Services\SignUpService` with `register(string $email, string $youngPersonName)` method
    - Check for duplicate email → return error if exists
    - Generate unique access token via TokenService
    - Create Guardian record
    - _Requirements: 6.4, 6.5, 6.8_

- [x] 6. Checkpoint - Verify services
  - Ensure all tests pass, ask the user if questions arise.

- [x] 7. Implement Post CRUD (Leader Dashboard)
  - [x] 7.1 Create PostController with CRUD actions
    - Create `App\Http\Controllers\PostController` with index, create, store, edit, update, destroy methods
    - Apply `auth` middleware to all routes
    - `store`: validate content (required, max:5000), photos (max 10 files, each max 10MB, mimes:jpeg,png,webp), latitude/longitude (nullable, numeric)
    - `store`: use PostService to create post (which triggers email notifications)
    - `update`: use PostService to update post
    - `destroy`: use PostService to delete post
    - _Requirements: 2.1, 2.2, 2.3, 2.5, 2.6, 2.7, 2.8_

  - [x] 7.2 Create post Blade views
    - Create `dashboard/posts/index.blade.php`: list all posts with edit/delete actions
    - Create `dashboard/posts/create.blade.php`: form with text area, multi-file upload, map pin picker (Leaflet.js click-to-place)
    - Create `dashboard/posts/edit.blade.php`: pre-populated form, existing photos shown with remove option, map pin editable
    - Include Leaflet.js map widget for pin selection (click on map to set lat/lng)
    - _Requirements: 2.1, 2.2, 2.3, 2.5_

  - [ ]* 7.3 Write feature tests for Post CRUD
    - Test creating post with valid content stores correctly
    - Test creating post with photos uploads and associates files
    - Test creating post with map pin stores coordinates
    - Test editing post preserves created_at timestamp
    - Test deleting post removes photos from filesystem
    - Test validation rejects empty content, content >5000 chars
    - Test validation rejects invalid photo types and oversized files
    - _Requirements: 2.1, 2.2, 2.3, 2.5, 2.6, 2.7, 2.8_

- [x] 8. Implement Parent Feed view
  - [x] 8.1 Create TokenAuthMiddleware
    - Create `App\Http\Middleware\TokenAuthMiddleware`
    - Extract token from URL route parameter
    - Validate via TokenService: if valid, share parent data with view and proceed; if invalid/revoked, abort 403
    - Register middleware in `app/Http/Kernel.php`
    - _Requirements: 3.1, 3.2, 3.4_

  - [x] 8.2 Create FeedController
    - Create `App\Http\Controllers\FeedController` with `index` and `map` methods
    - `index`: paginate posts (20 per page) in descending created_at order, load photos and map pin data
    - `map`: load all posts with map pins for combined map view
    - Apply `TokenAuthMiddleware` to all feed routes
    - _Requirements: 4.1, 4.4, 4.6_

  - [x] 8.3 Create feed Blade views
    - Create `layouts/feed.blade.php`: responsive layout with Tailwind CSS, viewport 320px–1920px
    - Create `feed/index.blade.php`: render posts with text, timestamp, author name, inline photo thumbnails, and Leaflet map per post (if pin exists)
    - Create `feed/_post.blade.php`: partial for individual post rendering
    - Create `feed/map.blade.php`: full combined map with all pins, clickable markers linking to associated posts
    - Include Leaflet.js + OpenStreetMap tiles (no API key needed)
    - Implement photo lightbox (click thumbnail to view full size)
    - Add pagination controls (load more / page links)
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

  - [ ]* 8.4 Write feature tests for Feed access and display
    - Test valid token grants access to feed
    - Test invalid token returns 403
    - Test revoked token returns 403
    - Test feed displays posts in reverse chronological order
    - Test pagination limits to 20 posts per page
    - **Property 7: Feed ordering and pagination**
    - **Validates: Requirements 2.4, 4.1, 4.6**
    - _Requirements: 3.1, 3.2, 4.1, 4.6_

- [x] 9. Implement Email notifications
  - [x] 9.1 Create email Mailable and template
    - Create `App\Mail\NewPostNotification` Mailable class
    - Set subject to include leader's display name (e.g., "New update from {leader_name}")
    - Create `emails/new-post.blade.php` template with: post text (truncated at 500 chars with "..." if longer), personal token link, portal name
    - _Requirements: 5.2, 5.4, 5.5_

  - [ ]* 9.2 Write property tests for email notifications
    - **Property 8: Email recipient filtering**
    - **Validates: Requirements 5.1, 6.7**
    - **Property 9: Email content correctness**
    - **Validates: Requirements 5.2, 5.4, 5.5**
    - **Property 10: Email resilience**
    - **Validates: Requirements 5.3**

- [x] 10. Implement Parent Sign-Up management
  - [x] 10.1 Create SignUpController
    - Create `App\Http\Controllers\SignUpController` with `show` and `store` methods
    - `show`: check if `signup_enabled` setting is "1"; if not, display "sign-ups closed" message
    - `store`: validate email (required, valid email, max 255) and young_person_name (required, max 255); use SignUpService to register
    - Display success message with instructions after registration
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_

  - [x] 10.2 Create ParentController for leader management
    - Create `App\Http\Controllers\ParentController` with index, store, destroy methods
    - `index`: list all parents with email, young person's name, access token link, and revoked status
    - `store`: manually add a parent (email + young person's name), generate token via SignUpService
    - `destroy`: revoke parent access via TokenService (sets `is_revoked = true`)
    - Apply `auth` middleware to all routes
    - _Requirements: 6.6, 6.7, 6.8_

  - [x] 10.3 Create sign-up and parent management Blade views
    - Create `signup/form.blade.php`: public form with young person's name and parent email fields
    - Create `dashboard/parents/index.blade.php`: table of parents with add form and remove buttons
    - Include toggle control to enable/disable sign-up link
    - _Requirements: 6.1, 6.2, 6.3, 6.6_

  - [ ]* 10.4 Write feature tests for parent sign-up and management
    - Test sign-up form accessible when enabled
    - Test sign-up form rejected when disabled
    - Test successful registration creates parent with token
    - Test duplicate email is rejected
    - Test leader can add parent manually
    - Test leader can revoke parent access
    - **Property 11: Parent registration produces unique token**
    - **Validates: Requirements 6.4, 6.8**
    - **Property 12: Duplicate email rejection**
    - **Validates: Requirements 6.5**
    - **Property 13: Token revocation blocks access**
    - **Validates: Requirements 6.7**
    - _Requirements: 6.1, 6.2, 6.4, 6.5, 6.7, 6.8_

- [x] 11. Implement Leader management
  - [x] 11.1 Create LeaderController
    - Create `App\Http\Controllers\LeaderController` with index, store, destroy, and updateProfile methods
    - `index`: list all leaders (admin only)
    - `store`: create new leader with email, password, display_name (admin only)
    - `destroy`: remove leader (admin only, cannot remove self)
    - `updateProfile`: allow any leader to update their own display_name
    - Apply `auth` middleware; add admin authorization check for index/store/destroy
    - _Requirements: 7.1, 7.2, 7.3, 7.4_

  - [x] 11.2 Create leader management Blade views
    - Create `dashboard/leaders/index.blade.php`: list leaders with add form and remove buttons (admin only)
    - Create profile edit section in `dashboard/settings.blade.php` for display name updates
    - _Requirements: 7.1, 7.2, 7.3_

  - [ ]* 11.3 Write feature tests for leader management
    - Test admin can create new leader
    - Test admin can remove another leader
    - Test non-admin cannot manage leaders
    - Test leader can update own display name
    - Test at least one admin must remain
    - _Requirements: 7.1, 7.2, 7.3, 7.4_

- [x] 12. Implement static pages and error handling
  - [x] 12.1 Create landing page and privacy policy
    - Create `App\Http\Controllers\PageController` with `landing` and `privacy` methods
    - Create `pages/landing.blade.php`: public landing page without post content
    - Create `pages/privacy.blade.php`: privacy policy page describing data collection and usage
    - Set landing page as the root route (`/`) for unauthenticated visitors
    - _Requirements: 3.3, 8.3_

  - [x] 12.2 Create custom error pages
    - Create `errors/403.blade.php`: "Access denied. This link may be invalid or has been revoked."
    - Create `errors/404.blade.php`: "Page not found. Please check the URL."
    - Create `errors/500.blade.php`: "Something went wrong. Please try again later."
    - Ensure Laravel's exception handler logs full error details server-side
    - Ensure error pages do not reveal protected content or stack traces
    - _Requirements: 8.1, 8.2, 8.4_

  - [ ]* 12.3 Write feature tests for error pages and static pages
    - Test 404 page renders for non-existent routes
    - Test 403 page renders for unauthorized access
    - Test landing page is accessible without auth and shows no post content
    - Test privacy policy page is publicly accessible
    - _Requirements: 3.3, 8.1, 8.2, 8.3_

- [x] 13. Wire routes and finalize dashboard
  - [x] 13.1 Define all application routes
    - Define public routes: landing (`/`), privacy policy, sign-up, feed (`/feed/{token}`)
    - Define authenticated leader routes: dashboard home, posts CRUD, parents management, leaders management, settings
    - Define feed combined map route: `/feed/{token}/map`
    - Group dashboard routes under `/dashboard` prefix with `auth` middleware
    - Apply `throttle:login` to login POST route
    - _Requirements: 1.6, 3.1, 3.3, 9.2_

  - [x] 13.2 Create Dashboard home view
    - Create `App\Http\Controllers\DashboardController` with `index` and `settings` methods
    - Create `dashboard/index.blade.php`: overview with quick links to posts, parents, leaders, settings
    - Create `layouts/app.blade.php`: dashboard layout with navigation (posts, parents, leaders, settings, logout)
    - Include Tailwind CSS styling consistent with feed layout
    - _Requirements: 1.2, 6.1_

- [x] 14. Create database seeder for initial admin leader
  - [x] 14.1 Create AdminLeaderSeeder
    - Create a seeder that creates the first admin leader account with a configurable email/password (from `.env` or defaults)
    - Include this seeder in `DatabaseSeeder.php` along with the settings seeder
    - _Requirements: 7.4_

- [x] 15. Final checkpoint - Full integration verification
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties from the design document
- Unit/feature tests validate specific examples and edge cases
- The `Guardian` model name is used to avoid conflict with PHP's `parent` keyword
- Leaflet.js is loaded from CDN (no npm build required for production)
- All emails are sent synchronously during the HTTP request — no queue workers needed

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1"] },
    { "id": 1, "tasks": ["1.2", "1.3"] },
    { "id": 2, "tasks": ["2.1", "2.2", "2.3", "2.4", "2.5", "2.6"] },
    { "id": 3, "tasks": ["4.1", "5.1", "5.3"] },
    { "id": 4, "tasks": ["4.2", "5.2", "5.4", "5.7"] },
    { "id": 5, "tasks": ["4.3", "5.5"] },
    { "id": 6, "tasks": ["5.6", "7.1", "8.1", "9.1", "10.1"] },
    { "id": 7, "tasks": ["7.2", "8.2", "10.2", "10.3", "11.1"] },
    { "id": 8, "tasks": ["7.3", "8.3", "9.2", "10.4", "11.2"] },
    { "id": 9, "tasks": ["8.4", "11.3", "12.1", "12.2"] },
    { "id": 10, "tasks": ["12.3", "13.1", "13.2", "14.1"] }
  ]
}
```
