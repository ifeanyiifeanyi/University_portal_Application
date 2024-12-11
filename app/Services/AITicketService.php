<?php

namespace App\Services;

// use GuzzleHttp\Client;
use OpenAI\Client;
use App\Models\Department;
use App\Models\SuggestedResponse;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\AiSuggestedResponse;

class AITicketService
{

    public function categorizeTicket($description)
    {
        $response = OpenAI::completions()->create([
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => "Categorize the following ticket description into one of these categories: Technical, Academic, Financial, Administrative: $description",
            'max_tokens' => 20,
        ]);

        return trim($response->choices[0]->text);
    }

    // public function generateSuggestedResponse($ticketId, $questionId, $description)
    // {
    //     $response = OpenAI::completions()->create([
    //         'model' => 'gpt-3.5-turbo-instruct',
    //         'prompt' => "Generate a professional and helpful response to this ticket inquiry: $description",
    //         'max_tokens' => 150,
    //     ]);

    //     return AiSuggestedResponse::create([
    //         'ticket_id' => $ticketId,
    //         'question_id' => $questionId,
    //         'suggested_response' => trim($response->choices[0]->text),
    //         'was_used' => false
    //     ]);
    // }

    public function suggestResponse($question)
    {
        $response = OpenAI::completions()->create([
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => "Generate a helpful response for the following support ticket question: $question",
            'max_tokens' => 1,
        ]);

        return trim($response->choices[0]->text);
    }
}
