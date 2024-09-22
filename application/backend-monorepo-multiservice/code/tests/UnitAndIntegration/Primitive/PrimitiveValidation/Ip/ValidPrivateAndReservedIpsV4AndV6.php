<?php

declare(strict_types=1);

namespace Tests\Galeas\Api\UnitAndIntegration\Primitive\PrimitiveValidation\Ip;

abstract class ValidPrivateAndReservedIpsV4AndV6
{
    /**
     * @return string[]
     */
    public static function listValidIps(): array
    {
        // Private IPV4 ranges: 10.0.0.0/8, 172.16.0.0/12, and 192.168.0.0/16.
        // Reserved IPV4 ranges: 0.0.0.0/8, 169.254.0.0/16, 127.0.0.0/8, and 240.0.0.0/4.
        // Private IPV6 ranges: fc00::/7.
        // Reserved IPV6 ranges: ::/127 (::1/128 and ::/128), ::ffff:0:0/96, and fe80::/10.
        return [
            // 10.0.0.0/8
            '10.0.0.0',
            '10.123.123.123',
            '10.255.255.255',
            // 172.16.0.0/12
            '172.16.0.0',
            '172.16.255.255',
            '172.23.0.0',
            '172.23.255.255',
            '172.31.0.0',
            '172.31.255.255',
            // 192.168.0.0/16
            '192.168.0.0',
            '192.168.123.123',
            '192.168.255.255',
            // 0.0.0.0/8
            '0.0.0.0',
            '0.123.123.123',
            '0.255.255.255',
            // 169.254.0.0/16
            '169.254.0.0',
            '169.254.123.123',
            '169.254.255.255',
            // 127.0.0.0/8
            '127.0.0.0',
            '127.123.123.123',
            '127.255.255.255',
            // 240.0.0.0/4
            '240.0.0.0',
            '247.0.0.0',
            '247.123.123.123',
            '247.255.255.255',
            '255.255.255.255',
            // fc00::/7
            'FC00:0000:0000:0000:0000:0000:0000:0000',
            'FC00:0000:0000:0000:0000:0000:0000:0001',
            'FCFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFE',
            'FCFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF',
            'FD00:0000:0000:0000:0000:0000:0000:0000',
            'FD00:0000:0000:0000:0000:0000:0000:0001',
            'FDFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFE',
            'FDFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF',
            'FC00::',
            'fc00::',
            'FC00::1',
            'fc00::1',
            'fc00:0000:0000:0000:0000:0000:0000:0000',
            'FD00::',
            'fd00::',
            'FD00::1',
            'fd00::1',
            'fd00:0000:0000:0000:0000:0000:0000:0000',
            'fdff:ffff:ffff:ffff:ffff:ffff:ffff:ffff',
            // ::/127
            '::',
            '::1',
            '0000:0000:0000:0000:0000:0000:0000:0000',
            '0000:0000:0000:0000:0000:0000:0000:0001',
            // ::ffff:0:0/96
            '0000:0000:0000:0000:0000:FFFF:0000:0000',
            '0000:0000:0000:0000:0000:FFFF:FFFF:FFFF',
            '::FFFF:0:0',
            '::FFFF:FFFF:FFFF',
            '::ffff:0:0',
            '::ffff:ffff:ffff',
            // fe80::/10
            'FE80:0000:0000:0000:0000:0000:0000:0000',
            'FEBF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF',
            'fe80::',
            'FE80::',
        ];
    }
}