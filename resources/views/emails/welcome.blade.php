<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $portalName }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 0; background-color: #f5f5f5;">
    <!-- Header -->
    <div style="background-color: {{ $brandColor }}; padding: 30px 20px; text-align: center;">
        <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;">{{ $portalName }}</h1>
    </div>

    <!-- Body -->
    <div style="background-color: #ffffff; padding: 30px 25px;">
        <h2 style="color: #1a1a2e; margin-top: 0; margin-bottom: 10px; font-size: 20px;">Welcome!</h2>

        <p style="color: #4a4a4a; margin-bottom: 15px;">
            You've been registered to receive Jamboree updates for <strong>{{ $parent->young_person_name }}</strong>.
        </p>

        <p style="color: #4a4a4a; margin-bottom: 20px;">
            You can view the latest posts, photos, and map updates from the leaders using the link below. You'll also receive email notifications when new updates are posted.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $feedUrl }}"
               style="display: inline-block; background-color: {{ $brandColor }}; color: #ffffff; text-decoration: none; padding: 14px 28px; font-weight: 600; font-size: 16px;">
                View the Feed
            </a>
        </div>

        <p style="color: #718096; font-size: 13px; margin-top: 25px;">
            Bookmark this link for easy access. Your personal link is:
        </p>
        <p style="background-color: #f7fafc; padding: 10px 14px; font-size: 12px; word-break: break-all; color: #4a5568; border: 1px solid #e2e8f0;">
            {{ $feedUrl }}
        </p>
    </div>

    <!-- Footer -->
    <div style="padding: 20px 25px; text-align: center; border-top: 1px solid #e2e8f0;">
        <p style="font-size: 12px; color: #a0aec0; margin: 0;">
            You are receiving this email because you were registered on {{ $portalName }}.
        </p>
        <p style="font-size: 10px; color: #cbd5e0; margin: 10px 0 0 0;">
            Kindly provided by <a href="https://ckenterprises.co.uk" style="color: #7413dc; text-decoration: none;">CK Enterprises UK</a>
        </p>
    </div>
</body>
</html>
