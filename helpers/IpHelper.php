<?php
namespace app\helpers;

use Yii;
use yii\helpers\BaseIpHelper;

class IpHelper
{
    public static function getIp(): string
    {
        return Yii::$app->request->userIP;
    }
    public static function isIpv4($ip): bool
    {
        return \yii\helpers\IpHelper::getIpVersion($ip)== BaseIpHelper::IPV4;
    }
    public static function isIpv6($ip): bool
    {
        return \yii\helpers\IpHelper::getIpVersion($ip)== BaseIpHelper::IPV6;
    }
    public static function maskIp(string $ip): string
    {
        if (self::isIpv4($ip)) {
            return preg_replace('/(\d{1,3}\.\d{1,3})\.\d{1,3}\.\d{1,3}/', '$1.**', $ip);
        }

        if (self::isIpv6($ip)) {
            $normalizedIp = \yii\helpers\IpHelper::expandIPv6($ip);
            return preg_replace('/(:[0-9a-fA-F]{1,4}){4}$/', ':****:****:****:****', $normalizedIp);
        }
        return 'N/A';
    }

}