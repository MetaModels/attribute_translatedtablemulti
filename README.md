[![Build Status](https://github.com/MetaModels/attribute_translatedtablemulti/actions/workflows/diagnostics.yml/badge.svg)](https://github.com/MetaModels/attribute_translatedtablemulti/actions)
[![Latest Version tagged](http://img.shields.io/github/tag/MetaModels/attribute_translatedtablemulti.svg)](https://github.com/MetaModels/attribute_translatedtablemulti/tags)
[![Latest Version on Packagist](http://img.shields.io/packagist/v/MetaModels/attribute_translatedtablemulti.svg)](https://packagist.org/packages/MetaModels/attribute_translatedtablemulti)
[![Installations via composer per month](http://img.shields.io/packagist/dm/MetaModels/attribute_translatedtablemulti.svg)](https://packagist.org/packages/MetaModels/attribute_translatedtablemulti)

# The translated multi attribute table

The translated multi attribute table for MetaModels.

With this attribute you are able to create complex table structures with the
[MultiColumnWizard]( https://github.com/menatwork/contao-multicolumnwizard-bundle).


## Original idea by Byteworks:

- [Ronny Binder](mailto:rb@bytworks.ch)
- [Michael Bischof](mailto:mb@byteworks.ch)


## Configure the table multi attribute

Create the configuration in e.g. the contao/config/config.php or src/Resources/contao/config/config.php or somewhere
else where the config is loaded and write something like this:

The `mm_test` is the name of the table and the `multi_test` is the name of the field.

```php
$GLOBALS['TL_CONFIG']['metamodelsattribute_multi']['mm_test']['multi_test'] = [
    'minCount'     => 1,
    'maxCount'     => 5,
    'columnFields' => [
        'col_title'     => [
            'label'     => 'Title',
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'style' => 'width:130px'
            ]
        ],
        'col_highlight' => [
            'label'     => 'Hervorheben',
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => [
                'style' => 'width:40px'
            ]
        ],
        'col_url'       => [
            'label'     => 'URL',
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'style'     => 'width:130px',
                'mandatory' => false,
                'rgxp'      => 'url'
            ]
        ],
    ],
];
```
