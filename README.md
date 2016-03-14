# silverstripe-aviary

A SilverStripe Image editor, built with the Aviary Photo Editor (or Adobe Creative SDK Image Editing component). The editor is usable in the Assets Admin & in the HtmlEditorField Image component.
![silverstripe-aviary](https://novatio.github.io/silverstripe-aviary/img/silverstripe-aviary.jpg)

----------

## Requirements ##
SilverStripe >= 3.1

## Installation ##

#### Composer ####

1. `composer require novatio/silverstripe-aviary`
2. Add your aviary configuration (see below)
3. Run a dev/build?flush=1

#### Manual ####
1. Save into folder named 'aviary/' in the root of your SilverStripe installation.
2. Add your aviary configuration (see below)
3. Run a dev/build?flush=1

## Configuration ##
1. Create a new application on the [Adobe Creative SDK site](https://creativesdk.adobe.com/myapps.html)
2. Add your ClientID to `mysite/_config/config.yml`:
```yaml
Aviary:
  ClientID: '<your client id>'
```

By default this aviary module will use the default Adobe Creative SDK Image Editing flow:
It will use the default `onSave` method, which will save the generated image to the Creative Cloud 
and then sync this file you your own filesystem.

This method imposes two limits: Editing/output is limited to 1 megapixel and 250.000 saves a month.

This module provides a config setting so that all image logic is processed locally, on your own server.
Processing all saves locally will probably enable you to handle larger files without a save limitation.

To enable this add the following to your config:
```yaml
Aviary:
  LocalProcessing: true
```

Note: With this setting the Adobe Creative SDK library (JavaScript) is still loaded from Adobe's servers.
If you also want to load this from your own server add the following config directive:
```yaml
Aviary:
  LocalProcessing: true
  UseLocalLibrary: true
```

**Please check the [Adobe Creative SDK Terms of Use](http://adobe.com/go/creative_sdk_terms) to be sure 
local processing and loading the library from your own server is allowed.**

----------

## Attribution ##
- [Adobe Creative SDK](https://creativesdk.adobe.com/) Image Editing component and any Adobe images/badges/buttons, licence & copyright: [Adobe](http://www.adobe.com/) ([Terms of Use](http://adobe.com/go/creative_sdk_terms))
