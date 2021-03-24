<?php
/**
 *  B2B Registration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright © Copyright 2020 - All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  FMM Modules
 * @package   b2bregistration
 */

class AdminB2BCustomFieldsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'b2b_custom_fields';
        $this->className = 'BToBCustomFields';
        $this->identifier = 'id_b2b_custom_fields';
        $this->list_simple_header = false;
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->position_identifier = 'position';
        $this->_orderBy = 'position';
        $this->bulk_actions = array(
            'delete' => array(
                'text' => 'Delete selected',
                'confirm' => 'Delete selected items?',
                'icon' => 'icon-trash',
            ),
        );
        parent::__construct();
        $this->context = Context::getContext();

        $this->fields_list = array(
            'id_b2b_custom_fields' => array(
                'title' => $this->l('ID'),
                'width' => 'auto',
                'orderby' => true,
                'filter_key' => 'id_b2b_custom_fields',
            ),
            'b2b_field_name' => array(
                'title' => $this->l('Field Label'),
                'maxlength' => 30,
                'filter_key' => 'b2b_field_name',
            ),
            'b2b_field_type' => array(
                'title' => $this->l('Field Type'),
                'maxlength' => 30,
                'orderby' => false,
                'filter_key' => 'b2b_field_type',
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'active',
            ),
            'position' => array(
                'title' => 'Position',
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'position' => 'position',
            ),
        );
    }

    public function postProcess()
    {
        parent::postProcess();
    }

    protected function beforeAdd($object)
    {
        if (empty($object->position) || !BToBCustomFields::positionOccupied($object->position)) {
            $object->position = BToBCustomFields::getHigherPosition() + 1;
        }
        parent::beforeAdd($object);
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $switch = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';
        $options = array(
            array(
                'id_option' => 'text',
                'name' => 'text',
            ),
            array(
                'id_option' => 'textarea',
                'name' => 'textarea',
            ),
        );
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('B2B Custom Fileds'),
                'icon' => 'icon-globe',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Field Label'),
                    'name' => 'b2b_field_name',
                    'lang' => true,
                    'col' => '4',
                    'required' => true,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Field Type'),
                    'name' => 'b2b_field_type',
                    //'desc' => $this->l('Please Eneter Web Site URL Address.'),
                    'options' => array(
                        'query' => $options,
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => $switch,
                    'label' => $this->l('Enable Field'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                    'hint' => $this->l('Enable or disable custom field.'),
                ),
                array(
                    'type' => $switch,
                    'label' => $this->l('Field Required'),
                    'name' => 'field_required',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'field_required_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'field_required_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
            ),
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }
        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
            'class' => 'button pull-right',
        );
        return parent::renderForm();
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int) (Tools::getValue('way'));
        $id_b2b_custom_fields = (int) (Tools::getValue('id'));
        $positions = Tools::getValue('b2b_custom_fields');
        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $id_b2b_custom_fields) {
                if ($field = new BToBCustomFields((int) $pos[2])) {
                    if (isset($position) && $field->updatePosition($way, $position)) {
                        echo 'ok position ' . (int) $position . ' for field ' . (int) $pos[1] . '\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update field ' .
                        (int) $id_b2b_custom_fields . ' to position ' . (int) $position . ' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This field (' .
                    (int) $id_b2b_custom_fields . ') can t be loaded"}';
                }
                break;
            }
        }
    }

    public function processPosition()
    {
        if (Tools::getIsset('update' . $this->table)) {
            $object = new BToBCustomFields((int) Tools::getValue('id_b2b_custom_fields'));
            self::$currentIndex = self::$currentIndex . '&update' . $this->table;
        } else {
            $object = new BToBCustomFields((int) Tools::getValue('id'));
        }
        if (!Validate::isLoadedObject($object)) {
            $this->errors[] = $this->l('An error occurred while updating the status for an object.') .
            $this->table . ' ' . $this->l('(cannot load object)');
        } elseif (!$object->updatePosition((int) Tools::getValue('way'), (int) Tools::getValue('position'))) {
            $this->errors[] = $this->l('Failed to update the position.');
        } else {
            $id_identifier_str = ($id_identifier = (int) Tools::getValue($this->identifier)) ? '&' .
            $this->identifier . '=' . $id_identifier : '';
            $redirect = self::$currentIndex . '&' .
            $this->table . 'Orderby=position&' . $this->table .
            'Orderway=asc&conf=5' . $id_identifier_str . '&token=' . $this->token;
            $this->redirect_after = $redirect;
        }
        return $object;
    }
}
