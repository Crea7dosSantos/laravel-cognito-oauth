<?php

namespace App\Models;

use Laravel\Passport\Client as BaseClient;

class Client extends BaseClient
{
    /**
     * クライアントが認可プロンプトを飛ばすべきか判定
     *
     * @return bool
     */
    public function skipsAuthorization()
    {
        return true;
    }
}
