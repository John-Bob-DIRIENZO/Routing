<?php

namespace App\Core\Utils;

class Regex
{
    public static function readFromDocBloc(string $docPrefix, string $docBlocComment)
    {
        preg_match("/@$docPrefix={(.*)}/", $docBlocComment, $match);
        return $match[1];
    }
}