<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class FunctionsHelper
{
    public static function generateCode($length = 8, $table = null, $field = null): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, $charactersLength - 1)];
        }

        if ($table && $field) {
            $codeExist = DB::table($table)
                ->where($field, $code)->exists();
            if ($codeExist) {
                $code = self::generateCode($length, $table, $field);
            }
        }
        return $code;
    }

    public static function getInvitationFileRelativePath($invitationId, $eventId): string
    {
        return 'events/' . $eventId . '/invitations/' . $invitationId . '.pdf';
    }

}
