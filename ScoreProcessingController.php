<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Score;
use App\Models\GpaRecord;
use Illuminate\Http\Request;

class ScoreProcessingController extends Controller
{
    public function processScores(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'exam_score' => 'required|numeric|min:0|max:100',
            'assessment_score' => 'required|numeric|min:0|max:100',
        ]);

        $enrollment = Enrollment::findOrFail($request->enrollment_id);
        $totalScore = $request->exam_score + $request->assessment_score;

        $grade = $this->calculateGrade($totalScore);
        $gradePoint = $this->calculateGradePoint($grade);

        $score = Score::updateOrCreate(
            ['enrollment_id' => $enrollment->id],
            [
                'exam_score' => $request->exam_score,
                'assessment_score' => $request->assessment_score,
                'grade' => $grade,
                'grade_point' => $gradePoint,
            ]
        );

        $this->updateGpaRecord($enrollment->student_id, $enrollment->semester_id);

        return response()->json(['message' => 'Score processed successfully', 'score' => $score]);
    }

    private function calculateGrade($totalScore)
    {
        if ($totalScore >= 70) return 'A';
        if ($totalScore >= 60) return 'B';
        if ($totalScore >= 50) return 'C';
        if ($totalScore >= 45) return 'D';
        return 'F';
    }

    private function calculateGradePoint($grade)
    {
        switch ($grade) {
            case 'A': return 5.0;
            case 'B': return 4.0;
            case 'C': return 3.0;
            case 'D': return 2.0;
            default: return 0.0;
        }
    }

    private function updateGpaRecord($studentId, $semesterId)
    {
        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('semester_id', $semesterId)
            ->with(['score', 'course'])
            ->get();

        $totalGradePoints = 0;
        $totalCreditUnits = 0;

        foreach ($enrollments as $enrollment) {
            if ($enrollment->score) {
                $totalGradePoints += $enrollment->score->grade_point * $enrollment->course->credit_units;
                $totalCreditUnits += $enrollment->course->credit_units;
            }
        }

        $gpa = $totalCreditUnits > 0 ? $totalGradePoints / $totalCreditUnits : 0;

        $gpaRecord = GpaRecord::updateOrCreate(
            ['student_id' => $studentId, 'semester_id' => $semesterId],
            ['gpa' => $gpa]
        );

        $this->updateCGPA($studentId);

        return $gpaRecord;
    }

    private function updateCGPA($studentId)
    {
        $gpaRecords = GpaRecord::where('student_id', $studentId)->get();

        $totalGPA = $gpaRecords->sum('gpa');
        $cgpa = $gpaRecords->count() > 0 ? $totalGPA / $gpaRecords->count() : 0;

        foreach ($gpaRecords as $record) {
            $record->update(['cgpa' => $cgpa]);
        }
    }
}