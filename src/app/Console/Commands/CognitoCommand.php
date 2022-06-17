<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Cognito;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CognitoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myapp:cognito {name} {email} {password} {method}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create/Auth User';

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
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $method = $this->argument('method');

        if (!in_array($method, ['auth', 'signUp', 'setPassword'])) {
            $this->output->error('method is allowed auth or signUp');
            return 1;
        }

        $this->$method($name, $email, $password);
        return 0;
    }

    /**
     * sign up
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return void
     */
    private function signUp(string $name, string $email, string $password): void
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $username = uniqid();

        try {
            $response = $this->service->signUp($username, $password);
            $this->service->confirmSignUp($username);
        } catch (CognitoIdentityProviderException $e) {
            $this->output->error(sprintf('error occuard'));
            $this->output->writeln($e->getAwsErrorMessage());
        }

        $user = User::create([
            'cognito_username' => $username,
            'cognito_sub' => $response->toArray()['UserSub'],
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        $this->output->success(sprintf('created %s', $email));
        $this->output->writeln($response->toArray()['UserSub']);
    }

    /**
     * sign in
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return void
     */
    private function auth(string $name, string $email, string $password): void
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $user = User::where('email', $email)->first();

        try {
            $response = $this->service->auth($user->cognito_username, $password);
        } catch (CognitoIdentityProviderException $e) {
            $this->output->error(sprintf('error occuard'));
            $this->output->writeln($e->getAwsErrorMessage());
            return;
        }

        $this->output->success(sprintf('token for %s', $user->cognito_username));
        $this->output->writeln($response->toArray()['AuthenticationResult']['IdToken']);

        $this->output->success(sprintf('refresh_token for %s', $user->cognito_username));
        $this->output->writeln($response->toArray()['AuthenticationResult']['RefreshToken']);
    }

    /**
     * sign in
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return void
     */
    private function setPassword(string $name, string $email, string $password): void
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        $user = User::where('email', $email)->first();

        try {
            $response = $this->service->setPassword($user->cognito_username, $password);
        } catch (CognitoIdentityProviderException $e) {
            $this->output->error(sprintf('error occuard'));
            $this->output->writeln($e->getAwsErrorMessage());
            return;
        }

        $this->output->success(sprintf('complete set password for %s', $user->cognito_username));
    }
}
