<?php

namespace App\Services;

use App\Models\ApprovalSetting;
use App\Models\ApprovalLog;

class ApprovalService
{
    public static function getRequiredLevels(string $documentType, float $amount = 0): \Illuminate\Support\Collection
    {
        return ApprovalSetting::forBudget($documentType, $amount)->get();
    }

    public static function initiate(string $documentType, int $documentId, float $amount = 0): void
    {
        $levels = self::getRequiredLevels($documentType, $amount);

        foreach ($levels as $level) {
            ApprovalLog::create([
                'document_type' => $documentType,
                'document_id' => $documentId,
                'approval_setting_id' => $level->id,
                'level_name' => $level->level_name,
                'level_order' => $level->level_order,
                'action' => 'pending',
            ]);

            // Notify approver
            if ($level->approver_user_id) {
                NotificationService::send(
                    $level->approver_user_id,
                    'approval_request',
                    "Approval needed: {$documentType} #{$documentId}",
                    "Document requires your approval (Level: {$level->level_name})",
                    route('approval.pending'),
                    $documentType,
                    $documentId
                );
            }
        }
    }

    public static function approve(string $documentType, int $documentId, int $userId, ?string $remarks = null): string
    {
        $pending = ApprovalLog::where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->where('action', 'pending')
            ->orderBy('level_order')
            ->first();

        if (!$pending) return 'no_pending';

        $pending->update([
            'action' => 'approved',
            'acted_by' => $userId,
            'acted_at' => now(),
            'remarks' => $remarks,
        ]);

        // Check if all levels approved
        $stillPending = ApprovalLog::where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->where('action', 'pending')
            ->exists();

        return $stillPending ? 'partial' : 'fully_approved';
    }

    public static function reject(string $documentType, int $documentId, int $userId, ?string $remarks = null): void
    {
        ApprovalLog::where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->where('action', 'pending')
            ->update([
                'action' => 'rejected',
                'acted_by' => $userId,
                'acted_at' => now(),
                'remarks' => $remarks,
            ]);
    }

    public static function getStatus(string $documentType, int $documentId): \Illuminate\Support\Collection
    {
        return ApprovalLog::where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->orderBy('level_order')
            ->with('actor:id,name')
            ->get();
    }
}
