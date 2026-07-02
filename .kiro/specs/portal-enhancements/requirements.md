# Requirements Document

## Introduction

This document specifies enhancement requirements for the existing Parent Notifier Portal — a Laravel application used by Scout expedition leaders to post updates for parents. These enhancements add Scouting UK branding, welcome emails, improved post creation UX, leader profiles visible to parents, a home contact schedule, and on-brand email templates. All enhancements build upon the existing authentication, post CRUD, feed, sign-up, and notification infrastructure.

## Glossary

- **Portal**: The Laravel/PHP web application serving the parent-facing Feed and the leader Dashboard
- **Leader**: An authenticated adult volunteer who creates Posts, manages Parents, and administers the Portal
- **Parent**: A recipient who views the Feed via a unique Access_Token; does not have a traditional login account
- **Dashboard**: The leader-facing administrative interface for creating Posts and managing settings
- **Feed**: A reverse-chronological list of Posts visible to Parents via their Access_Token URL
- **Access_Token**: A unique URL slug granting a Parent read-only access to the Feed
- **Home_Contact**: A Leader designated as the point of contact for parents on a specific date during an expedition
- **Home_Contact_Schedule**: A table of date-to-Leader assignments showing which Leader is reachable on which day
- **Portal_Name**: A configurable name for the portal stored in the settings table
- **Logo**: An uploaded image file displayed in the portal header and email templates
- **Pinned_Post**: A Post marked to appear at the top of the Feed regardless of chronological order
- **Sign_Up_Link_Token**: A unique token in the settings table that forms the sign-up URL; regenerated each time sign-ups are enabled
- **Welcome_Email**: A branded email sent to a newly registered Parent containing their Access_Token link
- **Email_Template**: An HTML email layout with inline styles, purple branding, header logo, and footer
- **Leader_Profile**: Extended Leader information (role, bio, phone number, profile photo) visible to Parents
- **Leaders_Page**: A Feed-accessible page displaying all Leader profiles
- **Contact_Page**: A Feed-accessible page displaying the current Home_Contact and upcoming schedule

## Requirements

### Requirement 1: Scouting UK Purple Branding

**User Story:** As a Leader, I want the portal to use Scouting UK purple branding, so that the portal looks official and aligned with the Scouts identity.

#### Acceptance Criteria

1. THE Portal SHALL use #7413DC as the primary colour and #23005B as the dark purple colour across all pages including Dashboard, Feed, login, sign-up, and error pages
2. THE Portal SHALL display a purple header bar at the top of every page (Dashboard and Feed) containing the Portal_Name and Logo if one has been uploaded
3. WHEN an admin Leader uploads a Logo via the settings page, THE Portal SHALL store the Logo file in the public storage directory and display the Logo in the header bar on all pages
4. WHEN no Logo has been uploaded, THE Portal SHALL display only the Portal_Name text in the header bar without a placeholder image
5. IF an admin Leader uploads a Logo file that is not a JPEG, PNG, or WebP image or exceeds 2 MB, THEN THE Portal SHALL reject the upload and display an error message indicating the accepted file types and maximum size
6. THE Portal SHALL apply Scouts fleur-de-lis inspired styling (purple colour palette, clean typography, rounded elements) consistently across all pages

### Requirement 2: Welcome Email for New Parents

**User Story:** As a Parent, I want to receive a branded welcome email when I sign up, so that I have my access link and know what the portal is about.

#### Acceptance Criteria

1. WHEN a Parent registers via the sign-up form, THE Portal SHALL send a Welcome_Email to the Parent's email address containing their personal Access_Token link, the Portal_Name, and a welcome message
2. WHEN a Leader manually adds a Parent from the Dashboard, THE Portal SHALL send the same Welcome_Email to the newly added Parent's email address
3. THE Welcome_Email SHALL use the same branded Email_Template (purple header, Logo, footer) as the new post notification email
4. WHEN a Leader enables the Sign_Up_Link from the Dashboard, THE Portal SHALL generate a new unique Sign_Up_Link_Token and store it in the settings table, replacing any previously stored token
5. WHEN a Leader disables and then re-enables the Sign_Up_Link, THE Portal SHALL generate a fresh Sign_Up_Link_Token such that any previously shared sign-up URL becomes invalid
6. IF the Welcome_Email fails to send, THEN THE Portal SHALL log the failure with the Parent's email and error reason, and continue with the registration process without blocking it

### Requirement 3: Improved Post Creation UX

**User Story:** As a Leader, I want an improved post creation form with clear options and a title field, so that I can create well-structured posts efficiently.

#### Acceptance Criteria

