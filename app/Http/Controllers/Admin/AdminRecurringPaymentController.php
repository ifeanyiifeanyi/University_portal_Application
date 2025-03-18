<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RecurringPaymentPlan;
use App\Http\Requests\RecurringPaymentRequest;

class AdminRecurringPaymentController extends Controller
{
    public function index()
    {
        $plans = RecurringPaymentPlan::withCount(['subscriptions' => function ($query) {
            $query->where('amount_paid', '>', 0);
        }])->latest()->get();

        return view('admin.payments.recurring_payment.index', compact('plans'));
    }




    public function store(RecurringPaymentRequest $request)
    {
        $validated = $request->validated();
        $plan = RecurringPaymentPlan::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Recurring payment plan created successfully',
                'plan' => $plan
            ]);
        }

        return redirect()->back()->with('success', 'Recurring payment plan created successfully');
    }

    public function update(RecurringPaymentRequest $request, RecurringPaymentPlan $plan)
    {

        $validated = $request->validated();
        $plan->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Recurring payment plan updated successfully',
                'plan' => $plan
            ]);
        }

        return redirect()->back()->with('success', 'Recurring payment plan updated successfully');
    }

    public function destroy(RecurringPaymentPlan $plan)
    {

        if ($plan->subscriptions()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete plan with active subscriptions');
        }

        $plan->delete();

        return redirect()->back()->with('success', 'Recurring payment plan deleted successfully');
    }

    public function trash()
    {
        $plans = RecurringPaymentPlan::onlyTrashed()->get();
        return view('admin.payments.recurring_payment.trash', compact('plans'));
    }
    public function restore($id)
    {
        $plan = RecurringPaymentPlan::withTrashed()->findOrFail($id);
        $plan->restore();

        return redirect()->back()->with('success', 'Recurring payment plan restored successfully');
    }


    public function forceDelete($id)
    {
        $plan = RecurringPaymentPlan::withTrashed()->findOrFail($id);
        $plan->forceDelete();

        return redirect()->back()->with('success', 'Recurring payment plan deleted permanently');
    }
}
