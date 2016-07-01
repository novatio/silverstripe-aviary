/**
 * File: aviary.js
 */

/**
 * We need to define featherEditor & initAviary globally. Why?
 * CMSFileAddController / Uploadfield uses iFrames, which we need to pop out of in order to create a usable editor...
 */
var featherEditor, initAviary;

(function($) {
    initAviary = function(apiKey, localProcessing, iframePointer) {
        // TODO: add tools-option to config.yml (if people want to slim it down), use in this call.
        featherEditor = new Aviary.Feather({
            apiKey: apiKey,
            theme: 'light',
            tools: 'all',
            appendTo: '',
            maxSize: '9999',
            // localProcessing method;
            // set with Aviary.LocalProcessing = true (== default)
            onSaveButtonClicked: function(imageID) {
                if(typeof localProcessing !== 'undefined' && localProcessing) {
                    featherEditor.getImageData(function (image) {
                        $.ajax({
                            type: "POST",
                            url: '/AviaryUpload/localupdate',
                            data: {
                                imageData: image,
                                imageID: imageID.replace('aviary_image_', '')
                            },
                            dataType: 'json',
                            cache: false,
                            success: function (data, textStatus, jqXHR) {
                                if (typeof data.thumbnail !== 'undefined') {
                                    // iFrame hack
                                    if (typeof iframePointer !== 'undefined') {
                                        $('#thumbnailImage', $(iframePointer).contents()).attr('src', data.thumbnail);
                                    } else {
                                        $('#thumbnailImage').attr('src', data.thumbnail);
                                        
                                        // update image dimensions if we are editing from within HTMLEditorField.
                                        $editorImageWidth  = $('.details.ss-uploadfield-item-editform .field.dimensions input[name=Width]');
                                        $editorImageHeight = $('.details.ss-uploadfield-item-editform .field.dimensions input[name=Height]');

                                        if($editorImageWidth.length) {
                                            var img = new Image();
                                            img.onload = function() {
                                                $editorImageWidth.val(this.width);
                                                $editorImageHeight.val(this.height);
                                            };
                                            img.src = image;
                                        }
                                    }
                                }

                                window.aviaryIsDirty = false;
                            }
                        });

                    });

                    return false;
                }
            },
            // remote save method (saves data on Adobe's/Amazon's servers before syncing back)
            // set with Aviary.LocalProcessing = false
            onSave: function(imageID, newURL) {
                var img = document.getElementById(imageID);
                img.src = newURL;

                $.ajax({
                    type: "POST",
                    url: '/AviaryUpload/update',
                    data: {
                        url: newURL,
                        imageID: imageID.replace('aviary_image_', '')
                    },
                    dataType: 'json',
                    cache: false,
                    success: function(data, textStatus, jqXHR) {
                        if(typeof data.thumbnail !== 'undefined') {
                            // iFrame hack
                            if(typeof iframePointer !== 'undefined') {
                                $('#thumbnailImage', $(iframePointer).contents()).attr('src', data.thumbnail);
                            } else {
                                $('#thumbnailImage').attr('src', data.thumbnail);
                            }
                        }

                        window.aviaryIsDirty = false;
                    }
                });
            },
            // custom isDirty logic needed, there's a weird avairybug; onSave does not clear "isDirty" state.
            // (+ can't change internal state of paintWidgetInstance.dirty)
            onCloseButtonClicked: function(isDirty) {
                if(!isDirty) {
                    return isDirty;
                } else if(typeof window.aviaryIsDirty !== 'undefined') {
                    return window.aviaryIsDirty;
                }

                return isDirty;
            },
            onError: function(errorObj) {
                window.aviaryIsDirty = true;
            },
            onClose: function(isDirty) {
                delete window.aviaryIsDirty;
            }
        });

        return false;
    };

    $.entwine('ss', function($) {
        $('[name=action_AviaryEditImage]').entwine({
            onadd: function(e) {
                // iFrame hack
                if(window.location !== window.top.location) {
                    // clone image to top document.
                    $('body', top.document).append(
                        $(document).find('img.aviary_image').clone()
                    );

                    var imageID = $(document).find('.aviary_image').attr('id').replace('aviary_image_', '');
                    var iframePointer = "li[data-fileid='"+imageID+"'] iframe";

                    // add iframePointer to aviary, for pingback (save: thumb update)
                    window.top.initAviary($(this).data('apikey'), $(this).data('localprocessing'), iframePointer);
                } else {
                    initAviary($(this).data('apikey'), $(this).data('localprocessing'));
                }
            },

            onclick: function(e) {
                // iFrame hack
                if(window.location !== window.top.location) {
                    window.top.featherEditor.launch({
                        image: $('.aviary_image').first().attr('ID')
                    });
                } else {
                    featherEditor.launch({
                        image: $('.aviary_image').first().attr('ID')
                    });
                }

                this._super(e);

                // HtmlEditor_Toolbar closes when not returning false
                return false;
            }
        });
    });
}(jQuery));