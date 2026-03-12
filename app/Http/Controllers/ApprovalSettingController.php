<?php

namespace App\Http\Controllers;

use App\Models\ApprovalSetting;
use App\Models\User;
use Illuminate\Http\Request;

class ApprovalSettingController extends Controller
{
    public function index(Request $request)
    {
        $query = ApprovalSetting::with('approverUser:id,name');

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        $settings = $query->orderBy('document_type')->orderBy('level_order')->paginate(25)->withQueryString();
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'role']);

        return view('settings.approval', compact('settings', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type' => 'required|in:pr,po,wo,gi',
            'level_name' => 'required|string|max:50',
            'level_order' => 'required|integer|min:1',
            'min_budget' => 'required|numeric|min:0',
            'max_budget' => 'nullable|numeric|min:0',
            'approver_user_id' => 'nullable|exists:users,id',
            'approver_role' => 'nullable|string|max:30',
        ]);

        ApprovalSetting::create($validated);
        return back()->with('success', 'Approval level added.');
    }

    public function update(Request $request, ApprovalSetting $approvalSetting)
    {
        $validated = $request->validate([
            'document_type' => 'required|in:pr,po,wo,gi',
            'level_name' => 'required|string|max:50',
            'level_order' => 'required|integer|min:1',
            'min_budget' => 'required|numeric|min:0',
            'max_budget' => 'nullable|numeric|min:0',
            'approver_user_id' => 'nullable|exists:users,id',
            'approver_role' => 'nullable|string|max:30',
        ]);

        $approvalSetting->update($validated);
        return back()->with('success', 'Approval level updated.');
    }

    public function destroy(ApprovalSetting $approvalSetting)
    {
        $approvalSetting->delete();
        return back()->with('success', 'Approval level deleted.');
    }
}
