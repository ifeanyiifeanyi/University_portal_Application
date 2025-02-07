<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class LogActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::latest()->get();
        return view('admin.logactivity.index', compact('activities'));
    }


    public function archivedLogs(){
        $archives = collect(Storage::files('archives'))
            ->filter(fn ($file) => str_starts_with(basename($file), 'activity_log_archive_'))
            ->map(fn ($file) => [
                'name' => basename($file),
                'size' => Storage::size($file),
                'created_at' => Storage::lastModified($file)
            ])
            ->sortByDesc('created_at')
            ->values();

        return view('admin.logactivity.indexDashboard', compact('archives'));
    }

    public function download(string $filename): StreamedResponse|Response
    {
        $path = "archives/{$filename}";

        if (!Storage::exists($path)) {
            abort(404, 'Archive not found');
        }

        return Storage::download($path);
    }

    public function truncateActivityLog(): RedirectResponse
    {
        try {
            Activity::truncate();
            return redirect()->back()
                ->with('success', 'Activity log table has been cleared successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to clear activity log: ' . $e->getMessage());
        }
    }

    public function deleteArchive(string $filename): RedirectResponse
    {
        $path = "archives/{$filename}";

        if (!Storage::exists($path)) {
            return redirect()->back()->with('error', 'Archive not found');
        }

        Storage::delete($path);

        return redirect()->route('activites_log.archived')
            ->with('success', 'Archive deleted successfully');
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();

        return redirect()->route('activities.index')->with('success', 'Activity deleted successfully.');
    }
}
