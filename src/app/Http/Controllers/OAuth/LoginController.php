<?php

namespace App\Http\Controllers\OAuth;

use App\Exceptions\NotExistsUserException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OAuth\Login\StoreRequest;
use App\UseCases\OAuth\LoginAction;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

final class LoginController extends Controller
{
    use RedirectsUsers;

    protected function index(Request $request)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        Log::debug($request->session()->get('url.intended'));
        Log::debug($request->session()->all());

        return view('oauth.login');
    }

    /**
     * login function
     *
     * @param StoreRequest $request
     * @param LoginAction $action
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request, LoginAction $action)
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        try {
            $action($request);
        } catch (CognitoIdentityProviderException $e) {
            return response()->json(['message' => $e->getAwsErrorMessage()], Response::HTTP_UNAUTHORIZED);
        } catch (NotExistsUserException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        return $request->wantsJson()
            ? new JsonResponse([], Response::HTTP_NO_CONTENT)
            : redirect()->intended($this->redirectPath());
    }
}
