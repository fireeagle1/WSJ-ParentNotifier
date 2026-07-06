<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $portalName }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 0; background-color: #f5f5f5;">
    <!-- Header -->
    <div style="background-color: #003982; padding: 25px 20px; text-align: center;">
        <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 700;">{{ $portalName }}</h1>
        <p style="color: #d5eeee; margin: 5px 0 0 0; font-size: 13px;">New update from {{ $leaderName }}</p>
    </div>

    <!-- Body -->
    <div style="background-color: #ffffff; padding: 25px;">
        <!-- Post Content -->
        <div style="background-color: #eef8f8; border-left: 4px solid #003982; padding: 15px 18px; margin: 0 0 20px 0; border-radius: 0 6px 6px 0;">
            <p style="margin: 0; white-space: pre-wrap; color: #2d3748; font-size: 14px; line-height: 1.7;">{{ $truncatedContent }}</p>
        </div>

        <!-- CTA Button -->
        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ $feedUrl }}"
               style="display: inline-block; background-color: #003982; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 14px;">
                View Full Update
            </a>
        </div>
    </div>

    <!-- Footer -->
    <div style="padding: 18px 25px; text-align: center; border-top: 1px solid #e2e8f0; background-color: #fafafa;">
        <p style="font-size: 11px; color: #a0aec0; margin: 0;">
            You are receiving this email because you are registered on {{ $portalName }}.
            <br>
            <a href="{{ $feedUrl }}" style="color: #003982;">View the feed</a>
        </p>
        <p style="font-size: 10px; color: #cbd5e0; margin: 10px 0 0 0;">
            Kindly provided by <a href="https://ckenterprises.co.uk" style="color: #003982; text-decoration: none;">CK Enterprises UK</a>
        </p>
    </div>
</body>
</html>
