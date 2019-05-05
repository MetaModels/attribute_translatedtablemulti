<?php

/**
 * This file is part of MetaModels/attribute_translatedtablemulti.
 *
 * (c) 2012-2019 The MetaModels team.
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
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtablemulti/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeTranslatedTableMultiBundle\Runonce;

use Contao\Controller;

/**
 * Class TranslatedTableMultiRunOnce
 *
 * @package MetaModels\AttributeTranslatedTableMultiBundle
 */
class TranslatedTableMultiRunOnce extends Controller
{
    /**
     * Initialize the object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('Database');
    }

    /**
     * Run the controller
     * Update from attribute_translatedmulto to attribute_translatedtablemulti
     *
     * @return void|null
     */
    public function run()
    {
        if ($this->Database->tableExists('tl_metamodel_translatedtablemulti')) {
            return;
        }

        if ($this->Database->tableExists('tl_metamodel_translatedmulti')) {
            $this->Database
                ->prepare('RENAME TABLE tl_metamodel_translatedmulti TO tl_metamodel_translatedtablemulti')
                ->execute();

            $this->Database
                ->prepare("UPDATE tl_metamodel_attribute SET type='translatedtablemulti' WHERE type='translatedmulti'")
                ->execute();
        }

        $this->Database
            ->prepare("UPDATE tl_metamodel_attribute SET type='translatedtablemulti' WHERE type='translatedmulti'")
            ->execute();

        $sql = "UPDATE tl_metamodel_rendersetting 
SET template='mm_attr_translatedtablemulti' 
WHERE template='mm_attr_translatedmulti'";

        $this->Database
            ->prepare($sql)
            ->execute();
    }
}
