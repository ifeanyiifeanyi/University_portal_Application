<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminInvoiceManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with([
            'academicSession',
            'semester',
            'student',
            'department',
            'paymentMethod',
            'paymentType',
            'payment',
            'student'
        ])
            ->whereNull('archived_at')
            ->latest()
            ->get();
        return view('admin.invoices.index', compact('invoices'));
    }

    public function markAsPaid($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'Invoice is already marked as paid');
        }

        DB::beginTransaction();
        try {
            // Update invoice status
            $invoice->update([
                'status' => 'paid'
            ]);

            // Check for an existing payment record
            $payment = Payment::where('student_id', $invoice->student_id)
                ->where('department_id', $invoice->department_id)
                ->where('payment_type_id', $invoice->payment_type_id)
                ->first();

            if (!$payment) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'No corresponding payment record found for the invoice. Please verify.');
            }


            // Update existing payment record
            try {
                $updateResult = $payment->update([
                    'status' => 'paid',  // Make sure this matches your enum value exactly
                    'admin_id' => Auth::id(),
                    'admin_comment' => 'Payment manually verified by admin: ' . Auth::user()->full_name,
                    'payment_date' => now()
                ]);

                // Log the update result
                Log::info("Payment update result: " . ($updateResult ? 'success' : 'failed'));
            } catch (\Exception $e) {
                Log::error("Payment update failed: " . $e->getMessage());
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Failed to update payment status. Error: ' . $e->getMessage());
            }

            // Check if receipt already exists for this payment
            $existingReceipt = Receipt::where('payment_id', $payment->id)->first();

            if ($existingReceipt) {
                DB::commit();
                return redirect()->route('admin.payments.showReceipt', $existingReceipt->id)
                    ->with('success', 'Payment has been marked as paid and existing receipt found');
            }

            // Generate receipt
            $receipt = Receipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => 'REC' . uniqid(),
                'amount' => $payment->amount,
                'date' => now(),
            ]);

            // Log the activity
            activity()
                ->performedOn($invoice)
                ->withProperties([
                    'status' => 'paid',
                    'admin' => Auth::user()->full_name,
                    'payment_id' => $payment->id
                ])
                ->log('Invoice marked as paid manually');

            if ($receipt) {
                DB::commit();
                return redirect()->route('admin.payments.showReceipt', $receipt->id)
                    ->with('success', 'Payment marked as paid and new receipt generated');
            }

            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to generate receipt. Please try again.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark payment as paid: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while processing the payment. Please try again.');
        }
    }

    public function archive($invoice)
    {
        $invoice = Invoice::findOrFail($invoice);

        if ($invoice->status === 'pending') {
            return redirect()->back()->with('error', 'Cannot archive pending invoices');
        }

        DB::beginTransaction();
        try {
            $invoice->update([
                'archived_at' => now()
            ]);

            activity()
                ->performedOn($invoice)
                ->withProperties(['status' => 'archived'])
                ->log('Invoice archived');

            DB::commit();
            return redirect()->route('admin.invoice.view')
                ->with('success', 'Invoice has been archived successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while archiving the invoice');
        }
    }


    public function trashed()
    {
        $trashedInvoices = Invoice::with([
            'academicSession',
            'semester',
            'student',
            'department',
            'paymentMethod',
            'paymentType',
            'payment',
            'student'
        ])
            ->onlyTrashed()
            ->latest()
            ->get();

        return view('admin.invoices.trashed', compact('trashedInvoices'));
    }

    public function reverseTicketOnArchive($id)
    {
        $invoice = Invoice::findOrFail($id);



        DB::beginTransaction();
        try {
            $invoice->update([
                'archived_at' => null
            ]);

            activity()
                ->performedOn($invoice)
                ->withProperties(['status' => 'archived'])
                ->log('Invoice removed from archive');

            DB::commit();
            return redirect()->route('admin.invoice.view')
                ->with('success', 'Invoice has been removed from archive successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while archiving the invoice');
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $invoice = Invoice::onlyTrashed()->findOrFail($id);

            activity()
                ->performedOn($invoice)
                ->withProperties(['status' => 'restored'])
                ->log('Invoice restored from trash');

            $invoice->restore();

            DB::commit();
            return redirect()->route('admin.invoice.trashed')
                ->with('success', 'Invoice has been restored successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while restoring the invoice');
        }
    }

    public function forceDelete($id)
    {
        DB::beginTransaction();
        try {
            $invoice = Invoice::onlyTrashed()->findOrFail($id);

            activity()
                ->performedOn($invoice)
                ->withProperties(['status' => 'permanently_deleted'])
                ->log('Invoice permanently deleted');

            $invoice->forceDelete();

            DB::commit();
            return redirect()->route('admin.invoice.trashed')
                ->with('success', 'Invoice has been permanently deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while permanently deleting the invoice');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load([
            'student.user',
            'department',
            'paymentType',
            'paymentMethod',
            'academicSession',
            'semester',
            'payment'
        ]);

        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($invoice)
    {
        $invoice = Invoice::findOrFail($invoice);

        if ($invoice->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending invoices can be deleted!');
        }

        DB::beginTransaction();
        try {
            activity()
                ->performedOn($invoice)
                ->withProperties(['status' => 'deleted'])
                ->log('Invoice deleted');

            $invoice->delete();
            DB::commit();

            return redirect()->route('admin.invoice.view')
                ->with('success', 'Invoice has been deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the invoice');
        }
    }

    public function archived()
    {
        $archivedInvoices = Invoice::with([
            'academicSession',
            'semester',
            'student',
            'department',
            'paymentMethod',
            'paymentType',
            'payment',
            'student'
        ])
            ->whereNotNull('archived_at')
            ->latest()
            ->get();

        return view('admin.invoices.archived', compact('archivedInvoices'));
    }
}
