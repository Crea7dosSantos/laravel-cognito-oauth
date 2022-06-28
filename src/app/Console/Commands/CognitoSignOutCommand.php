<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Cognito;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CognitoSignOutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cognito:signout {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sign out Cognito';

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
        $this->signOut($email);

        return 0;
    }

    /**
     * sign out
     *
     * @param string $email
     * @return void
     */
    private function signOut(string $email): void
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $user = User::where('email', $email)->first();

        try {
            $this->service->signOut($user->cognito_username);
        } catch (CognitoIdentityProviderException $e) {
            $this->output->error(sprintf('error occuard'));
            $this->output->writeln($e->getAwsErrorMessage());
        }

        $this->output->success(sprintf('sign out for %s', $user->cognito_username));
    }
}
