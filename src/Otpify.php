<?php

namespace PrasanthJ\Otpify;

use Carbon\Carbon;
use PrasanthJ\Otpify\Models\Otp;

class Otpify
{
    /**
     * Generates a new token.
     *
     * @param   string      $identifier
     * @param   int|null    $userId
     * @param   string|null $otpType
     * @param   int|null    $digits
     * @param   int|null    $validity
     *
     * @return array<string,mixed|string>
     */
    public static function generate(string $identifier, int $userId = null, string $otpType = null, int $digits = null, int $validity = null)
    {
        if ($digits === null) {
            $digits = config('otpify.digits');
        }

        if ($validity === null) {
            $validity = config('otpify.validity');
        }

        Otp::where([
            ['identifier', $identifier],
            ['otp_type', $otpType]
        ])->delete();

        if (($digits >= 4) && ($digits <= 12)) {
            $token = rand(pow(10, $digits - 1), pow(10, $digits) - 1);

            Otp::create([
                'user_id'           => $userId,
                'identifier'        => $identifier,
                'token'             => $token,
                'validity'          => $validity,
                'otp_type'          => $otpType
            ]);

            return [
                'status'    => 'success',
                'token'     => $token,
                'message'   => 'OTP genetated successfully'
            ];
        }
    }

    /**
     * Validates the generated token.
     *
     * @param   string      $identifier
     * @param   string      $token
     * @param   string|null $otpType
     *
     * @return  array<string,string>
     */
    public static function validate(string $identifier, string $token, string $otpType = null)
    {
        $otp = Otp::where([
            ['identifier', $identifier],
            ['otp_type', $otpType]
        ])->first();

        if ($otp == null) {

            return [
                'status'    => 'error',
                'message'   => 'OTP does not exist'
            ];
        } else {
            if (($otp->token == $token) && ($otp->verified == false)) {
                $carbon = new Carbon();
                $now = $carbon->now();
                $validity = $otp->created_at->addMinutes($otp->validity);

                if (strtotime($validity) < strtotime($now)) {

                    return [
                        'status'    => 'error',
                        'message'   => 'OTP Expired'
                    ];
                } else {
                    $otp->verified = true;
                    $otp->update();

                    return [
                        'status'    => 'success',
                        'message'   => 'OTP is valid'
                    ];
                }
            } elseif (($otp->token == $token) && ($otp->verified == true)) {

                return [
                    'status'    => 'info',
                    'message'   => 'OTP already verified'
                ];
            } else {

                return [
                    'status'    => 'warning',
                    'message'   => 'OTP invalid'
                ];
            }
        }
    }
}
