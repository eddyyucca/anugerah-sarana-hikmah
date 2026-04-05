<?php

namespace App\Services;

use App\Models\ApprovalSetting;
use App\Models\ApprovalLog;
<<<<<<< HEAD
use App\Models\User;
=======
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c

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
<<<<<<< HEAD

    /**
     * Check if user can approve the document
     * @param User $user
     * @param string $documentType
     * @param int $documentId
     * @param float $amount - Document amount for budget validation
     * @return array ['can_approve' => bool, 'message' => string]
     */
    public static function canApprove(User $user, string $documentType, int $documentId, float $amount = 0): array
    {
        // Admin can always approve
        if ($user->isAdmin()) {
            return ['can_approve' => true, 'message' => ''];
        }

        // Check if user has can_approve permission for this document type
        $menuKey = self::getMenuKeyForDocumentType($documentType);
        if (!$user->canAccess($menuKey, 'can_approve')) {
            return ['can_approve' => false, 'message' => 'You do not have approval permission for this document.'];
        }

        // Check if there's a pending approval for this document
        $pending = ApprovalLog::where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->where('action', 'pending')
            ->orderBy('level_order')
            ->first();

        if (!$pending) {
            return ['can_approve' => false, 'message' => 'No pending approval found for this document.'];
        }

        // Check if user is eligible approver for this level
        $setting = ApprovalSetting::find($pending->approval_setting_id);
        if (!self::isEligibleApprover($user, $setting)) {
            return ['can_approve' => false, 'message' => 'You are not the designated approver for this approval level.'];
        }

        return ['can_approve' => true, 'message' => ''];
    }

    /**
     * Check if user is eligible to approve based on ApprovalSetting
     * @param User $user
     * @param ApprovalSetting $setting
     * @return bool
     */
    public static function isEligibleApprover(User $user, ApprovalSetting $setting): bool
    {
        // If specific user is designated, only that user can approve
        if ($setting->approver_user_id) {
            return $user->id === $setting->approver_user_id;
        }

        // If role is designated, user with that role can approve
        if ($setting->approver_role) {
            return $user->role === $setting->approver_role;
        }

        // If no specific approver set, any user with can_approve permission can approve
        return true;
    }

    /**
     * Map document type to menu key
     * @param string $documentType
     * @return string
     */
    private static function getMenuKeyForDocumentType(string $documentType): string
    {
        return match($documentType) {
            'pr' => 'purchase-requests',
            'po' => 'purchase-orders',
            'wo' => 'work-orders',
            'gi' => 'goods-issues',
            'gs' => 'goods-receipts',
            'so' => 'stock-opname',
            default => $documentType,
        };
    }
=======
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
}
