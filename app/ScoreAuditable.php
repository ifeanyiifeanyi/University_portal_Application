<?php

namespace App;

use App\Models\ScoreAudit;
use Illuminate\Support\Facades\Request;

trait ScoreAuditable
{
    public function auditScore($scoreId, $action, $comment, $oldValue = null, $newValue = null)
    {
        $changedFields = is_array($oldValue) && is_array($newValue)
            ? array_keys(array_diff_assoc($newValue, $oldValue))
            : null;

        ScoreAudit::create([
            'student_score_id' => $scoreId,
            'user_id' => auth()->id(),
            'action' => $action,
            'comment' => $comment,
            'old_value' => json_encode($oldValue),
            'new_value' => json_encode($newValue),
            'changed_fields' => $changedFields,
            'ip_address' => Request::ip()
        ]);
    }
}
