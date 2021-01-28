L10n Translator
=====

Extension for managing the files located in the `var/labels` folder of a TYPO3 installation. It provides several CLI commands for
file and label handling as well as a backend module to translate any label.

Configuration
----

Add all paths to XLF files you want to handle with this extension (e.g. `news/Resources/Private/Languages/locallang.xlf`) 
alongside all languages you want to support in the extension manager configuration.

Features
----

* Create missing files in the `var/labels` folder (CLI)
* Create missing labels in files in the `var/labels` folder (CLI)
* Proof integrity of language files in the `var/labels` folder (CLI)
* Edit existing labels in a custom backend module (BE)

CLI Examples
----

Execute all CLI commands 

With typo3-console via the prefix `./vendor/bin/typo3cms l10nTranslator:create:missingFiles all`

Create all missing files in `var/labels/de`. This will create copies of all configured files that are not there yet.
`l10nTranslator:create:missingFiles de`

Create all missing labels in `var/labels/es` in all configured files.
`l10nTranslator:create:missingLabels es --file=powermail/Resources/Private/Language/locallang.xlf`

Create all missing labels for powermail in spanish and fills the source language with german labels.
`l10nTranslator:create:missingLabels es --file=powermail/Resources/Private/Language/locallang.xlf --sourceLanguage=de`

Create all missing files for all configured languages
`l10nTranslator:create:missingFiles all`

Create all missing labels for all configured languages
`l10nTranslator:create:missingLabels all`

Create all missing files for all existing sys_languages
`l10nTranslator:create:missingFiles system`

Create all missing labels for all existing sys_languages
`l10nTranslator:create:missingLabels system`
