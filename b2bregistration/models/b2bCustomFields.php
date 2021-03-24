<?php
/**
 * B2B Registration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2017 © fmemodules All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  FMM Modules
 * @package   b2bregistration
 */

class BToBCustomFields extends ObjectModel
{
    public $id_b2b_custom_fields;
    public $b2b_field_name;
    public $b2b_field_type;
    public $position;
    public $field_required;
    public $active;

    public static $definition = array(
        'table' => 'b2b_custom_fields',
        'primary' => 'id_b2b_custom_fields',
        'multilang_shop' => true,
        'multilang' => true,
        'fields' => array(
            'id_b2b_custom_fields' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'b2b_field_name' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
            'b2b_field_type' => array('type' => self::TYPE_STRING),
            'field_required' => array('type' => self::TYPE_BOOL),
            'position' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_BOOL),
        ),
    );

    //Positions
    public static function positionOccupied($position)
    {
        if (!$position) {
            return false;
        }
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('b2b_custom_fields');
        $sql->where('position=' . (int) $position);
        return (bool) DB::getInstance()->getRow($sql);
    }

    public static function getHigherPosition()
    {
        $sql = new DbQuery();
        $sql->select('MAX(`position`)');
        $sql->from('b2b_custom_fields');
        $position = DB::getInstance()->getValue($sql);
        return (is_numeric($position)) ? $position : -1;
    }

    public function updatePosition($way, $position)
    {
        $sql = new DbQuery();
        $sql->select('`id_b2b_custom_fields`, `position`');
        $sql->from('b2b_custom_fields');
        $sql->where('id_b2b_custom_fields=' . (int) Tools::getValue('id'));
        $sql->orderby('`position` ASC');
        $res = Db::getInstance()->executeS($sql);
        if (!$res) {
            return false;
        }
        foreach ($res as $field) {
            if ((int) $field['id_b2b_custom_fields'] == (int) $this->id) {
                $moved_field = $field;
            }
        }

        if (!isset($moved_field) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'b2b_custom_fields`
            SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
            WHERE `position`
            ' . ($way
            ? '> ' . (int) $moved_field['position'] . ' AND `position` <= ' . (int) $position
            : '< ' . (int) $moved_field['position'] . ' AND `position` >= ' . (int) $position))
        && Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'b2b_custom_fields`
            SET `position` = ' . (int) $position . '
            WHERE `id_b2b_custom_fields` = ' . (int) $moved_field['id_b2b_custom_fields']);
    }

    public static function selectCustomFields($id_lang)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('b2b_custom_fields', 'f');
        $sql->leftJoin('b2b_custom_fields_lang', 'fl', 'fl.`id_b2b_custom_fields` = f.`id_b2b_custom_fields`');
        $sql->where('fl.id_lang=' . (int) $id_lang);
        $sql->where('f.active=1');
        $sql->orderby('f.position');
        return Db::getInstance()->executeS($sql);
    }
}
