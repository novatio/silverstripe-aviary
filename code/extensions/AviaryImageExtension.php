<?php

class AviaryImageExtension extends DataExtension
{
    public function updateCMSFields(FieldList $fields)
    {
        if(!$apiKey = Config::inst()->get('Aviary', 'ClientID')) {
            return;
        }

        // load Aviary (js+css)
        Aviary::loadAviary();

        // Image pointer
        $aviaryImage = LiteralField::create(
            'AviaryImage',
            '<img class="aviary_image" id="aviary_image_'.$this->owner->ID.'" src="'.$this->owner->URL.'" style="display: none;" />'
        );

        // create edit button
        $editButton = FormAction::create(
            'AviaryEditImage',
            _t('Aviary.EditImage', 'Edit Image')
        )->setAttribute('data-apikey', $apiKey)
         ->setAttribute('data-localprocessing', Config::inst()->get('Aviary', 'LocalProcessing'));

        if ($fields->hasTabSet()) {
            $fields->insertAfter(
                CompositeField::create(
                    $editButton
                )->setName('AviaryEditImageWrapper'),
                'ImageFull'
            );

            $fields->insertAfter($aviaryImage, 'FilePreviewImage');
        } else {
            $fields->add(CompositeField::create(
                $aviaryImage,
                $editButton
            ));
        }
    }
}