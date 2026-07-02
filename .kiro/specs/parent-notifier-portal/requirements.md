# Requirements Document

## Introduction

The Parent Notifier Portal is a Laravel/PHP web application that allows Scout expedition leaders to post updates, photos, and map pins to a single shared feed. Parents access the portal via a unique token link and can see all posts. When leaders publish updates, parents are notified by email using Laravel's built-in mail system. Leaders manage parent sign-ups and control access. The application is designed to run on shared cPanel hosting without SSH access.

## Glossary

- **Portal**: The Laravel/PHP web application serving the parent-facing feed and the leader Dashboard
- **Leader**: An authenticated adult volunteer who creates Posts, manages Parents, and administers the Portal via email/password login
- **Parent**: A recipient who views the Feed via a unique access token; does not have a traditional login account
- **Post**: A feed entry created by a Leader containing text, optional photos, and an optional Map_Pin
- **Map_Pin**: A geographic coordinate (latitude/longitude) attached to a Post, displayed on an interactive map
- **Access_Token**: A unique URL slug that grants a Parent read-only access to the Feed
- **Young_Person**: A Scout whose parent receives updates through the Portal
- **Feed**: A reverse-chronological list of Posts visible to Parents
- **Sign_Up_Link**: A URL that Parents use to register their email and Young Person's name
- **Dashboard**: The leader-facing administrative interface for creating Posts and managing settings

## Requirements

### Requirement 1: Leader Authentication

**User Story:** As a Leader, I want to log in with email and password, so that I can securely access the Dashboard.

#### Acceptance Criteria

1. WHEN a Leader navigates to the login page, THE Portal SHALL display a login form with an email field (maximum 255 characters, validated as a well-formed email address) and a password field (maximum 128 characters)
2. WHEN a Leader submits valid credentials, THE Portal SHALL create a session that expires after 2 hours of inactivity and redirect the Leader to the Dashboard
3. IF a Leader submits invalid credentials, THEN THE Portal SHALL display a generic error message indicating that the email or password is incorrect without revealing which field is invalid, and return the Leader to the login form with the email field preserved
4. IF a Leader fails to authenticate 5 consecutive times for the same email address, THEN THE Portal SHALL reject further login attempts for that email for 15 minutes and display a message indicating the account is temporarily locked
5. WHEN a Leader clicks the logout button, THE Portal SHALL destroy the session and redirect to the login page
6. WHEN an unauthenticated user attempts to access a protected Dashboard page, THE Portal SHALL redirect the user to the login page and, upon successful authentication, redirect the user to the originally requested page

### Requirement 2: Leader Post Creation

**User Story:** As a Leader, I want to create posts with text, photos, and map pins, so that Parents can follow the expedition's progress.

#### Acceptance Criteria

1. WHEN a Leader submits a new Post from the Dashboard with text content between 1 and 5000 characters, THE Portal SHALL save the Post with the Leader's display name and a timestamp
2. WHEN a Leader attaches up to 10 photos to a Post, where each photo is a JPEG, PNG, or WebP file no larger than 10 MB, THE Portal SHALL upload and store the photos and associate them with the Post
3. WHEN a Leader adds a Map_Pin to a Post, THE Portal SHALL store the latitude and longitude coordinates with the Post
4. THE Portal SHALL display Posts in the Feed in reverse chronological order
5. WHEN a Leader edits an existing Post, THE Portal SHALL allow updates to the text content, photos, and Map_Pin, and retain the original creation timestamp
6. WHEN a Leader deletes a Post, THE Portal SHALL remove the Post and its associated photos from the Portal
7. IF a Leader submits a Post with text content that is empty or exceeds 5000 characters, THEN THE Portal SHALL reject the submission and display an error message indicating the text length requirement
8. IF a Leader attaches a photo that exceeds 10 MB or is not a JPEG, PNG, or WebP file, THEN THE Portal SHALL reject the photo and display an error message indicating the accepted file types and size limit

### Requirement 3: Parent Access via Token Link

**User Story:** As a Parent, I want to access the portal using a unique link, so that I can view expedition updates without creating an account.

#### Acceptance Criteria

1. WHEN a Parent navigates to a valid Access_Token URL, THE Portal SHALL display the Feed
2. WHEN a visitor navigates to an invalid or revoked Access_Token URL, THE Portal SHALL display a 403 Forbidden page with a message indicating that the link is invalid or access has been revoked
3. WHEN a visitor navigates to the Portal root without an Access_Token and without a Leader session, THE Portal SHALL display a landing page that does not reveal any Post content
4. THE Portal SHALL NOT require Parents to enter a password when accessing the Feed via a valid Access_Token
5. THE Portal SHALL generate a unique Access_Token of at least 32 URL-safe alphanumeric characters for each registered Parent, such that the token is not sequentially guessable

### Requirement 4: Parent Feed View

**User Story:** As a Parent, I want to view posts, photos, and map pins on a feed, so that I can follow the expedition in a familiar social-media-style layout.

#### Acceptance Criteria

