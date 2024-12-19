<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
