# Requirements Document

## Introduction

This document specifies seven enhancements to the existing Parent Notifier Portal. The enhancements bring Scouting UK purple branding across all UI and email elements, introduce a welcome email for new parents, add rotating token-based sign-up links, improve the post creation experience with a rich text editor and title field, expose leader profiles to parents, provide a home contact schedule for parents, and establish a shared branded email template used by all outgoing emails.

## Glossary

- **Portal**: The existing Laravel/PHP Parent Notifier web application
- **Leader**: An authenticated adult volunteer who administers the Portal via the Dashboard
- **Parent**: A recipient who views the Feed via a unique Access_Token URL
- **Dashboard**: The leader-facing administrative interface
- **Feed**: The parent-facing reverse-chronological list of Posts
- **Access_Token**: A unique URL slug that grants a Parent read-only access to the Feed
- **Branded_Email_Template**: The shared Blade layout (`emails/layouts/branded.blade.php`) used by all outgoing emails, containing the purple header, logo, portal name, content area, and footer
- **Sign_Up_Token**: A unique URL-safe string that forms part of the sign-up URL, regenerated each time sign-ups are enabled
- **Trix_Editor**: The Basecamp Trix rich text editor integrated via Laravel for composing Post content
- **Home_Contact**: A scheduled leader designated as the point of contact for parents on a specific date
- **Portal_Logo**: An image uploaded by a Leader via Dashboard settings, displayed in the header and email template
- **Portal_Subtitle**: A configurable text string displayed beneath the portal name in the header
- **Primary_Purple**: The hex colour #7413DC used as the primary brand colour
- **Dark_Purple**: The hex colour #490499 used for hover states and darker UI accents
- **Young_Person**: A Scout whose parent receives updates through the Portal

## Requirements

### Requirement 1: Scouting UK Purple Branding

**User Story:** As a Leader, I want the portal to use Scouting UK purple branding, so that the portal visually aligns with the Scouting organisation identity.

#### Acceptance Criteria

