<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                'created_at' => Storage::lastModified($file),
                'month' => substr(basename($file), 20, 7) // Extract YYYY_MM from filename
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

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();

        return redirect()->route('activities.index')->with('success', 'Activity deleted successfully.');
    }
}
