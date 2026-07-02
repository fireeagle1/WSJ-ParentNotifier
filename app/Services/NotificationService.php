<?php

namespace App\Services;

use App\Mail\NewPostNotification;
use App\Models\Guardian;
use App\Models\NotificationLog;
use App\Models\Post;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotificationService
{
    /**
     * Send email notifications to all active (non-revoked) parents.
     *
     * Sends synchronously one-by-one, logging success/failure for each.
     * Continues sending to remaining parents even if one fails.
     */
    public function notifyAllParents(Post $post): NotificationResult
    {
        $parents = Guardian::where('is_revoked', false)->get();

        $sent = 0;
        $failed = 0;

        foreach ($parents as $parent) {
            try {
                Mail::to($parent->email)->send(new NewPostNotification($post, $parent));

                NotificationLog::create([
                    'post_id' => $post->id,
                    'parent_id' => $parent->id,
                    'status' => 'sent',
                ]);

                $sent++;
            } catch (Throwable $e) {
                NotificationLog::create([
                    'post_id' => $post->id,
                    'parent_id' => $parent->id,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                $failed++;
            }
        }

        return new NotificationResult(sent: $sent, failed: $failed);
    }
}
