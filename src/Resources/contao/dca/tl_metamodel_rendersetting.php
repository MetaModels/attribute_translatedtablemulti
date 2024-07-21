<?php

/**
 * This file is part of MetaModels/attribute_translatedtablemulti.
 *
 * (c) 2012-2024 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableMulti
 * @author     Andreas Dziemba <adziemba@web.de>
 * @author     Andreas Isaak <andy.jared@googlemail.com>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtablemulti/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['metapalettes']['translatedtablemulti extends default'] = [
    '+advanced' => ['translatedtablemulti_hide_tablehead'],
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['translatedtablemulti_hide_tablehead'] = [
    'label'       => 'translatedtablemulti_hide_tablehead.label',
    'description' => 'translatedtablemulti_hide_tablehead.description',
    'exclude'     => true,
    'inputType'   => 'checkbox',
    'eval'        => [
        'tl_class' => 'clr w50'
    ],
    'sql'         => "varchar(1) NOT NULL default '0'",
];