1. WHEN a Leader navigates to the post creation page, THE Portal SHALL display a two-column layout with the post form on the left and publishing notes on the right
2. THE Portal SHALL provide an optional Title field (maximum 200 characters) on the post creation form, displayed above the content field
3. WHEN a Post has a Title set, THE Portal SHALL display the Title prominently above the post content in the Feed
4. THE Portal SHALL provide toggle controls on the post creation form for: "Include map pin", "Attach photos", and "Send email notification to parents"
5. THE Portal SHALL default the "Send email notification to parents" toggle to checked (enabled) on the post creation form
6. WHEN a Leader submits a Post with the email notification toggle unchecked, THE Portal SHALL save the Post without sending email notifications to Parents
7. THE Portal SHALL provide a "Pin post" option on the post creation and edit forms, allowing one Post to be pinned at a time
8. WHEN a Leader pins a Post, THE Portal SHALL display the Pinned_Post at the top of the Feed above all other Posts, and unpin any previously pinned Post
9. THE Portal SHALL display publishing notes and tips on the right column of the post creation form, including guidance on photo sizes, map pin usage, and notification behaviour
10. WHEN a Leader edits an existing Post, THE Portal SHALL display the same two-column layout with existing values pre-populated

### Requirement 4: Leader Profiles

**User Story:** As a Parent, I want to see information about the expedition leaders, so that I know who is looking after the young people.

#### Acceptance Criteria

1. THE Portal SHALL store the following additional fields for each Leader: role (maximum 100 characters), bio (maximum 1000 characters), phone_number (maximum 20 characters), and profile_photo (file path)
2. WHEN a Leader accesses the settings page, THE Portal SHALL display a profile section where the Leader can update their role, bio, phone_number, and profile_photo
3. WHEN a Leader uploads a profile_photo, THE Portal SHALL store the file in public storage and associate the path with the Leader record
4. IF a Leader uploads a profile_photo that is not a JPEG, PNG, or WebP image or exceeds 2 MB, THEN THE Portal SHALL reject the upload and display an error message indicating the accepted file types and maximum size
5. THE Portal SHALL provide a Leaders_Page accessible to Parents via the Feed navigation, displaying all Leaders with their profile_photo, display_name, role, and bio
6. WHEN a Leader has no profile_photo uploaded, THE Portal SHALL display a default avatar placeholder on the Leaders_Page

### Requirement 5: Home Contact Schedule

**User Story:** As a Parent, I want to know who the home contact is today and in the coming days, so that I can reach the right person if needed.

#### Acceptance Criteria

1. THE Portal SHALL store Home_Contact records with the following fields: id, leader_id (foreign key to leaders), date, phone_number (maximum 20 characters), and notes (maximum 500 characters)
2. WHEN an admin Leader accesses the home contact management page, THE Portal SHALL allow creating, editing, and deleting Home_Contact_Schedule entries
3. THE Portal SHALL provide a Contact_Page accessible to Parents via the Feed navigation, displaying today's Home_Contact (name, phone number, role) and the upcoming schedule for the next 7 days
4. THE Contact_Page SHALL display a prominent notice: "Please do not contact young people directly while they are abroad. Use the home contact number below."
5. WHEN no Home_Contact is scheduled for today, THE Contact_Page SHALL display a message indicating that no home contact is currently assigned and show the next scheduled contact if one exists
6. THE Portal SHALL validate that each Home_Contact entry references a valid Leader and a date that is not in the past when creating new entries
7. WHEN a Leader referenced by a Home_Contact entry is deleted, THE Portal SHALL retain the Home_Contact entry with the Leader's name preserved for historical reference

### Requirement 6: On-Brand Email Templates

**User Story:** As a Leader, I want all portal emails to be professionally branded, so that parents recognize communications as official and trustworthy.

#### Acceptance Criteria

1. THE Portal SHALL use a base Email_Template layout for all outgoing emails, featuring a purple (#7413DC) header bar, Portal_Name, Logo (if uploaded), and a consistent footer
2. THE Email_Template SHALL use HTML with inline styles compatible with major email clients (Gmail, Outlook, Apple Mail, Yahoo Mail)
3. WHEN the Portal sends a new post notification email, THE Portal SHALL render it using the branded Email_Template with the post content, Leader name, and Access_Token link
4. WHEN the Portal sends a Welcome_Email, THE Portal SHALL render it using the same branded Email_Template with the welcome message, Portal_Name, and Access_Token link
5. WHEN no Logo has been uploaded, THE Email_Template SHALL display only the Portal_Name text in the header without a broken image placeholder
6. THE Email_Template footer SHALL include the Portal_Name and a note indicating the email was sent from the expedition update portal

