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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtablemulti/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

/**
 * MCW settings
 */
$rootDir = System::getContainer()->getParameter('kernel.project_dir');
assert(\is_string($rootDir));

/** @psalm-suppress UnresolvableInclude */
if (\file_exists($rootDir . '/system/config/module-multicolumnwizard.php')) {
    include_once($rootDir . '/system/config/module-multicolumnwizard.php');
}
