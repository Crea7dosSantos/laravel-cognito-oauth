<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

final class NotExistsUserException extends Exception
{
    public function __construct()
    {
        Log::debug(__CLASS__ . '::' . __FUNCTION__ . ' called:(' . __LINE__ . ')');
        Log::debug('ユーザープールでの認証には成功ましたが、DBに登録情報が存在しませんでした');
    }
}
