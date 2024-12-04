<?php

namespace App\Services;

use OpenAI\Client;
use App\Models\Department;
use App\Models\SuggestedResponse;
use OpenAI\Laravel\Facades\OpenAI;

class AITicketService {
    protected $client;

    public function __construct()
    {
        $this->client = Client::create(env('OPENAI_API_KEY'));;
    }

    public function categorizeTicket($description)
    {
        $response = $this->client->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => "Categorize the following ticket description: $description",
            'max_tokens' => 20,
        ]);

        return trim($response['choices'][0]['text']);
    }
}
