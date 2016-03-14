<?php

class Aviary extends Object
{
    public static function loadAviary()
    {

        if(
            Config::inst()->get('Aviary', 'LocalProcessing') &&
            Config::inst()->get('Aviary', 'UseLocalLibrary') &&
            ($libary = self::fetchLocalAviaryLibrary())
        ) {
            Requirements::javascript($libary);
        } elseif (Director::is_https()) {
            Requirements::javascript('https://dme0ih8comzn4.cloudfront.net/imaging/v3/editor.js');
        } else {
            Requirements::javascript('http://feather.aviary.com/imaging/v3/editor.js');
        }

        Requirements::javascript(AVIARY_DIR . '/javascript/aviary.js');
    }

    protected static function fetchLocalAviaryLibrary()
    {
        $libraryFile = AVIARY_DIR . '/javascript/aviary.library.js';
        $libraryFullPath = BASE_PATH . '/' . $libraryFile;
        if(!file_exists($libraryFullPath)) {
            if($library = file_get_contents('https://dme0ih8comzn4.cloudfront.net/imaging/v3/editor.js')) {
                file_put_contents($libraryFullPath, $library);
            }
        }

        if(file_exists($libraryFullPath)) {
            return $libraryFile;
        }
    }
}