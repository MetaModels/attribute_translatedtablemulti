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
 * @package    MetaModels/attribute_translatedtablemulti
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtablemulti/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace MetaModels\AttributeTranslatedTableMultiBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use RuntimeException;

/**
 * Change the database table name from "tl_metamodel_translatedmulti" to "tl_metamodel_translatedtablemulti".
 */
class ChangeTranslatedTableNameMigration extends AbstractMigration
{
    /**
     * The database connection.
     *
     * @var Connection
     */
    private Connection $connection;

    /**
     * Create a new instance.
     *
     * @param Connection $connection The database connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Change table name in MetaModels attribute translatedtablemulti.';
    }

    /**
     * Must only run if:
     * - the MM tables tl_metamodel_translatedmulti exist.
     *
     * @return bool
     *
     * @throws RuntimeException If the old table not migrated.
     */
    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();
        if (
            $schemaManager->tablesExist(['tl_metamodel_translatedmulti'])
            && $schemaManager->tablesExist(['tl_metamodel_translatedtablemulti'])
        ) {
            $error  = 'Could not migrate attribute_translatedtablemulti.';
            $error .= ' Reason: There are both tables available.';
            $error .= ' Old table: tl_metamodel_translatedmulti | New table: attribute_translatedtablemulti.';
            $error .= ' Please migrate the tables manually or delete one table.';
            throw new RuntimeException($error);
        }

        if ($schemaManager->tablesExist(['tl_metamodel_translatedmulti'])) {
            return true;
        }

        return false;
    }

    /**
     * Change table name tl_metamodel_translatedmulti.
     *
     * @return MigrationResult
     */
    public function run(): MigrationResult
    {
        $schemaManager = $this->connection->createSchemaManager();

        if ($schemaManager->tablesExist(['tl_metamodel_translatedmulti'])) {
            $schemaManager->renameTable('tl_metamodel_translatedmulti', 'tl_metamodel_translatedtablemulti');

            $this->connection->createQueryBuilder()
                ->update('tl_metamodel_attribute', 't')
                ->set('t.type', ':new_name')
                ->where('t.type=:old_name')
                ->setParameter('new_name', 'translatedtablemulti')
                ->setParameter('old_name', 'translatedmulti')
                ->executeQuery();

            $this->connection->createQueryBuilder()
                ->update('tl_metamodel_rendersetting', 't')
                ->set('t.template', ':new_name')
                ->where('t.template=:old_name')
                ->setParameter('new_name', 'mm_attr_translatedtablemulti')
                ->setParameter('old_name', 'mm_attr_translatedmulti')
                ->executeQuery();

            return new MigrationResult(true, 'Rename table tl_metamodel_multi to tl_metamodel_translatedtablemulti.');
        }

        return new MigrationResult(false, '');
    }
}
