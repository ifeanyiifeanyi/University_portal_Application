<?php

namespace App\Observers;

use App\Models\RecurringPaymentPlan;

class RecurringPaymentPlanObserver
{
    /**
     * Handle the RecurringPaymentPlan "created" event.
     */
    public function created(RecurringPaymentPlan $plan): void
    {
        activity()
            ->performedOn($plan)
            ->withProperties([
                'name' => $plan->name,
                'description' => $plan->description,
                'amount' => $plan->amount,
                'is_active' => $plan->is_active,
                'id' => $plan->id,
            ])
            ->log('recurring_payment_plan_created');
    }

    /**
     * Handle the RecurringPaymentPlan "updated" event.
     */
    public function updated(RecurringPaymentPlan $plan): void
    {
        $changes = $plan->getDirty();

        activity()
            ->performedOn($plan)
            ->withProperties([
                'old' => array_intersect_key($plan->getOriginal(), $changes),
                'new' => $changes,
                'id' => $plan->id,
            ])
            ->log('recurring_payment_plan_updated');
    }

    /**
     * Handle the RecurringPaymentPlan "deleted" event.
     */
    public function deleted(RecurringPaymentPlan $plan): void
    {
        activity()
            ->performedOn($plan)
            ->withProperties([
                'name' => $plan->name,
                'id' => $plan->id,
                'subscriptions_count' => $plan->subscriptions_count,
            ])
            ->log('recurring_payment_plan_soft_deleted');
    }

    /**
     * Handle the RecurringPaymentPlan "restored" event.
     */
    public function restored(RecurringPaymentPlan $plan): void
    {
        activity()
            ->performedOn($plan)
            ->withProperties([
                'name' => $plan->name,
                'id' => $plan->id,
            ])
            ->log('recurring_payment_plan_restored');
    }

    /**
     * Handle the RecurringPaymentPlan "force deleted" event.
     */
    public function forceDeleted(RecurringPaymentPlan $plan): void
    {
        activity()
            ->performedOn($plan)
            ->withProperties([
                'name' => $plan->name,
                'id' => $plan->id,
                'last_known_subscriptions_count' => $plan->subscriptions_count,
            ])
            ->log('recurring_payment_plan_force_deleted');
    }
}
