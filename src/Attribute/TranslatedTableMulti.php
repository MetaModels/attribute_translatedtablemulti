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
 * @author     David Maack <david.maack@arcor.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtablemulti/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeTranslatedTableMultiBundle\Attribute;

use Contao\System;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use MetaModels\Attribute\Base;
use MetaModels\Attribute\IComplex;
use MetaModels\Attribute\ITranslated;
use MetaModels\IMetaModel;
use Doctrine\DBAL\Connection;
use MetaModels\ITranslatedMetaModel;

/**
 * This is the MetaModelAttribute class for handling table text fields.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class TranslatedTableMulti extends Base implements ITranslated, IComplex
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private Connection $connection;

    /**
     * Instantiate an MetaModel attribute.
     *
     * Note that you should not use this directly but use the factory classes to instantiate attributes.
     *
     * @param IMetaModel      $objMetaModel The MetaModel instance this attribute belongs to.
     * @param array           $arrData      The information array, for attribute information, refer to documentation of
     *                                      table tl_metamodel_attribute and documentation of the certain attribute
     *                                      classes for information what values are understood.
     * @param Connection|null $connection   Database connection.
     */
    public function __construct(IMetaModel $objMetaModel, array $arrData = [], Connection $connection = null)
    {
        parent::__construct($objMetaModel, $arrData);

        if (null === $connection) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'Connection is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd
            $connection = System::getContainer()->get('database_connection');
            assert($connection instanceof Connection);
        }
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributeSettingNames()
    {
        return \array_merge(parent::getAttributeSettingNames(), []);
    }

    /**
     * Retrieve the table name containing the values.
     *
     * @return string
     */
    protected function getValueTable()
    {
        return 'tl_metamodel_translatedtablemulti';
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getFieldDefinition($arrOverrides = [])
    {
        // Get table and column
        $strTable = $this->getMetaModel()->getTableName();
        $strField = $this->getColName();

        $arrFieldDef                         = parent::getFieldDefinition($arrOverrides);
        $arrFieldDef['inputType']            = 'multiColumnWizard';
        $arrFieldDef['eval']['columnFields'] = [];

        // Check for override in local config
        if (isset($GLOBALS['TL_CONFIG']['metamodelsattribute_multi'][$strTable][$strField])) {
            // Retrieve the config.
            $config = $GLOBALS['TL_CONFIG']['metamodelsattribute_multi'][$strTable][$strField];

            // Add CSS class.
            if (!empty($arrFieldDef['eval']['tl_class'])) {
                $config['tl_class'] = isset($config['tl_class'])
                    ? $config['tl_class'] . ' ' . $arrFieldDef['eval']['tl_class']
                    : $arrFieldDef['eval']['tl_class'];
            }

            // Hide buttons if readonly.
            if (!empty($arrFieldDef['eval']['readonly'])) {
                $config['hideButtons'] = true;
            }

            // Add field configs.
            foreach ($config['columnFields'] as $col => $data) {
                $config['columnFields']['col_' . $col] = $data;
                unset($config['columnFields'][$col]);

                // Add readonly and delete picker and wizard class.
                if (!empty($arrFieldDef['eval']['readonly'])) {
                    $config['columnFields']['col_' . $col]['eval']['readonly'] = true;
                    unset(
                        $config['columnFields']['col_' . $col]['eval']['dcaPicker'],
                        $config['columnFields']['col_' . $col]['eval']['datepicker'],
                        $config['columnFields']['col_' . $col]['eval']['colorpicker']
                    );
                    $config['columnFields']['col_' . $col]['eval']['tl_class'] =
                        \str_replace('wizard', '', $config['columnFields']['col_' . $col]['eval']['tl_class'] ?? '');
                }
            }

            // Append the eval config.
            $arrFieldDef['eval'] = $config;
        }

        return $arrFieldDef;
    }

    /**
     * Build the where clause.
     *
     * @param QueryBuilder       $queryBuilder The query builder.
     * @param null|list<string>|string $mixIds       One, none or many ids to use.
     * @param string|null        $strLangCode  The language code, optional.
     * @param int|null           $intRow       The row number, optional.
     * @param string|null        $varCol       The col number, optional.
     * @param string             $tableAlias   The table alias, optional.
     *
     * @return void
     */
    protected function buildWhere(
        QueryBuilder $queryBuilder,
        $mixIds,
        $strLangCode = null,
        $intRow = null,
        $varCol = null,
        $tableAlias = ''
    ): void {
        if ('' !== $tableAlias) {
            $tableAlias .= '.';
        }

        $queryBuilder
            ->andWhere($tableAlias . 'att_id = :att_id')
            ->setParameter('att_id', (int) $this->get('id'));

        if (\is_int($intRow) && \is_string($varCol)) {
            $queryBuilder
                ->andWhere($tableAlias . 'row = :row AND ' . $tableAlias . 'col = :col')
                ->setParameter('row', $intRow)
                ->setParameter('col', $varCol);
        }

        if ('' !== $strLangCode && null !== $strLangCode) {
            $queryBuilder
                ->andWhere($tableAlias . 'langcode = :langcode')
                ->setParameter('langcode', $strLangCode);
        }

        if (null === $mixIds) {
            return;
        }
        if (\is_array($mixIds)) {
            if ([] === $mixIds) {
                return;
            }

            $queryBuilder
                ->andWhere($tableAlias . 'item_id IN (:item_ids)')
                ->setParameter('item_ids', $mixIds, ArrayParameterType::STRING);
            return;
        }
        $queryBuilder
            ->andWhere($tableAlias . 'item_id = :item_id')
            ->setParameter('item_id', $mixIds);
    }

    /**
     * {@inheritdoc}
     */
    public function valueToWidget($varValue)
    {
        if (!\is_array($varValue)) {
            return [];
        }

        $widgetValue = array();
        foreach ($varValue as $row) {
            foreach ($row as $col) {
                $widgetValue[$col['row']]['col_' . $col['col']] = $col['value'];
            }
        }

        return $widgetValue;
    }

    /**
     * {@inheritdoc}
     */
    public function widgetToValue($varValue, $itemId)
    {
        if (!\is_array($varValue)) {
            return null;
        }

        $newValue = array();
        foreach ($varValue as $k => $row) {
            foreach ($row as $kk => $col) {
                $kk = \substr($kk, 4);

                $newValue[$k][$kk]['value'] = $col;
                $newValue[$k][$kk]['col']   = $kk;
                $newValue[$k][$kk]['row']   = $k;
            }
        }

        return $newValue;
    }

    /**
     * Retrieve the setter array.
     *
     * @param array  $arrCell     The cells of the table.
     * @param string $intId       The id of the item.
     * @param string $strLangCode The language code.
     *
     * @return array
     */
    protected function getSetValues($arrCell, $intId, $strLangCode)
    {
        return array(
            'tstamp'   => time(),
            'value'    => (string) $arrCell['value'],
            'att_id'   => $this->get('id'),
            'row'      => (int) $arrCell['row'],
            'col'      => $arrCell['col'],
            'item_id'  => $intId,
            'langcode' => $strLangCode,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslatedDataFor($arrIds, $strLangCode)
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->getValueTable(), 't')
            ->orderBy('t.row', 'ASC')
            ->addOrderBy('t.col', 'ASC');

        $this->buildWhere($queryBuilder, $arrIds, $strLangCode, null, null, 't');
        $statement = $queryBuilder->executeQuery();
        $arrReturn = [];
        while ($value = $statement->fetchAssociative()) {
            $arrReturn[$value['item_id']][$value['row']][$value['col']] = $value;
        }

        return $arrReturn;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function searchForInLanguages($strPattern, $arrLanguages = [])
    {
        return [];
    }


    /**
     * {@inheritDoc}
     */
    public function setTranslatedDataFor($arrValues, $strLangCode)
    {
        // Get the ids.
        $arrIds = \array_keys($arrValues);

        // Reset all data for the ids in language.
        $this->unsetValueFor($arrIds, $strLangCode);

        foreach ($arrIds as $itemId) {
            // Walk every row.
            foreach ($arrValues[$itemId] ?? [] as $row) {
                // Walk every column and update / insert the value.
                foreach ($row as $col) {
                    $values = $this->getSetValues($col, $itemId, $strLangCode);
                    if ($values['value'] === '') {
                        continue;
                    }

                    $queryBuilder = $this->connection->createQueryBuilder()->insert($this->getValueTable());
                    foreach ($values as $name => $value) {
                        $queryBuilder
                            ->setValue($this->getValueTable() . '.' . $name, ':' . $name)
                            ->setParameter($name, $value);
                    }

                    $sql        = $queryBuilder->getSQL();
                    $parameters = $queryBuilder->getParameters();

                    $queryBuilder = $this->connection->createQueryBuilder()->update($this->getValueTable());
                    foreach ($values as $name => $value) {
                        $queryBuilder
                            ->set($this->getValueTable() . '.' . $name, ':' . $name)
                            ->setParameter($name, $value);
                    }

                    $updateSql = $queryBuilder->getSQL();
                    $sql      .= ' ON DUPLICATE KEY ' . str_replace($this->getValueTable() . ' SET ', '', $updateSql);

                    $this->connection->executeQuery($sql, $parameters);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function unsetValueFor($arrIds, $strLangCode)
    {
        $queryBuilder = $this->connection->createQueryBuilder()->delete($this->getValueTable());
        $this->buildWhere($queryBuilder, $arrIds, $strLangCode, null, null, $this->getValueTable());
        $queryBuilder->executeQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getFilterOptions($idList, $usedOnly, &$arrCount = null)
    {
        return [];
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setDataFor($arrValues)
    {
        $metamodel = $this->getMetaModel();
        if ($metamodel instanceof ITranslatedMetaModel) {
            $activeLanguage = $metamodel->getLanguage();
        } else {
            // FIXME: trigger deprecation - see other code.
            /** @psalm-suppress DeprecatedMethod */
            $activeLanguage = $this->getMetaModel()->getActiveLanguage();
        }
        $this->setTranslatedDataFor($arrValues, $activeLanguage);
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getDataFor($arrIds)
    {
        $metamodel = $this->getMetaModel();
        if ($metamodel instanceof ITranslatedMetaModel) {
            $strActiveLanguage = $metamodel->getLanguage();
            $strFallbackLanguage = $metamodel->getMainLanguage();
        } else {
            // FIXME: trigger deprecation - see other code.
            /** @psalm-suppress DeprecatedMethod */
            $strActiveLanguage = $this->getMetaModel()->getActiveLanguage();
            /** @psalm-suppress DeprecatedMethod */
            $strFallbackLanguage = $this->getMetaModel()->getFallbackLanguage();
            assert(is_string($strFallbackLanguage));
        }

        $arrReturn = $this->getTranslatedDataFor($arrIds, $strActiveLanguage);

        // Second round, fetch fallback languages if not all items could be resolved.
        if (($strActiveLanguage !== $strFallbackLanguage) && (\count($arrReturn) < \count($arrIds))) {
            $arrFallbackIds = [];
            foreach ($arrIds as $intId) {
                if (null === ($arrReturn[$intId] ?? null)) {
                    $arrFallbackIds[] = $intId;
                }
            }

            if ($arrFallbackIds) {
                $arrFallbackData = $this->getTranslatedDataFor($arrFallbackIds, $strFallbackLanguage);
                // Cannot use array_merge here as it would renumber the keys.
                foreach ($arrFallbackData as $intId => $arrValue) {
                    $arrReturn[$intId] = $arrValue;
                }
            }
        }

        return $arrReturn;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException When the passed value is not an array of ids.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function unsetDataFor($arrIds)
    {
        if ([] === $arrIds) {
            return;
        }

        $queryBuilder = $this->connection->createQueryBuilder()->delete($this->getValueTable());
        $this->buildWhere($queryBuilder, $arrIds);
        $queryBuilder->executeQuery();
    }
}
