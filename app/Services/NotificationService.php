<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function send(int $userId, string $type, string $title, ?string $message = null, ?string $link = null, ?string $refType = null, ?int $refId = null): void
    {
        Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'reference_type' => $refType,
            'reference_id' => $refId,
        ]);
    }

    public static function sendToRole(string $role, string $type, string $title, ?string $message = null, ?string $link = null, ?string $refType = null, ?int $refId = null): void
    {
        $users = User::where('role', $role)->where('is_active', true)->get();
        foreach ($users as $user) {
            self::send($user->id, $type, $title, $message, $link, $refType, $refId);
        }
    }

    public static function sendToApprovers(string $documentType, string $title, ?string $message = null, ?string $link = null, ?string $refType = null, ?int $refId = null): void
    {
        $approverIds = \App\Models\ApprovalSetting::where('document_type', $documentType)
            ->where('is_active', true)
            ->whereNotNull('approver_user_id')
            ->pluck('approver_user_id')
            ->unique();

        foreach ($approverIds as $userId) {
            self::send($userId, 'approval_request', $title, $message, $link, $refType, $refId);
        }
    }
}