1. WHEN a Parent views the Feed, THE Portal SHALL display each Post's text content, human-readable timestamp (date and time), and author name, ordered from newest to oldest
2. WHEN a Post contains photos, THE Portal SHALL display up to 10 photos per Post inline within the Post on the Feed, each rendered as a thumbnail that the Parent can tap or click to view at full size
3. WHEN a Post contains a Map_Pin, THE Portal SHALL display a map within the Post showing the pin location, supporting pan and zoom interactions
4. WHEN multiple Posts contain Map_Pins, THE Portal SHALL provide a combined map view accessible via a visible control on the Feed page, showing all pin locations with markers that the Parent can tap or click to identify the associated Post
5. THE Portal SHALL render the Feed usably for viewports from 320px to 1920px wide, reflowing content to fit the available width without requiring horizontal scrolling
6. WHEN the Feed contains more than 20 Posts, THE Portal SHALL paginate or progressively load Posts in groups of no more than 20, providing a control to load additional Posts

### Requirement 5: Email Notifications

**User Story:** As a Parent, I want to receive an email when a new update is posted, so that I know to check the portal for the latest information.

#### Acceptance Criteria

1. WHEN a Leader publishes a new Post, THE Portal SHALL send an email notification to all registered Parents with active (non-revoked) Access_Tokens using Laravel's mail system
2. THE Portal SHALL include the Parent's personal Access_Token link in each notification email so the Parent can navigate directly to the Feed
3. IF an email fails to send to a specific Parent, THEN THE Portal SHALL log the failure including the Parent's email address and the error reason, and continue sending to remaining Parents
4. THE Portal SHALL include the Post's full text content in the notification email body, truncated at 500 characters with an ellipsis if the text exceeds that length
5. THE Portal SHALL set the email subject line to include the Leader's display name to help Parents identify the notification source

### Requirement 6: Parent Sign-Up Management

**User Story:** As a Leader, I want to control parent sign-ups by enabling or disabling a sign-up link, so that I can manage who receives updates.

#### Acceptance Criteria

1. WHEN a Leader enables the Sign_Up_Link from the Dashboard, THE Portal SHALL make the sign-up page accessible to visitors
2. WHEN a Leader disables the Sign_Up_Link, THE Portal SHALL reject new sign-up submissions with a message indicating sign-ups are closed
3. WHEN a Parent accesses an active Sign_Up_Link, THE Portal SHALL display a form requesting the Young Person's name and the Parent's email address
4. WHEN a Parent submits the sign-up form with valid data, THE Portal SHALL register the Parent's email, associate the Young Person's name, and generate a unique Access_Token
5. IF a Parent submits the sign-up form with an email already registered, THEN THE Portal SHALL inform the Parent that the email is already registered
6. WHEN a Leader views the parent list on the Dashboard, THE Portal SHALL display each registered Parent's email, Young Person's name, and Access_Token link
7. WHEN a Leader removes a Parent from the list, THE Portal SHALL revoke the Parent's Access_Token and stop sending email notifications to that address
8. WHEN a Leader manually adds a Parent from the Dashboard, THE Portal SHALL create the record with the specified email and Young Person's name and generate an Access_Token

### Requirement 7: Leader Management

**User Story:** As a Leader, I want to manage other Leader accounts, so that multiple Leaders can collaborate on posting updates.

#### Acceptance Criteria

1. WHEN a Leader with admin privileges creates a new Leader account, THE Portal SHALL store the Leader record with email, password, and display name
2. WHEN an admin Leader removes another Leader, THE Portal SHALL revoke that Leader's access to the Dashboard
3. WHEN a Leader updates their own profile, THE Portal SHALL persist the display name changes
4. THE Portal SHALL designate at least one Leader as an admin who can manage other Leader accounts

### Requirement 8: Privacy and Error Handling

**User Story:** As a system administrator, I want the portal to handle errors gracefully and protect user privacy, so that the system is robust and trustworthy.

#### Acceptance Criteria

1. WHEN a visitor requests a non-existent page, THE Portal SHALL return a 404 Not Found page with a user-friendly message
2. WHEN a visitor requests a resource without proper authorization, THE Portal SHALL return a 403 Forbidden page without revealing protected content
3. THE Portal SHALL provide a publicly accessible privacy policy page describing data collection and usage
4. IF an unhandled application error occurs, THEN THE Portal SHALL log the error details server-side and display a generic error page to the visitor

### Requirement 9: Shared Hosting Compatibility

**User Story:** As a system administrator, I want the portal to run on shared cPanel hosting without SSH access, so that deployment and operation do not require dedicated server resources.

#### Acceptance Criteria

1. THE Portal SHALL send email notifications synchronously during the Post creation request without requiring a background queue worker
2. THE Portal SHALL function without SSH access by supporting deployment via FTP or cPanel File Manager
3. THE Portal SHALL store uploaded photos in the local filesystem within the application's storage directory
4. THE Portal SHALL use MySQL as the database backend compatible with cPanel-provisioned databases
