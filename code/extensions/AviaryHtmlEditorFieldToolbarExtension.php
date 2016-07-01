<?php

// TODO: merge methods (where possible) with AviaryImageExtension
class AviaryHtmlEditorFieldToolbarExtension extends DataExtension
{
    public function updateFieldsForFile(FieldList $fields, $url, $file)
    {
        if(!$apiKey = Config::inst()->get('Aviary', 'ClientID')) {
            return;
        }

        // load Aviary (js+css)
        Aviary::loadAviary();

        // Image pointer
        $aviaryImage = LiteralField::create(
            'AviaryImage',
            '<img class="aviary_image" id="aviary_image_' . $file->ID . '" src="/' . $url . '" style="display: none;" />'
        );

        // create edit button
        $editButton = FormAction::create(
            'AviaryEditImage',
            _t('Aviary.EditImage', 'Edit Image')
        )->setAttribute('data-apikey', $apiKey)
         ->setAttribute('data-localprocessing', Config::inst()->get('Aviary', 'LocalProcessing'));;

        if (
            // weird double load bug...
            !($fields->fieldByName('FilePreview.FilePreviewImage.AviaryEditImageWrapper')) &&
            ($previewRoot = $fields->fieldByName('FilePreview.FilePreviewImage.ImageFull'))

        ) {
            $fields->insertAfter(
                CompositeField::create(
                    $editButton
                )->setName('AviaryEditImageWrapper'),
                $previewRoot->getName()
            );

            $fields->insertAfter($aviaryImage, 'FilePreviewImage');
        }
    }
}