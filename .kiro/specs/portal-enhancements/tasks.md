# Implementation Plan: Portal Enhancements

## Overview

This plan implements six feature areas — Scouting UK purple branding, welcome emails, improved post creation UX, leader profiles, home contact schedule, and branded email templates — on top of the existing Laravel 13.x Parent Notifier Portal. Tasks are ordered in waves by dependency: database first, then models, then services, then controllers, then views, then routes.

## Tasks

- [ ] 1. Database migrations
  - [ ] 1.1 Create migration to add columns to `leaders` table
    - Add `role` (string, 100, nullable), `bio` (text, nullable), `phone_number` (string, 20, nullable), `profile_photo` (string, nullable) after `is_admin`
    - File: `database/migrations/xxxx_xx_xx_add_profile_fields_to_leaders_table.php`
    - _Requirements: 4.1_

  - [ ] 1.2 Create migration to add columns to `posts` table
    - Add `title` (string, 200, nullable) after `leader_id`
    - Add `is_pinned` (boolean, default false) after `longitude`
    - File: `database/migrations/xxxx_xx_xx_add_title_and_pinned_to_posts_table.php`
    - _Requirements: 3.2, 3.7_

  - [ ] 1.3 Create migration for `home_contacts` table
    - Columns: `id`, `leader_id` (nullable FK to leaders with nullOnDelete), `leader_name` (string 255), `date` (date, indexed), `phone_number` (string 20), `notes` (string 500, nullable), timestamps
    - File: `database/migrations/xxxx_xx_xx_create_home_contacts_table.php`
    - _Requirements: 5.1, 5.7_

- [ ] 2. Model updates and new model
  - [ ] 2.1 Update `Post` model
    - Add `title` and `is_pinned` to `$fillable`
    - Add `is_pinned` to `casts()` as boolean
    - File: `app/Models/Post.php`
    - _Requirements: 3.2, 3.7_

  - [ ] 2.2 Update `Leader` model
    - Add `role`, `bio`, `phone_number`, `profile_photo` to `$fillable`
    - Add `homeContacts()` HasMany relationship
    - File: `app/Models/Leader.php`
    - _Requirements: 4.1_

  - [ ] 2.3 Create `HomeContact` model
    - Define `$table = 'home_contacts'`, `$fillable`, date cast, `leader()` BelongsTo relationship
    - File: `app/Models/HomeContact.php`
    - _Requirements: 5.1_

