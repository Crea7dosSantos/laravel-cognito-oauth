<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class MeController extends Controller
{
    /**
     * get user information
     *
     * @param Request $request
     * @return MeResource
     */
    public function __invoke(Request $request): MeResource
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');

        return new MeResource($request->user());
    }
}
