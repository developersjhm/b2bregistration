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
 * @copyright © Copyright 2020 - All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  FMM Modules
 * @package   b2bregistration
 */

class AdminB2BCustomersController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'b2bregistration';
        $this->className = 'BusinessAccountModel';
        $this->identifier = 'id_b2bregistration';
        $this->list_simple_header = false;
        $this->lang = false;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->specificConfirmDelete = false;
        $this->_filterHaving = true;
        parent::__construct();

        $titles_array = array();
        $genders = Gender::getGenders($this->context->language->id);
        foreach ($genders as $gender) {
            /* @var Gender $gender */
            $titles_array[$gender->id_gender] = $gender->name;
        }
        $this->_select = 'a.active as status, b.*, c.city, c.vat_number, c.address1, c.alias, gl.name as title';
        $this->_join = '
        INNER JOIN `' . _DB_PREFIX_ . 'customer` b ON (b.`id_customer` = a.`id_customer`)
        LEFT JOIN `' . _DB_PREFIX_ . 'address` c ON c.id_customer = b.id_customer
        LEFT JOIN ' . _DB_PREFIX_ .
        'gender_lang gl ON (b.id_gender = gl.id_gender AND gl.id_lang = ' .
        (int) $this->context->language->id . ')
        ';
        $this->context = Context::getContext();
        $this->fields_list = array(
            'id_b2bregistration' => array(
                'title' => $this->l('ID'),
                'width' => 'auto',
                'orderby' => true,
                'filter_key' => 'a!id_b2bregistration',
            ),
            'title' => array(
                'title' => $this->l('Social title'),
                'filter_key' => 'b!id_gender',
                'type' => 'select',
                'list' => $titles_array,
                'filter_type' => 'int',
                'order_key' => 'gl!name',
            ),
            'firstname' => array(
                'title' => $this->l('First name'),
                'maxlength' => 30,
                'filter_key' => 'b!firstname',
            ),
            'lastname' => array(
                'title' => $this->l('Last name'),
                'maxlength' => 30,
                'orderby' => false,
                'filter_key' => 'b!lastname',
            ),
            'email' => array(
                'title' => $this->l('Email address'),
                'maxlength' => 50,
                'filter_key' => 'b!email',
            ),
            'status' => array(
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active',
            ),
            'company' => array(
                'title' => $this->l('Company'),
                'filter_key' => 'b!company',
            ),
            'siret' => array(
                'title' => $this->l('Identification Number'),
                'filter_key' => 'b!siret',
            ),
            'address1' => array(
                'title' => $this->l('Address'),
                'filter_key' => 'c!address1',
            ),
            'city' => array(
                'title' => $this->l('City'),
                'filter_key' => 'c!city',
            ),
        );
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $dashboard_link = $this->context->link->getAdminLink('AdminModules') .
        '&configure=' .
        $this->module->name .
        '&tab_module=' .
        $this->module->tab .
        '&module_name=' .
        $this->module->name;
        $this->tpl_list_vars['dashboard_link'] = $dashboard_link;
        return parent::renderList();
    }

    public function renderForm()
    {
        $switch = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        if ($obj->id_customer) {
            $b2b = BusinessAccountModel::getB2BCustomer($this->context->language->id, $obj->id_customer);
            $fields_custom = BToBFieldsData::getCustomFieldsData((int) $obj->id_customer);
        }
        $name_suffix = explode(',', Configuration::get(
            'B2BREGISTRATION_NAME_SUFFIX_OPTIONS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $enable_custom = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $custom_fields = new BToBCustomFields();
        $fields = $custom_fields->selectCustomFields($this->context->language->id);
        $this->context->smarty->assign('name_suffix', $name_suffix);
        $genders = Gender::getGenders();
        $list_genders = array();
        foreach ($genders as $key => $gender) {
            /* @var Gender $gender */
            $list_genders[$key]['id'] = 'gender_' . $gender->id;
            $list_genders[$key]['value'] = $gender->id;
            $list_genders[$key]['label'] = $gender->name;
        }
        $years = Tools::dateYears();
        $months = Tools::dateMonths();
        $days = Tools::dateDays();
        $groups = Group::getGroups($this->default_form_language, true);
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Customer'),
                'icon' => 'icon-user',
            ),
            'input' => array(
                array(
                    'type' => $switch,
                    'label' => $this->l('Enabled Customer'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'actives_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'actives_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                    'hint' => $this->l('Enable or disable customer login.'),
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Social title'),
                    'name' => 'id_gender',
                    'required' => false,
                    'class' => 't',
                    'values' => $list_genders,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('First name'),
                    'name' => 'firstname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:') . ' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Middle name'),
                    'name' => 'middle_name',
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:') . ' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Last name'),
                    'name' => 'lastname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:') . ' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Company'),
                    'name' => 'company',
                    'col' => '4',
                    'required' => true,
                ),
                array(
                    'type' => 'name_suffix',
                    'label' => $this->l('Name Suffix'),
                    'name' => 'name_suffix',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('SIRET'),
                    'name' => 'siret',
                    'col' => '4',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Website'),
                    'name' => 'website',
                    'col' => '4',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Address Alias'),
                    'name' => 'alias',
                    'col' => '4',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Address'),
                    'name' => 'address1',
                    'col' => '4',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('City'),
                    'name' => 'city',
                    'col' => '4',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Vat Number'),
                    'name' => 'vat_number',
                    'col' => '4',
                    //'required' => true,
                ),
                array(
                    'type' => 'text',
                    'prefix' => '<i class="icon-envelope-o"></i>',
                    'label' => $this->l('Email address'),
                    'name' => 'email',
                    'col' => '4',
                    'required' => true,
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'password',
                    'label' => $this->l('Password'),
                    'name' => 'passwd',
                    'required' => true,
                    'col' => '4',
                ),
                array(
                    'type' => 'birthday',
                    'label' => $this->l('Birthday'),
                    'name' => 'birthday',
                    'required' => true,
                    'options' => array(
                        'days' => $days,
                        'months' => $months,
                        'years' => $years,
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Default customer group'),
                    'name' => 'id_default_group',
                    'options' => array(
                        'query' => $groups,
                        'id' => 'id_group',
                        'name' => 'name',
                    ),
                    'col' => '4',
                    'hint' => array(
                        $this->l('This group will be the user\'s default group.'),
                        $this->l('Only the discount for the selected group will be applied to this customer.'),
                    ),
                ),
                array(
                    'type' => $switch,
                    'label' => $this->l('Enabled Newsletter'),
                    'name' => 'newsletter',
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
                    'hint' => $this->l('Enable or disable customer login.'),
                ),
                array(
                    'type' => $switch,
                    'label' => $this->l('Partner offers'),
                    'name' => 'optin',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'optin_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'optin_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                    'disabled' => (bool) !Configuration::get('PS_CUSTOMER_OPTIN'),
                    'hint' => $this->l('This customer will receive your ads via email.'),
                ),
                array(
                    'type' => 'custom_fields',
                    'name' => 'custom_fields',
                ),
            ),
        );
        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        $birthday = explode('-', $this->getFieldValue($obj, 'birthday'));
        $this->context->smarty->assign('enable_custom', $enable_custom);
        $this->context->smarty->assign('custom_fields', $fields);
        if (!empty($b2b) || !empty($fields_custom)) {
            $this->context->smarty->assign('selected_name_sufix', $b2b['name_suffix']);
            $this->context->smarty->assign('id_customer', $obj->id_customer);
            $this->context->smarty->assign('fields_custom', $fields_custom);
            $birthday = explode('-', $b2b['birthday']);
            $this->fields_value = array(
                'years' => $birthday[0],
                'months' => $birthday[1],
                'days' => $birthday[2],
                'name_suffixes' => $b2b['name_suffix'],
                'id_customer' => $b2b['id_customer'],
                'firstname' => $b2b['firstname'],
                'lastname' => $b2b['lastname'],
                'email' => $b2b['email'],
                'passwd' => $b2b['passwd'],
                'active' => $b2b['active'],
                'id_gender' => $b2b['id_gender'],
                'id_default_group' => $b2b['id_default_group'],
                'birthday' => $b2b['birthday'],
                'optin' => $b2b['optin'],
                'newsletter' => $b2b['newsletter'],
                'website' => $b2b['website'],
                'siret' => $b2b['siret'],
                'company' => $b2b['company'],
                'alias' => $b2b['alias'],
                'address1' => $b2b['address1'],
                'vat_number' => $b2b['vat_number'],
                'city' => $b2b['city'],
            );
        } else {
            $this->fields_value = array(
                'years' => $this->getFieldValue($obj, 'birthday') ? $birthday[0] : 0,
                'months' => $this->getFieldValue($obj, 'birthday') ? $birthday[1] : 0,
                'days' => $this->getFieldValue($obj, 'birthday') ? $birthday[2] : 0,
            );
        }
        $tab_link = $this->context->link->getAdminLink('AdminModules') .
        '&configure=' .
        $this->module->name .
        '&tab_module=' .
        $this->module->tab .
        '&module_name=' .
        $this->module->name;
        $this->context->smarty->assign('tab_link', $tab_link);
        return parent::renderForm();
    }

    public function postProcess()
    {
        $id_b2bregistration = (int) Tools::getValue('id_b2bregistration');
        $id_fields = Tools::getValue('id_fields');
        $default_country = (int) Configuration::get(
            'PS_COUNTRY_DEFAULT',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_email = (int) Configuration::get(
            'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $email_sender = pSQL(Configuration::get(
            'B2BREGISTRATION_ADMIN_EMAIL_SENDER',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $name_suffix = explode(',', Configuration::get(
            'B2BREGISTRATION_NAME_SUFFIX_OPTIONS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $auto_approvel = (int) Configuration::get(
            'B2BREGISTRATION_AUTO_APPROVEL',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        if (Tools::isSubmit('status' . $this->table)) {
            $loaded_obj = $this->loadObject(true);
            $customer = new Customer($loaded_obj->id_customer);
            if ($loaded_obj->active == 0) {
                $customer->active = 1;
            } else {
                $customer->active = 0;
            }
            $res = $customer->update();
            if ($res == true) {
                $subject = Mail::l('B2B Registration Approvel');
                $templateVars = array(
                    '{first_name}' => $customer->firstname,
                    '{last_name}' => $customer->lastname,
                    '{email}' => $customer->email,
                );
                if ($customer->active == 1) {
                    $template_name = 'b2b_activated';
                } else {
                    $template_name = 'b2b_customer_pending';
                }
                $title = $subject;
                $from = Configuration::get('PS_SHOP_EMAIL');
                $email_sender = Configuration::get('PS_SHOP_NAME');
                $fromName = $email_sender;

                $mailDir = _PS_MODULE_DIR_ . 'b2bregistration/mails/';
                $toName = $customer->firstname;

                $send = Mail::Send(
                    Context::getContext()->language->id,
                    $template_name,
                    $title,
                    $templateVars,
                    $customer->email,
                    $toName,
                    $from,
                    $fromName,
                    null,
                    null,
                    $mailDir
                );
            }
        }
        if (Tools::isSubmit('submitAddb2bregistration')) {
            $result = false;
            if ($id_b2bregistration > 0) {
                $obj = $this->loadObject(true);
                $customers = new Customer((int) $obj->id_customer);
                $customers->delete();
                BusinessAccountModel::extraFieldsDeletion((int) $obj->id_customer);
                BToBFieldsData::customFieldsDeletion((int) $obj->id_customer);
            }
            $enable_custom = (int) Configuration::get(
                'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $id_gender = (int) Tools::getValue('id_gender');
            $firstname = pSQL(Tools::getValue('firstname'));
            $lastname = pSQL(Tools::getValue('lastname'));
            $middlename = pSQL(Tools::getValue('middle_name'));
            $company = pSQL(Tools::getValue('company'));
            $siret = pSQL(Tools::getValue('siret'));
            $website = pSQL(Tools::getValue('website'));
            $alias = pSQL(Tools::getValue('alias'));
            $address1 = pSQL(Tools::getValue('address1'));
            $city = pSQL(Tools::getValue('city'));
            $vat = pSQL(Tools::getValue('vat_number'));
            $email = pSQL(Tools::getValue('email'));
            $password = pSQL(Tools::getValue('passwd'));
            $name_suffix = pSQL(Tools::getValue('name_suffix'));
            $day = Tools::getValue('days');
            $id_default_group = (int) Tools::getValue('id_default_group');
            $month = Tools::getValue('months');
            $year = Tools::getValue('years');
            $optin = (int) Tools::getValue('optin');
            $newsletter = (int) Tools::getValue('newsletter');
            $birthdate = $year . "-" . $month . "-" . $day;
            $passwd = Tools::encrypt($password);
            if ($auto_approvel == 1) {
                $active = 1;
            } else {
                $active = (bool) Tools::getValue('active');
            }
            $customer = new Customer();
            if (empty($firstname)) {
                $this->context->controller->errors[] = $this->l('Enter First Name');
            } elseif (!Validate::isName($firstname)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter valid first name'
                );
            } elseif (empty($lastname)) {
                $this->context->controller->errors[] = $this->l('Enter Last Name');
            } elseif (!Validate::isName($lastname)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter valid last name'
                );
            } elseif (empty($company)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter company name'
                );
            } elseif (empty($siret)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter SIRET/identification number'
                );
            } elseif (!Validate::isSiret($siret) && _PS_VERSION_ < '1.7.0.0') {
                $this->context->controller->errors[] = $this->l(
                    'Please enter valid SIRET/identification number'
                );
            } elseif (empty($website)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter website link'
                );
            } elseif (empty($alias)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter address alias e.g Home '
                );
            } elseif (empty($address1)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter address'
                );
            } elseif (!Validate::isAddress($address1)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter valid address'
                );
            } elseif (empty($city)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter city'
                );
            } elseif (!Validate::isCityName($city)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter valid city name'
                );
            } elseif (empty($email)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter email address'
                );
            } elseif (!Validate::isEmail($email)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter valid email address'
                );
            } elseif ($customer->customerExists($email, false, true)) {
                $this->context->controller->errors[] = $this->l(
                    'Email Already Exists. Please choose another one'
                );
            } elseif (empty($password)) {
                $this->context->controller->errors[] = $this->l(
                    'Please enter password'
                );
            } elseif (!Validate::isPasswd($password)) {
                $this->context->controller->errors[] = $this->l(
                    'Password length must be 5 or greater'
                );
            } elseif (empty($day) && empty($month) && empty($year)) {
                $this->context->controller->errors[] = $this->l(
                    'Please select birthdate'
                );
            } else {
                $customer->id_shop = $this->context->shop->id;
                $customer->id_shop_group = $this->context->shop->id_shop_group;
                $customer->id_default_group = $id_default_group;
                $customer->id_lang = $this->context->language->id;
                $customer->id_gender = $id_gender;
                $customer->firstname = $firstname;
                $customer->lastname = $lastname;
                $customer->birthday = $birthdate;
                $customer->email = $email;
                $customer->newsletter = $newsletter;
                $customer->optin = $optin;
                $customer->website = $website;
                $customer->company = $company;
                $customer->siret = $siret;
                $customer->passwd = $passwd;
                $customer->active = $active;
                $res = $customer->save();
                $result = true;
                if ($res) {
                    $addres = new Address();
                    $addres->id_customer = (int) $customer->id;
                    $addres->company = $company;
                    $addres->id_country = $default_country;
                    $addres->firstname = $firstname;
                    $addres->lastname = $lastname;
                    $addres->vat_number = $vat;
                    $addres->address1 = $address1;
                    $addres->alias = $alias;
                    $addres->city = $city;
                    $addres->dni = $siret;
                    $addres->save();
                    $b2b = new BusinessAccountModel();
                    $b2b->id_customer = (int) $customer->id;
                    $b2b->flag = 1;
                    $b2b->active = $active;
                    $b2b->middle_name = $middlename;
                    $b2b->name_suffix = $name_suffix;
                    $b2b->save();
                    $result = true;
                    if (!empty($id_fields) && $enable_custom) {
                        $data = new BToBFieldsData();
                        foreach ($id_fields as $field_id) {
                            $fields = Tools::getValue("field_" . (int) $field_id);
                            $label = Tools::getValue("label_" . (int) $field_id);
                            $data->b2b_field_name = $fields;
                            $data->b2b_field_title = $label;
                            $data->id_customer = (int) $customer->id;
                            $data->id_field = (int) $field_id;
                            $data->add();
                            $result = true;
                        }
                    }
                }
                if ($enable_email == 1 && $result == true) {
                    $subject = Mail::l('Customer Registration By Admin');
                    $templateVars = array(
                        '{first_name}' => $firstname,
                        '{last_name}' => $lastname,
                        '{company_name}' => $company,
                        '{website}' => $website,
                        '{email}' => $email,
                    );
                    $template_name = 'b2b_customer_registration';
                    $title = $subject;
                    $from = Configuration::get('PS_SHOP_EMAIL');
                    if ($email_sender == "") {
                        $email_sender = Configuration::get('PS_SHOP_NAME');
                    }
                    $fromName = $email_sender;
                    $mailDir = _PS_MODULE_DIR_ . 'b2bregistration/mails/';
                    $toName = $firstname;
                    $send = Mail::Send(
                        Context::getContext()->language->id,
                        $template_name,
                        $title,
                        $templateVars,
                        $email,
                        $toName,
                        $from,
                        $fromName,
                        null,
                        null,
                        $mailDir
                    );
                    if ($send) {
                        $this->context->controller->confirmation[] = $this->l(
                            'Email sent successfully'
                        );
                    }
                }
            }
        }
        if (Tools::isSubmit('deleteb2bregistration')) {
            $obj = $this->loadObject(true);
            if ($obj->id_customer) {
                $customer = new Customer((int) $obj->id_customer);
                $customer->delete();
                BToBFieldsData::customFieldsDeletion((int) $obj->id_customer);
            }
        }
        parent::postProcess();
        BusinessAccountModel::deleteNotCustomer();
    }
}
