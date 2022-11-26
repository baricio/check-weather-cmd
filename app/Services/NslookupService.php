<?php

namespace App\Services;

use Exception;

class NslookupService
{
    private $_outPut = null;
    private $_resultCode = null;

    public function getPublicIp()
    {
        $this->execComandGetPublicIp();
        return $this->filterIp();
    }

    public function execComandGetPublicIp()
    {
        exec(
            "nslookup myip.opendns.com resolver1.opendns.com",
            $this->_outPut,
            $this->_resultCode
        );
    }

    private function filterIp()
    {
        $commandResposne = (string) collect($this->_outPut)->reverse()->skip(1)->first();
        $regexIpAddress = '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}(?:\/\d{2})?/';
        preg_match($regexIpAddress, $commandResposne, $ip_match);
        $ip = data_get($ip_match, '0', null);
        if (empty($ip)) {
            throw new Exception("Fail connect to internet", 1);
        }
        return $ip;
    }
}
