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

class BusinessAccountModel extends ObjectModel
{
    public $id_b2bregistration;
    public $id_customer;
    public $middle_name;
    public $name_suffix;
    public $flag;
    public $active;
    public static $definition = array(
        'table' => 'b2bregistration',
        'primary' => 'id_b2bregistration',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT),
            'middle_name' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'name_suffix' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'flag' => array('type' => self::TYPE_BOOL),
            'active' => array('type' => self::TYPE_BOOL),
        ),
    );

    public static function getAllGenders($id_lang)
    {
        $sql = new DbQuery();
        $sql->select('b.*, a.*');
        $sql->from('gender', 'a');
        $sql->leftJoin('gender_lang', 'b', 'b.`id_gender` = a.`id_gender` AND b.`id_lang` = ' . (int) $id_lang);
        return Db::getInstance()->executeS($sql);
    }

    public static function existsTab($tab_class)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT id_tab AS id
            FROM `' . _DB_PREFIX_ . 'tab` t
            WHERE LOWER(t.`class_name`) = \'' . pSQL($tab_class) . '\'');

        if (count($result) == 0) {
            return false;
        }
        return true;
    }

    public static function getB2BCustomer($id_lang, $id_customer)
    {
        $sql = new DbQuery();
        $sql->select('c.*, g.*, a.alias, a.city, a.address1, a.vat_number, shop.*, b.*');
        $sql->from('customer', 'c');
        $sql->leftJoin('gender_lang', 'g', 'c.`id_gender` = g.`id_gender` AND g.`id_lang` = ' . (int) $id_lang);
        $sql->leftJoin('address', 'a', 'a.`id_customer` = c.`id_customer`');
        $sql->leftJoin('shop', 'shop', 'c.`id_shop` = shop.`id_shop`');
        $sql->leftJoin('b2bregistration', 'b', 'c.`id_customer` = b.`id_customer`');
        $sql->where('c.id_customer =' . (int) $id_customer);
        return Db::getInstance()->getRow($sql);
    }

    public static function getB2BCustomers($id_lang, $id_customer)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('customer', 'c');
        $sql->leftJoin('gender_lang', 'g', 'c.`id_gender` = g.`id_gender` AND g.`id_lang` = ' . (int) $id_lang);
        $sql->leftJoin('address', 'a', 'a.`id_customer` = c.`id_customer`');
        $sql->leftJoin('shop', 'shop', 'c.`id_shop` = shop.`id_shop`');
        $sql->leftJoin('b2bregistration', 'b', 'c.`id_customer` = b.`id_customer`');
        $sql->where('c.id_customer =' . (int) $id_customer);
        return Db::getInstance()->ExecuteS($sql);
    }

    public static function getGenderName($id_gender, $id_lang)
    {
        $sql = new DbQuery();
        $sql->select('name');
        $sql->from('gender_lang');
        $sql->where('id_gender=' . $id_gender . ' AND id_lang=' . $id_lang);
        return Db::getInstance()->getRow($sql);
    }

    public static function getRegisteredB2B($id_customer)
    {
        $sql = new DbQuery();
        $sql->select('id_customer, id_b2bregistration');
        $sql->from('b2bregistration');
        $sql->where('id_customer=' . (int) $id_customer);
        return Db::getInstance()->getRow($sql);
    }
    public static function getAllCategories()
    {
        $sql = new DbQuery();
        $sql->select('id_category');
        $sql->from('category');
        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            foreach ($result as &$res) {
                $res = array_shift($res);
            }
        }
        return $result;
    }

    public static function addB2BGroupToCategory($id_category, $id_group)
    {
        if (!BusinessAccountModel::isAlreadyAdded($id_category, $id_group)) {
            return Db::getInstance()->insert(
                'category_group',
                array('id_category' => (int) $id_category,
                    'id_group' => (int) $id_group)
            );
        }
        return true;
    }

    public static function isAlreadyAdded($id_category, $id_group)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('category_group');
        $sql->WHERE('`id_category` = ' . (int) $id_category . ' AND id_group= ' . $id_group);
        return (bool) Db::getInstance()->getRow($sql);
    }

    public static function extraFieldsDeletion($id_customer)
    {
        return Db::getInstance()->delete('b2bregistration', 'id_customer =' . $id_customer);
    }

    public static function deleteNotCustomer()
    {
        return Db::getInstance()->delete('b2bregistration', 'id_customer = 0');
    }

    public static function deleteAddress($id_customer)
    {
        return Db::getInstance()->delete('address', 'id_customer =' . $id_customer);
    }

    public static function getAddress($id_customer)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('address');
        $sql->where('id_customer=' . (int) $id_customer);
        return Db::getInstance()->getRow($sql);
    }

    public static function getBusinessStatus($id_customer)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('b2bregistration');
        $sql->where('id_customer=' . (int) $id_customer);
        return Db::getInstance()->getRow($sql);
    }
    public static function addDefaultValues()
    {
        $context = Context::getContext();
        return (Configuration::updateValue(
            'B2BREGISTRATION_ENABLE_DISABLE',
            true,
            false,
            $context->shop->id_shop_group,
            $context->shop->id
        ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
                false,
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
                false,
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_NAME_SUFFIX_OPTIONS',
                'MD,PHD',
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE',
                false,
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE',
                false,
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
                true,
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
                true,
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE',
                true,
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE',
                false,
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_DOB_ENABLE_DISABLE',
                false,
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
                true,
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
                true,
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
                true,
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            // MultiLang Fields
            Configuration::updateValue(
                'B2BREGISTRATION_URL_KEY',
                array($context->language->id => 'b2b-customer-create'),
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_URL_TEXT',
                array($context->language->id => 'Create Business Account'),
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_PERSONAL_TEXT',
                array($context->language->id => 'Personal Information'),
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_COMPANY_TEXT',
                array($context->language->id => 'Company Information'),
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_SIGNIN_TEXT',
                array($context->language->id => 'Sign in Information'),
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_CUSTOM_FIELD_TEXT',
                array($context->language->id => 'Custom Fields'),
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_ERROR_MSG_TEXT',
                array($context->language->id => 'Your account is pending for validation and will be activated soon'),
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ) &&
            Configuration::updateValue(
                'B2BREGISTRATION_ADDRESS_TEXT',
                array($context->language->id => 'Address Information'),
                false,
                $context->shop->id_shop_group,
                $context->shop->id
            ));
    }

    public static function deleteDefaultValues()
    {
        return (Configuration::deleteByName('B2BREGISTRATION_ENABLE_DISABLE') &&
            Configuration::deleteByName('B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE') &&
            Configuration::deleteByName('B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE') &&
            Configuration::deleteByName('B2BREGISTRATION_NAME_PREFIX_OPTIONS') &&
            Configuration::deleteByName('B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE') &&
            Configuration::deleteByName('B2BREGISTRATION_NAME_SUFFIX_OPTIONS') &&
            Configuration::deleteByName('B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE') &&
            Configuration::deleteByName('B2BREGISTRATION_GROUPS') &&
            Configuration::deleteByName('B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE') &&
            Configuration::deleteByName('B2BREGISTRATION_ADMIN_EMAIL_SENDER') &&
            Configuration::deleteByName('B2BREGISTRATION_ADMIN_EMAIL_ID') &&
            Configuration::deleteByName('B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE') &&
            Configuration::deleteByName('B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE') &&
            Configuration::deleteByName('B2BREGISTRATION_SITE_KEY') &&
            Configuration::deleteByName('B2BREGISTRATION_SECRET_KEY') &&
            Configuration::deleteByName('B2BREGISTRATION_DOB_ENABLE_DISABLE') &&
            Configuration::deleteByName('B2BREGISTRATION_ENABLE_CUSTOM_FIELDS') &&
            Configuration::deleteByName('B2BREGISTRATION_ADDRESS_ENABLE_DISABLE') &&
            Configuration::deleteByName('B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE') &&
            Configuration::deleteByName('B2BREGISTRATION_WEBSITE_ENABLE_DISABLE') &&
            Configuration::deleteByName('B2BREGISTRATION_URL_KEY') &&
            Configuration::deleteByName('B2BREGISTRATION_URL_TEXT') &&
            Configuration::deleteByName('B2BREGISTRATION_PERSONAL_TEXT') &&
            Configuration::deleteByName('B2BREGISTRATION_COMPANY_TEXT') &&
            Configuration::deleteByName('B2BREGISTRATION_SIGNIN_TEXT') &&
            Configuration::deleteByName('B2BREGISTRATION_CMS_PAGES') &&
            Configuration::deleteByName('B2BREGISTRATION_NORMAL_REGISTRATION') &&
            Configuration::deleteByName('B2BREGISTRATION_AUTO_APPROVEL') &&
            Configuration::deleteByName('B2BREGISTRATION_CUSTOM_FIELD_TEXT') &&
            Configuration::deleteByName('B2BREGISTRATION_ERROR_MSG_TEXT') &&
            Configuration::deleteByName('B2BREGISTRATION_ADDRESS_TEXT')
        );
    }
}
