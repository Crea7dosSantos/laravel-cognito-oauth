<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Cognito;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class CognitoRefreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:refresh {email} {refresh_token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Cognito IDToken';

    private $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Cognito $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $refresh_token = $this->argument('refresh_token');

        $this->refresh($email, $refresh_token);

        return 0;
    }

    /**
     * refresh token
     *
     * @param string $email
     * @param string $refresh_token
     * @return void
     */
    private function refresh(string $email, string $refresh_token): void
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $user = User::where('email', $email)->first();

        try {
            $response = $this->service->refresh($user->cognito_username, $refresh_token);
        } catch (CognitoIdentityProviderException $e) {
            $this->output->error(sprintf('error occuard'));
            $this->output->writeln($e->getAwsErrorMessage());
        }

        $this->output->success(sprintf('token for %s', $user->cognito_username));
        $this->output->writeln($response->toArray()['AuthenticationResult']['IdToken']);

        Log::debug($response->toArray());
    }
}