1. THE Portal SHALL use Primary_Purple (#7413DC) as the primary colour for all headers, navigation bars, primary buttons, and active UI elements across the Dashboard and Feed
2. THE Portal SHALL use Dark_Purple (#490499) for hover states on primary buttons and as a secondary accent colour in navigation and UI elements
3. THE Portal SHALL replace all existing indigo/blue themed elements (including the previous #4a90d9 email colour) with the Primary_Purple and Dark_Purple colour scheme
4. WHEN a Leader uploads a Portal_Logo via the Dashboard settings page, THE Portal SHALL store the image file and display the Portal_Logo in the header area of all pages and within the Branded_Email_Template
5. IF a Leader uploads a Portal_Logo that is not a JPEG, PNG, or WebP file, or exceeds 2 MB in size, THEN THE Portal SHALL reject the upload and display an error message indicating the accepted file types and maximum size
6. WHEN no Portal_Logo has been uploaded, THE Portal SHALL display the header without a logo image, showing only the portal name text
7. WHEN a Leader sets a Portal_Subtitle via the Dashboard settings page, THE Portal SHALL display the subtitle text beneath the portal name in the header of all pages
8. WHEN no Portal_Subtitle has been configured, THE Portal SHALL display the header with only the portal name and no subtitle text

### Requirement 2: Welcome Email for New Parents

**User Story:** As a Parent, I want to receive a welcome email when I am added to the portal, so that I have my personal access link and understand how to use the portal.

#### Acceptance Criteria

1. WHEN a Parent is registered via the sign-up form, THE Portal SHALL send a welcome email to the Parent's email address using the Branded_Email_Template
2. WHEN a Leader manually adds a Parent from the Dashboard, THE Portal SHALL send a welcome email to the Parent's email address using the Branded_Email_Template
3. THE Portal SHALL include the Parent's personal Access_Token URL in the welcome email so the Parent can navigate directly to the Feed
4. THE Portal SHALL include the portal name in the welcome email body
5. THE Portal SHALL include brief instructions in the welcome email explaining that the Parent can use the provided link to view expedition updates
6. IF the welcome email fails to send, THEN THE Portal SHALL log the failure with the Parent's email address and the error reason, and complete the Parent registration without interruption
7. THE Portal SHALL set the welcome email subject line to include the portal name

### Requirement 3: Sign-Up Link Toggle with Rotating Token

**User Story:** As a Leader, I want the sign-up link to use a unique token that rotates each time sign-ups are enabled, so that old links become invalid and I can control access to the sign-up page.

#### Acceptance Criteria

1. WHEN a Leader enables sign-ups from the Dashboard, THE Portal SHALL generate a new Sign_Up_Token of at least 32 URL-safe alphanumeric characters and store it in the settings
2. WHEN sign-ups are enabled, THE Portal SHALL make the sign-up page accessible exclusively at the URL path `/signup/{Sign_Up_Token}` where `{Sign_Up_Token}` is the currently active token
3. WHEN a visitor navigates to `/signup/{token}` with a token that does not match the current active Sign_Up_Token, THE Portal SHALL display a message indicating that the sign-up link is invalid or expired
4. WHEN a Leader disables sign-ups from the Dashboard, THE Portal SHALL reject all sign-up page requests regardless of the token provided, displaying a message indicating sign-ups are closed
5. WHEN a Leader re-enables sign-ups after previously disabling them, THE Portal SHALL generate a new Sign_Up_Token, invalidating the previous token
6. WHILE sign-ups are enabled, THE Portal SHALL display the current full sign-up URL (including the Sign_Up_Token) on the parent management page of the Dashboard so Leaders can copy and share the link
7. WHILE sign-ups are disabled, THE Portal SHALL display a message on the parent management page indicating that sign-ups are closed, without showing a sign-up URL

### Requirement 4: Improved Post Creation UX

**User Story:** As a Leader, I want a richer post creation experience with a title field, rich text editor, and notification control, so that I can create well-formatted posts and control when parents are emailed.

#### Acceptance Criteria

1. WHEN a Leader creates or edits a Post, THE Portal SHALL display an optional title field (maximum 255 characters) above the content area
2. WHEN a Leader saves a Post with a title, THE Portal SHALL store the title and display it prominently above the post content in the Feed
3. WHEN a Leader saves a Post without a title, THE Portal SHALL store and display the Post without a title heading
4. THE Portal SHALL replace the plain textarea for post content with the Trix_Editor providing a formatting toolbar with bold, italic, links, bullet lists, and numbered lists
5. THE Portal SHALL store post content as HTML produced by the Trix_Editor
6. THE Portal SHALL render post HTML content safely in the Feed, allowing only the formatting tags produced by the Trix_Editor (bold, italic, links, lists, headings, block quotes)
7. WHEN a Leader creates a Post, THE Portal SHALL display a "Send email notification" toggle that defaults to ON
8. WHEN a Leader submits a Post with the "Send email notification" toggle set to ON, THE Portal SHALL send email notifications to all active Parents
9. WHEN a Leader submits a Post with the "Send email notification" toggle set to OFF, THE Portal SHALL save the Post without sending email notifications
10. THE Portal SHALL display the photo upload section, map pin picker section, and content editor as distinct labelled sections on the post creation form
11. THE Portal SHALL display a publishing notes sidebar on the post creation page explaining the email notification toggle, photo upload limits, and map pin functionality
12. IF a Leader submits a Post with a title that exceeds 255 characters, THEN THE Portal SHALL reject the submission and display an error message indicating the maximum title length

### Requirement 5: Leader Profiles (Public-Facing)

**User Story:** As a Parent, I want to see profiles of the expedition leaders, so that I know who is looking after my young person.

#### Acceptance Criteria

1. WHEN a Leader accesses the profile editing section of the Dashboard, THE Portal SHALL display editable fields for role, bio text (maximum 1000 characters), phone number, and a profile photo upload
2. WHEN a Leader saves profile changes, THE Portal SHALL persist the updated role, bio, phone number, and profile photo
3. IF a Leader uploads a profile photo that is not a JPEG, PNG, or WebP file, or exceeds 2 MB in size, THEN THE Portal SHALL reject the upload and display an error message indicating the accepted file types and maximum size
4. THE Portal SHALL provide a "Meet the Leaders" page accessible to Parents via a token-authenticated URL from the Feed
5. WHEN a Parent navigates to the "Meet the Leaders" page, THE Portal SHALL display each Leader's profile photo (or a placeholder if none uploaded), display name, role, and bio text
6. THE Portal SHALL display a visible link or button to the "Meet the Leaders" page on the Feed page accessible to authenticated Parents
7. WHEN a Leader has not set a role or bio, THE Portal SHALL display the Leader's name on the "Meet the Leaders" page with empty role and bio sections

### Requirement 6: Home Contact Schedule

**User Story:** As a Parent, I want to see which leader is the home contact for today, so that I know who to call in case of an emergency.

#### Acceptance Criteria

1. WHEN a Leader accesses the home contact management section of the Dashboard, THE Portal SHALL display a schedule interface for assigning Leaders as Home_Contact on specific dates
2. WHEN a Leader creates a Home_Contact entry, THE Portal SHALL store the assigned Leader, date, phone number, and optional notes
3. WHEN a Leader edits an existing Home_Contact entry, THE Portal SHALL update the stored Leader assignment, date, phone number, and notes
4. WHEN a Leader deletes a Home_Contact entry, THE Portal SHALL remove the entry from the schedule
5. THE Portal SHALL provide a "Contact" page accessible to Parents via a token-authenticated URL from the Feed
6. WHEN a Parent navigates to the "Contact" page, THE Portal SHALL display today's Home_Contact (if one is assigned) including the Leader's display name, phone number, and any notes
7. WHEN a Parent navigates to the "Contact" page and no Home_Contact is assigned for today, THE Portal SHALL display a message indicating that no home contact is scheduled for today
8. WHEN a Parent navigates to the "Contact" page, THE Portal SHALL display upcoming Home_Contact entries for the next 7 days after today
9. THE Portal SHALL display a prominent safety reminder on the "Contact" page instructing Parents not to contact young people directly
10. THE Portal SHALL display a visible link or button to the "Contact" page on the Feed page accessible to authenticated Parents
11. IF a Leader attempts to create a Home_Contact entry for a date that already has an assigned contact, THEN THE Portal SHALL reject the submission and display an error indicating that a contact is already assigned for that date

### Requirement 7: Branded Email Template

**User Story:** As a Leader, I want all outgoing emails to use a consistent branded template, so that communications look professional and aligned with the portal's identity.

#### Acceptance Criteria

1. THE Portal SHALL provide a shared Blade email layout at `emails/layouts/branded.blade.php` used by all outgoing emails
2. THE Branded_Email_Template SHALL include a purple (#7413DC) header section containing the Portal_Logo (if uploaded) and the portal name
3. THE Branded_Email_Template SHALL include a white content area section where email-specific body content is rendered
4. THE Branded_Email_Template SHALL include a purple (#7413DC) footer section containing a link to the portal's privacy policy page
5. WHEN a Portal_Logo has been uploaded, THE Branded_Email_Template SHALL display the logo image in the header section
6. WHEN no Portal_Logo has been uploaded, THE Branded_Email_Template SHALL display only the portal name text in the header section
7. THE Portal SHALL use the Branded_Email_Template for the new post notification email
8. THE Portal SHALL use the Branded_Email_Template for the welcome email
