<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Domain extends Model
{
    use HasFactory;

    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * get domain part
     *
     * @return string
     */
    public function getPart(): string
    {
        $second_slash_idx = strpos($this->url, '/') + 1;
        $exclude_protocols_url = substr($this->url, $second_slash_idx + 1);
        $url_path_first_slash_idx = strpos($exclude_protocols_url, '/');
        $domain = substr($exclude_protocols_url, 0, - (strlen($exclude_protocols_url) - $url_path_first_slash_idx));

        return $domain;
    }

    /**
     * get domain part exclude subdomain
     *
     * @return string
     */
    public function getWithoutSubdomain(): string
    {
        $second_slash_idx = strpos($this->url, '/') + 1;
        $exclude_protocols_url = substr($this->url, $second_slash_idx + 1);

        if (strpos($exclude_protocols_url, '/') !== false) {
            $url_path_first_slash_idx = strpos($exclude_protocols_url, '/');
            $domain = substr($exclude_protocols_url, 0, - (strlen($exclude_protocols_url) - $url_path_first_slash_idx));
        } else {
            $domain = $exclude_protocols_url;
        }

        // Log::debug("domain-pre: $domain");
        if (strpos($domain, 'api.') !== false || strpos($domain, 'mypage.') !== false) {
            // domainの中にapi.が含まれている場合
            $domain_arr = explode('.', $domain);
            unset($domain_arr[0]);
            $domain = implode('.', $domain_arr);
        }

        return $domain;
    }
}
