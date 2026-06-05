<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\text;
use function Laravel\Prompts\spin;

#[Signature('dialog')]
#[Description('Converse with OpenAI')]
class DialogCommand extends Command
{

    protected array $history = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        while (true) {
            $prompt = text('What is on your mind?', required: true);

            $this->history[] =  [
                'role' => 'user',
                'content' => $prompt,
            ];

            $response = spin(
                fn() => $this->sendAiRequest(),
                'Woof! Thinking...'
            );

            $this->history = [...$this->history, ...$response['output']];

            $this->info($response['output'][0]['content'][0]['text']);
        }
    }

    /**
     * Send request to OpenAI and return decoded JSON response.
     */
    private function sendAiRequest(): array
    {
        return Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/responses', [
                'model' => 'gpt-5.4-nano',
                'instructions' => 'You are a helpful assistant. You are also a happy yellow labrador retriever.',
                'input' => $this->history
            ])
            ->throw()
            ->json();
    }
}