- [ ] 3. Branded email template and Welcome Email mailable
  - [ ] 3.1 Create branded base email layout
    - HTML layout with inline styles, purple (#7413DC) header bar, Portal_Name, Logo (conditional), consistent footer
    - Must be compatible with Gmail, Outlook, Apple Mail, Yahoo Mail (table-based layout with inline styles)
    - File: `resources/views/emails/layouts/branded.blade.php`
    - _Requirements: 6.1, 6.2, 6.5, 6.6_

  - [ ] 3.2 Update existing `new-post` email to use branded layout
    - Modify `resources/views/emails/new-post.blade.php` to extend or include the branded layout
    - Ensure post content, leader name, and access token link are rendered within the branded template
    - _Requirements: 6.3_

  - [ ] 3.3 Create `WelcomeEmail` mailable class
    - Accept Guardian, portal name, feed URL, logo URL
    - Use branded layout, include welcome message, Portal_Name, and Access_Token link
    - File: `app/Mail/WelcomeEmail.php`
    - _Requirements: 2.1, 2.3, 6.4_

  - [ ] 3.4 Create welcome email Blade view
    - File: `resources/views/emails/welcome.blade.php`
    - Display welcome message, portal name, personal feed link, and guidance text
    - Use the branded layout from 3.1
    - _Requirements: 2.1, 6.4_

- [ ] 4. Service layer updates
  - [ ] 4.1 Create `EmailService`
    - Method `sendWelcomeEmail(Guardian $parent): void` — sends WelcomeEmail, catches Throwable, logs failure to notification_logs with type/status, does not block registration
    - Method `sendPostNotification(Post $post): NotificationResult` — delegates to existing NotificationService logic
    - File: `app/Services/EmailService.php`
    - _Requirements: 2.1, 2.6_

  - [ ] 4.2 Update `PostService::create()` to support new fields
    - Accept `title`, `is_pinned`, `send_notification` in data array
    - If `is_pinned` is true, set all other posts' `is_pinned` to false before creating
    - If `send_notification` is false, skip call to notification service
    - _Requirements: 3.6, 3.7, 3.8_

  - [ ] 4.3 Update `PostService::update()` to support title and pin
    - Accept `title` and `is_pinned` in data array
    - Enforce single-pin invariant on update as well
    - _Requirements: 3.7, 3.8, 3.10_

  - [ ] 4.4 Update `SignUpService::register()` to send welcome email
    - After creating the Guardian, call `EmailService::sendWelcomeEmail()`
    - Inject EmailService via constructor
    - _Requirements: 2.1_

  - [ ] 4.5 Update sign-up link token generation in toggle-signup flow
    - When sign-up is enabled, generate a new unique `signup_link_token` and store in settings table, replacing any previous token
    - Existing `ParentController::toggleSignup()` or a new method should handle this
    - _Requirements: 2.4, 2.5_

- [ ] 5. Checkpoint — Run migrations and verify models
  - Ensure all tests pass (`php artisan test`), ask the user if questions arise.
  - Run `php artisan migrate` to verify migrations apply cleanly

- [ ] 6. Controller updates and new controllers
  - [ ] 6.1 Create `HomeContactController` (Dashboard)
    - CRUD actions: `index`, `store`, `update`, `destroy`
    - Validation: leader_id exists, date after_or_equal:today (on create), phone_number max:20, notes max:500
    - Populate `leader_name` from the referenced Leader on creation
    - File: `app/Http/Controllers/HomeContactController.php`
    - _Requirements: 5.2, 5.6_

  - [ ] 6.2 Create `ContactPageController` (Feed-side)
    - `show(string $token)` — query today's HomeContact and next 7 days, ordered by date ascending
    - Pass the prominent notice text to the view
    - Handle "no contact scheduled today" case
    - File: `app/Http/Controllers/ContactPageController.php`
    - _Requirements: 5.3, 5.4, 5.5_

  - [ ] 6.3 Create `LeadersPageController` (Feed-side)
    - `show(string $token)` — load all Leaders with profile fields, pass to view
    - File: `app/Http/Controllers/LeadersPageController.php`
    - _Requirements: 4.5_

  - [ ] 6.4 Update `PostController` for new form fields
    - `store()`: validate `title` (nullable, string, max:200), `is_pinned` (boolean), `send_notification` (boolean); pass to PostService
    - `update()`: same validation additions; pass to PostService
    - `create()` and `edit()`: no logic change needed yet (view changes handle display)
    - _Requirements: 3.2, 3.4, 3.5, 3.6, 3.7_

  - [ ] 6.5 Update `FeedController::index()` for pinned post ordering
    - Query pinned post separately (first), then remaining posts in reverse chronological order
    - Pass both `$pinnedPost` and `$posts` to view
    - _Requirements: 3.8_

  - [ ] 6.6 Create or extend `SettingsController` for logo and portal name
    - `uploadLogo(Request)` — validate JPEG/PNG/WebP, max 2MB, store in public storage, save path to settings
    - `removeLogo()` — delete file and remove setting
    - `updatePortalName(Request)` — validate and save portal_name setting
    - `updateProfile(Request)` — validate and update Leader's role, bio, phone_number, profile_photo
    - File: `app/Http/Controllers/SettingsController.php`
    - _Requirements: 1.3, 1.5, 4.2, 4.3, 4.4_

  - [ ] 6.7 Update `ParentController::store()` to send welcome email
    - After manually adding a parent, call `EmailService::sendWelcomeEmail()`
    - _Requirements: 2.2_

- [ ] 7. View updates
  - [ ] 7.1 Update `layouts/app.blade.php` — purple branding for Dashboard
    - Purple header bar (#7413DC) with Portal_Name and Logo (if uploaded)
    - Apply purple colour palette and Scouts-inspired styling (rounded elements, clean typography)
    - _Requirements: 1.1, 1.2, 1.4, 1.6_

  - [ ] 7.2 Update `layouts/feed.blade.php` — purple branding for Feed
    - Same purple header bar styling as Dashboard
    - Add navigation links for Leaders page and Contact page
    - _Requirements: 1.1, 1.2, 1.4, 1.6, 4.5, 5.3_

  - [ ] 7.3 Update `dashboard/posts/create.blade.php` — two-column post form
    - Left column: Title field (optional, max 200), Content field, toggle for "Include map pin", toggle for "Attach photos", toggle for "Send email notification" (default checked), "Pin post" checkbox
    - Right column: Publishing notes and tips (photo sizes, map pin usage, notification behaviour)
    - _Requirements: 3.1, 3.2, 3.4, 3.5, 3.7, 3.9_

  - [ ] 7.4 Update `dashboard/posts/edit.blade.php` — two-column edit form
    - Same layout as create form with existing values pre-populated
    - Include pin toggle and title field
    - _Requirements: 3.10_

  - [ ] 7.5 Update `feed/index.blade.php` — display pinned post and titles
    - Show pinned post at top with visual indicator (e.g. pin icon, "Pinned" badge)
    - Display post Title prominently above content when set
    - _Requirements: 3.3, 3.8_

  - [ ] 7.6 Create `feed/leaders.blade.php` — Leaders page
    - Display all leaders with profile_photo (or default avatar), display_name, role, bio
    - Use feed layout with navigation
    - _Requirements: 4.5, 4.6_

  - [ ] 7.7 Create `feed/contact.blade.php` — Contact page
    - Prominent notice: "Please do not contact young people directly while they are abroad. Use the home contact number below."
    - Today's home contact (name, phone, role) highlighted
    - Upcoming 7-day schedule list
    - Handle no-contact-today state
    - _Requirements: 5.3, 5.4, 5.5_

  - [ ] 7.8 Create `dashboard/home-contacts/index.blade.php` — Home contact management
    - List existing schedule entries with edit/delete
    - Form to add new entry (select leader, date, phone_number, notes)
    - _Requirements: 5.2_

  - [ ] 7.9 Update `dashboard/settings.blade.php` — add logo, portal name, and profile fields
    - Section for logo upload with preview, portal name input
    - Section for leader profile: role, bio, phone_number, profile_photo upload
    - Display validation errors for file type/size
    - _Requirements: 1.3, 1.5, 4.2, 4.4_

- [ ] 8. Route registration
  - [ ] 8.1 Add new routes to `routes/web.php`
    - Feed routes (token auth middleware group): `/feed/{token}/leaders`, `/feed/{token}/contact`
    - Dashboard routes (auth middleware group): `home-contacts` resource (except show), settings routes for logo/portal-name/profile
    - _Requirements: 4.5, 5.2, 5.3_

- [ ] 9. Final verification checkpoint
  - Run `php artisan test` — all existing 44 tests plus any new tests should pass
  - Verify migrations run cleanly: `php artisan migrate:fresh`
  - Ensure all tests pass, ask the user if questions arise.

- [ ]* 10. Property-based tests (optional)
  - [ ]* 10.1 Write property test for pin invariant
    - **Property 1: Pin invariant and feed ordering**
    - Generate random sets of posts, apply pin operations, verify exactly one post is pinned and it appears first in feed query
    - **Validates: Requirements 3.7, 3.8**

  - [ ]* 10.2 Write property test for notification suppression
    - **Property 2: Notification suppression**
    - Generate random post data with `send_notification=false`, verify zero emails dispatched and post is persisted
    - **Validates: Requirements 3.6**

  - [ ]* 10.3 Write property test for contact page date filtering
    - **Property 3: Contact page date filtering**
    - Generate random HomeContact date sets, vary "today" reference date, verify only today + next 7 days returned in date order
    - **Validates: Requirements 5.3**

  - [ ]* 10.4 Write property test for home contact past date validation
    - **Property 4: Home contact date validation**
    - Generate random past dates, verify validation rejects them
    - **Validates: Requirements 5.6**

  - [ ]* 10.5 Write property test for post notification email rendering
    - **Property 5: Post notification email contains required data**
    - Generate random post content and parent tokens, verify rendered email contains leader name, content substring, access token URL
    - **Validates: Requirements 6.3**

  - [ ]* 10.6 Write property test for welcome email rendering
    - **Property 6: Welcome email contains required data**
    - Generate random parent records, verify rendered email contains portal name and access token URL
    - **Validates: Requirements 6.4**

## Task Dependency Graph

```json
{
  "waves": [
    {
      "name": "Wave 1: Database Migrations",
      "tasks": ["1.1", "1.2", "1.3"],
      "description": "Add columns to leaders and posts tables, create home_contacts table"
    },
    {
      "name": "Wave 2: Models",
      "tasks": ["2.1", "2.2", "2.3"],
      "description": "Update Post and Leader models, create HomeContact model",
      "dependsOn": ["Wave 1: Database Migrations"]
    },
    {
      "name": "Wave 3: Email Templates and Mailable",
      "tasks": ["3.1", "3.2", "3.3", "3.4"],
      "description": "Create branded email layout, update existing email, create WelcomeEmail mailable and view",
      "dependsOn": ["Wave 2: Models"]
    },
    {
      "name": "Wave 4: Services",
      "tasks": ["4.1", "4.2", "4.3", "4.4", "4.5"],
      "description": "Create EmailService, update PostService and SignUpService, update signup token flow",
      "dependsOn": ["Wave 3: Email Templates and Mailable"]
    },
    {
      "name": "Wave 5: Checkpoint",
      "tasks": ["5"],
      "description": "Run migrations and verify all existing tests pass",
      "dependsOn": ["Wave 4: Services"]
    },
    {
      "name": "Wave 6: Controllers",
      "tasks": ["6.1", "6.2", "6.3", "6.4", "6.5", "6.6", "6.7"],
      "description": "Create HomeContactController, ContactPageController, LeadersPageController, SettingsController; update PostController, FeedController, ParentController",
      "dependsOn": ["Wave 5: Checkpoint"]
    },
    {
      "name": "Wave 7: Views",
      "tasks": ["7.1", "7.2", "7.3", "7.4", "7.5", "7.6", "7.7", "7.8", "7.9"],
      "description": "Update layouts with purple branding, create post form, feed pages, home contact management, settings views",
      "dependsOn": ["Wave 6: Controllers"]
    },
    {
      "name": "Wave 8: Routes",
      "tasks": ["8.1"],
      "description": "Register all new routes in web.php",
      "dependsOn": ["Wave 7: Views"]
    },
    {
      "name": "Wave 9: Final Verification",
      "tasks": ["9"],
      "description": "Run full test suite, verify migrations run cleanly",
      "dependsOn": ["Wave 8: Routes"]
    },
    {
      "name": "Wave 10: Property Tests (Optional)",
      "tasks": ["10.1", "10.2", "10.3", "10.4", "10.5", "10.6"],
      "description": "Property-based tests for correctness properties",
      "dependsOn": ["Wave 9: Final Verification"]
    }
  ]
}
```

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP delivery
- All tasks modify the existing Laravel 13.x codebase in place — no new project scaffolding needed
- Wave order ensures no task depends on something not yet implemented
- The branded email layout (task 3.1) is a dependency for both welcome and notification emails
- The single-pin invariant is enforced in PostService (tasks 4.2, 4.3), not via database constraint
- Existing tests (44) must continue passing after each wave
