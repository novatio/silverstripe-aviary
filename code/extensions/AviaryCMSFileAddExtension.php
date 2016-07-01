<?php

class AviaryCMSFileAddExtension extends DataExtension
{
    public function updateEditForm($form)
    {
        if(!$apiKey = Config::inst()->get('Aviary', 'ClientID')) {
            return;
        }

        // load Aviary (js+css)
        Aviary::loadAviary();
    }
}