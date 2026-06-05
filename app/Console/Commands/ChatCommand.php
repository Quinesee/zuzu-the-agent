<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\text;
use function Laravel\Prompts\spin;

#[Signature('chat')]
#[Description('Receive AI response')]
class ChatCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $prompt = text('What is on your mind?', required: true);

        $response = spin(
            fn() => $this->sendAiRequest($prompt),
            'Woof! Thinking...'
        );

        dump($response['output'][0]['content'][0]['text']);
    }

    /**
     * Send request to OpenAI and return decoded JSON response.
     */
    private function sendAiRequest(string $prompt): array
    {
        return Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/responses', [
                'model' => 'gpt-5.4-nano',
                'instructions' => 'You are a helpful assistant. You are also a happy yellow labrador retriever.',
                'input' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ])
            ->throw()
            ->json();
    }
}
