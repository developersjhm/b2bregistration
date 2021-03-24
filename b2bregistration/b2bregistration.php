<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FME Modules
 *  @copyright 2019 FME Modules
 *  @license   Comerical Licence
 *  @package   compositeproductbuilder
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/models/businessAccountModel.php';
include_once dirname(__FILE__) . '/models/b2bCustomFields.php';
include_once dirname(__FILE__) . '/models/b2bFieldsData.php';
class B2bregistration extends Module
{
    protected $config_form = false;
    protected $tab_parent_class = null;
    private $tab_class = 'B2BRegistration';
    private $tab_module = 'b2bregistration';
    public function __construct()
    {
        $this->name = 'b2bregistration';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'FMM Modules';
        $this->need_instance = 0;
        $this->controllers = array('business');
        $this->module_key = '6440dbe808c1bfe3b8a16dfc0ac664ec';
        $this->author_address = '0xcC5e76A6182fa47eD831E43d80Cd0985a14BB095';
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('B2B Registration');
        $this->description = $this->l('Offers a custom signup form for B2B customers or wholesalers');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall my module?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->translations = array(
            'first_name_required' => $this->l('Please enter first name'),
            'first_name_valid' => $this->l('Please enter valid first name'),
            'last_name_required' => $this->l('Please enter last name'),
            'last_name_valid' => $this->l('Please enter valid last name'),
            'address_alias_required' => $this->l('Please enter address alias e.g Home'),
            'address_required' => $this->l('Please enter address'),
            'address_valid' => $this->l('Please enter valid address'),
            'city_required' => $this->l('Please enter city name'),
            'city_valid' => $this->l('Please enter valid city name'),
            'website_required' => $this->l('Please enter website link'),
            'company_required' => $this->l('Please enter company name'),
            'siret_required' => $this->l('Please enter identification/siret number'),
            'siret_valid' => $this->l('Please enter valid identification/siret number'),
            'email_required' => $this->l('Please enter email address'),
            'email_valid' => $this->l('Please enter valid email address'),
            'email_exist' => $this->l('Email already exists. Choose another one'),
            'password_required' => $this->l('Please enter password'),
            'password_valid' => $this->l('Password length must be 5 or greater'),
            'confirm_required' => $this->l('Please enter confirmation password'),
            'confirm_valid' => $this->l('Both password does not match'),
            'invalid_birthday' => $this->l('Please Enter Valid Birth Date (E.g.: 1970-12-31)'),
            'empty_birthday' => $this->l('Please Enter Birth Date (E.g.: 1970-12-31)'),
            'email_send' => $this->l('Email sent successfully'),
            'validate_account' => $this->l('Your account is pending for validation and will be activated soon'),
            'update_account' => $this->l('Your information is updated successfully.'),
        );
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';
        if (!BusinessAccountModel::existsTab($this->tab_class)) {
            if (!$this->addTab($this->tab_class, 0)) {
                return false;
            }
        }
        return parent::install() &&
        $this->registerHook('header') &&
        $this->registerHook('backOfficeHeader') &&
        $this->registerHook('displayNav2') &&
        $this->registerHook('displayNav') &&
        $this->registerHook('ModuleRoutes') &&
        $this->registerHook('actionDeleteGDPRCustomer') &&
        $this->registerHook('registerGDPRConsent') &&
        $this->registerHook('actionExportGDPRData') &&
        $this->registerHook('actionObjectCustomerDeleteAfter') &&
        $this->registerHook('actionObjectCustomerUpdateAfter') &&
        $this->registerHook('displayCustomerAccount') &&
        BusinessAccountModel::addDefaultValues() &&
        $this->createB2BGroup();
    }

    public function uninstall()
    {
        include dirname(__FILE__) . '/sql/uninstall.php';
        if (_PS_VERSION_ < 1.7) {
            $this->removeTab($this->tab_class);
        }
        return parent::uninstall() &&
        $this->deleteB2BGroup() &&
        BusinessAccountModel::deleteDefaultValues();
    }

    protected function addTab($tab_class, $id_parent)
    {
        $tab = new Tab();
        $tab->class_name = $tab_class;
        $tab->id_parent = $id_parent;
        $tab->module = $this->tab_module;
        $tab->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $this->l('B2B Registration');
        $tab->add();

        $subtab1 = new Tab();
        $subtab1->class_name = 'AdminB2BCustomers';
        $subtab1->id_parent = Tab::getIdFromClassName($tab_class);
        $subtab1->module = $this->tab_module;
        $subtab1->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $this->l('Manage B2B Customers');
        $subtab1->add();

        $subtab2 = new Tab();
        $subtab2->class_name = 'AdminB2BCustomFields';
        $subtab2->id_parent = Tab::getIdFromClassName($tab_class);
        $subtab2->module = $this->tab_module;
        $subtab2->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $this->l('Add B2B Custom Fields');
        $subtab2->add();
        return true;
    }

    private function removeTab($tabClass)
    {
        $idTab = Tab::getIdFromClassName($tabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();
            return true;
        }
        return false;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (Tools::getvalue("action") == 'savePrefix') {
            $obj = new Gender();
            $gender = (int) Tools::getValue('gender');
            $languages = Language::getLanguages();
            $obj->type = $gender;
            foreach ($languages as $lang) {
                $prefix_name = pSQL(Tools::getValue("prefix_text_" . $lang['id_lang']));
                $obj->name[$lang['id_lang']] = $prefix_name;
            }
            $result = $obj->save();
            die(json_encode($result));
        }
        if (((bool) Tools::isSubmit('submitB2bregistrationModule')) == true) {
            $this->postProcess();
        }
        $this->context->smarty->assign('module_dir', $this->_path);
        return $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $this->html = $this->display(__FILE__, 'views/templates/hook/info.tpl');
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitB2bregistrationModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $groups = Group::getGroups($this->context->language->id, $this->context->shop->id);
        // List of CMS Pages
        $cms_pages = array();
        foreach (CMS::listCms($this->context->language->id) as $cms_page) {
            $cms_pages[] = array('id' => $cms_page['id_cms'], 'name' => $cms_page['meta_title']);
        }
        $cpGroups = (Configuration::get(
            'B2BREGISTRATION_GROUPS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        )) ? explode(',', Configuration::get(
            'B2BREGISTRATION_GROUPS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        )) : array();
        $cpGender = (Configuration::get(
            'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        )) ? explode(',', Configuration::get(
            'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        )) : array();
        $admin_email_sender = pSQL(Configuration::get(
            'B2BREGISTRATION_ADMIN_EMAIL_SENDER',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $selected_page = pSQL(Configuration::get(
            'B2BREGISTRATION_CMS_PAGES',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $genders = businessAccountModel::getAllGenders($this->context->language->id);
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'groups' => $groups,
            'genders' => $genders,
            'cpGroups' => $cpGroups,
            'cpGender' => $cpGender,
            'selected_page' => $selected_page,
            'cms_pages' => $cms_pages,
            'ps_version' => _PS_VERSION_,
            'admin_email_sender' => $admin_email_sender,
        );

        return $this->html . $helper->generateForm($this->getConfigForm());
    }

    public function init()
    {
        parent::init();
        $this->ajax = (bool) Tools::getValue('ajax', false);
    }

    /**
     * Handle Request for opening fancybox for new prefixes
     */
    public function ajaxProcessOpenPrefixesDialog()
    {
        $languages = Language::getLanguages();
        $defaultFormLanguage = (int) $this->context->employee->id_lang;
        $current_index = $this->context->link->getAdminLink('AdminModules', false);
        $current_token = Tools::getAdminTokenLite('AdminModules');
        $action_url = $current_index . '&configure=' . $this->name . '&token=' . $current_token;
        $this->context->smarty->assign(array(
            'languages' => $languages,
            'defaultFormLanguage' => $defaultFormLanguage,
            'action_url' => $action_url,
            'ps_version' => _PS_VERSION_,
        ));
        $res = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ .
            'b2bregistration/views/templates/admin/prefix/new_prefix.tpl'
        );
        die(json_encode($res));
    }

    /**
     * Handle Request for deleting prefixes
     */
    public function ajaxProcessDeletePrefix()
    {
        $id = (int) Tools::getValue('id_prefix');
        $obj = new Gender($id);
        $result = $obj->delete();
        die(json_encode($result));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $switch_option = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';
        $fields_form = array();
        $this->multiple_fieldsets = true;
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Configuration'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Enable Module'),
                    'name' => 'B2BREGISTRATION_ENABLE_DISABLE',
                    'is_bool' => true,
                    'desc' => $this->l('Use this to enable and disable module'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        $fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('B2B Registration Setting'),
                'icon' => 'icon icon-cogs',
            ),
            'input' => array(
                array(
                    'label' => 'Registration Form URL Key',
                    'type' => 'text',
                    'name' => 'B2BREGISTRATION_URL_KEY',
                    'desc' => $this->l('Frontend Default: b2b-customer-create'),
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                ),
                array(
                    'type' => $switch_option,
                    'label' => $this->l('B2B Customer Auto Approvel'),
                    'name' => 'B2BREGISTRATION_AUTO_APPROVEL',
                    'is_bool' => true,
                    'desc' => $this->l('Use this to enable and disable b2b customer auto approvel'),
                    'values' => array(
                        array(
                            'id' => 'auto_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'auto_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Disable Normal Registration'),
                    'name' => 'B2BREGISTRATION_NORMAL_REGISTRATION',
                    'is_bool' => true,
                    'desc' => $this->l('Use this to enable and disable normal registration'),
                    'values' => array(
                        array(
                            'id' => 'normal_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'normal_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Enable Custom Fields'),
                    'name' => 'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
                    'is_bool' => true,
                    'desc' => $this->l('Use this to enable and disable custom fileds'),
                    'values' => array(
                        array(
                            'id' => 'cs_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'cs_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Enable Top Link in Header'),
                    'name' => 'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
                    'is_bool' => true,
                    'desc' => $this->l('Use this to enable and disable top link in header at front office'),
                    'values' => array(
                        array(
                            'id' => 'link_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'link_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'label' => 'Top Link Text',
                    'type' => 'text',
                    'name' => 'B2BREGISTRATION_URL_TEXT',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                ),
                array(
                    'label' => 'Personal Data Heading',
                    'type' => 'text',
                    'name' => 'B2BREGISTRATION_PERSONAL_TEXT',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                ),
                array(
                    'label' => 'Company Data Heading',
                    'type' => 'text',
                    'name' => 'B2BREGISTRATION_COMPANY_TEXT',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                ),
                array(
                    'label' => 'Signin Data Heading',
                    'type' => 'text',
                    'name' => 'B2BREGISTRATION_SIGNIN_TEXT',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                ),
                array(
                    'label' => 'Address Data Heading',
                    'type' => 'text',
                    'name' => 'B2BREGISTRATION_ADDRESS_TEXT',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                ),
                array(
                    'label' => 'Custom Field Heading',
                    'type' => 'text',
                    'name' => 'B2BREGISTRATION_CUSTOM_FIELD_TEXT',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                ),
                array(
                    'label' => 'Pending Account Message Text',
                    'type' => 'textarea',
                    'name' => 'B2BREGISTRATION_ERROR_MSG_TEXT',
                    'col' => '5',
                    'lang' => true,
                    'required' => true,
                ),
                array(
                    'label' => 'Choose CMS Page for Terms and Conditions',
                    'type' => 'B2BREGISTRATION_CMS_PAGES',
                    'name' => 'B2BREGISTRATION_CMS_PAGES',
                ),
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Enable Name Prefix'),
                    'name' => 'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'prefix_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'prefix_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'label' => 'Name Prefix Dropdown Options',
                    'type' => 'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
                    'name' => 'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
                    'required' => true,
                ),
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Enable Name Suffix'),
                    'name' => 'B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'suffix_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'suffix_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'label' => 'Name Suffix Dropdown Options',
                    'type' => 'text',
                    'name' => 'B2BREGISTRATION_NAME_SUFFIX_OPTIONS',
                    'desc' => $this->l(
                        'Comma (,) separated values.e.g MD,PHD'
                    ),
                    'col' => '5',
                    'required' => true,
                ),
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Enable Middle Name'),
                    'name' => 'B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'middle_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'middle_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'label' => 'Assign Groups',
                    'type' => 'B2BREGISTRATION_GROUPS',
                    'name' => 'B2BREGISTRATION_GROUPS',
                    'required' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        $fields_form[2]['form'] = array(
            'legend' => array(
                'title' => $this->l('Add Other Form Fields'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Date of Birth'),
                    'name' => 'B2BREGISTRATION_DOB_ENABLE_DISABLE',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'dob_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'dob_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => $switch_option,
                    'label' => $this->l('IDENTIFICATION/Siret Number'),
                    'name' => 'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'identification_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'identification_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Website'),
                    'name' => 'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'gender_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'gender_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Address'),
                    'name' => 'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'address_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'address_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        $fields_form[3]['form'] = array(
            'legend' => array(
                'title' => $this->l('Admin Email Notification Setting'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Send Email Notification to Admin'),
                    'name' => 'B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE',
                    'is_bool' => true,
                    'desc' => $this->l('Use this to enable and disable email notifications for admin'),
                    'values' => array(
                        array(
                            'id' => 'admin_e_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'admin_e_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'label' => 'Admin Email ID',
                    'type' => 'text',
                    'name' => 'B2BREGISTRATION_ADMIN_EMAIL_ID',
                    'col' => '5',
                    'required' => true,
                ),
                array(
                    'label' => 'Email Sender',
                    'type' => 'B2BREGISTRATION_ADMIN_EMAIL_SENDER',
                    'name' => 'B2BREGISTRATION_ADMIN_EMAIL_SENDER',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        $fields_form[4]['form'] = array(
            'legend' => array(
                'title' => $this->l('Customer Email Notification Setting'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Send Email Notification to Customer'),
                    'name' => 'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE',
                    'is_bool' => true,
                    'desc' => $this->l('Use this to enable and disable email notifications for customer'),
                    'values' => array(
                        array(
                            'id' => 'customer_e_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'customer_e_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        $fields_form[5]['form'] = array(
            'legend' => array(
                'title' => $this->l('Google reCAPTCHA Setting'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => $switch_option,
                    'label' => $this->l('Google reCAPTCHA'),
                    'name' => 'B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'captcha_e_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'captcha_e_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'label' => 'Site Key',
                    'type' => 'B2BREGISTRATION_SITE_KEY',
                    'name' => 'B2BREGISTRATION_SITE_KEY',
                ),
                array(
                    'label' => 'Secret key',
                    'type' => 'B2BREGISTRATION_SECRET_KEY',
                    'name' => 'B2BREGISTRATION_SECRET_KEY',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        return $fields_form;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false);
        $field = array();
        foreach ($languages as $lang) {
            $field['B2BREGISTRATION_URL_KEY'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_URL_KEY_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_URL_KEY',
                    (int) $lang['id_lang'],
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ));
            $field['B2BREGISTRATION_URL_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_URL_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_URL_TEXT',
                    (int) $lang['id_lang'],
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ));

            $field['B2BREGISTRATION_PERSONAL_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_PERSONAL_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_PERSONAL_TEXT',
                    (int) $lang['id_lang'],
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ));
            $field['B2BREGISTRATION_SIGNIN_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_SIGNIN_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_SIGNIN_TEXT',
                    (int) $lang['id_lang'],
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ));
            $field['B2BREGISTRATION_ADDRESS_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_ADDRESS_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_ADDRESS_TEXT',
                    (int) $lang['id_lang'],
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ));
            $field['B2BREGISTRATION_COMPANY_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_COMPANY_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_COMPANY_TEXT',
                    (int) $lang['id_lang'],
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ));
            $field['B2BREGISTRATION_CUSTOM_FIELD_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_CUSTOM_FIELD_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_CUSTOM_FIELD_TEXT',
                    (int) $lang['id_lang'],
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ));
            $field['B2BREGISTRATION_ERROR_MSG_TEXT'][$lang['id_lang']] = pSQL(Tools::getValue(
                'B2BREGISTRATION_ERROR_MSG_TEXT_' . $lang['id_lang'],
                Configuration::get(
                    'B2BREGISTRATION_ERROR_MSG_TEXT',
                    (int) $lang['id_lang'],
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ));
        }
        $field['B2BREGISTRATION_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_CMS_PAGES'] = (int) Configuration::get(
            'B2BREGISTRATION_CMS_PAGES',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_AUTO_APPROVEL'] = (int) Configuration::get(
            'B2BREGISTRATION_AUTO_APPROVEL',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_NAME_PREFIX_OPTIONS'] = pSQL(Configuration::get(
            'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $field['B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_NAME_SUFFIX_OPTIONS'] = pSQL(Configuration::get(
            'B2BREGISTRATION_NAME_SUFFIX_OPTIONS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $field['B2BREGISTRATION_NORMAL_REGISTRATION'] = (int) Configuration::get(
            'B2BREGISTRATION_NORMAL_REGISTRATION',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_GROUPS'] = (int) Configuration::get(
            'B2BREGISTRATION_GROUPS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_ADMIN_EMAIL_ID'] = pSQL(Configuration::get(
            'B2BREGISTRATION_ADMIN_EMAIL_ID',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $field['B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_ADMIN_EMAIL_SENDER'] = (int) Configuration::get(
            'B2BREGISTRATION_ADMIN_EMAIL_SENDER',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE'] = (int) Configuration::get(
            'B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $field['B2BREGISTRATION_SITE_KEY'] = pSQL(Configuration::get(
            'B2BREGISTRATION_SITE_KEY',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $field['B2BREGISTRATION_SECRET_KEY'] = pSQL(Configuration::get(
            'B2BREGISTRATION_SECRET_KEY',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $field['B2BREGISTRATION_DOB_ENABLE_DISABLE'] = pSQL(Configuration::get(
            'B2BREGISTRATION_DOB_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $field['B2BREGISTRATION_ENABLE_CUSTOM_FIELDS'] = pSQL(Configuration::get(
            'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $field['B2BREGISTRATION_ADDRESS_ENABLE_DISABLE'] = pSQL(Configuration::get(
            'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $field['B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE'] = pSQL(Configuration::get(
            'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        $field['B2BREGISTRATION_WEBSITE_ENABLE_DISABLE'] = pSQL(Configuration::get(
            'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));

        return $field;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $B2BREGISTRATION_ENABLE_DISABLE = (int) Tools::getValue('B2BREGISTRATION_ENABLE_DISABLE');
        $B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE = (int) Tools::getValue('B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE');
        $B2BREGISTRATION_AUTO_APPROVEL = (int) Tools::getValue('B2BREGISTRATION_AUTO_APPROVEL');
        $B2BREGISTRATION_CMS_PAGES = (int) Tools::getValue('B2BREGISTRATION_CMS_PAGES');
        $B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE = (int) Tools::getValue(
            'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE'
        );
        $B2BREGISTRATION_NAME_PREFIX_OPTIONS = Tools::getValue('B2BREGISTRATION_NAME_PREFIX_OPTIONS');
        $B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE = (int) Tools::getValue(
            'B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE'
        );
        $B2BREGISTRATION_NORMAL_REGISTRATION = (int) Tools::getValue(
            'B2BREGISTRATION_NORMAL_REGISTRATION'
        );
        $B2BREGISTRATION_NAME_SUFFIX_OPTIONS = pSQL(Tools::getValue('B2BREGISTRATION_NAME_SUFFIX_OPTIONS'));
        $B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE = (int) Tools::getValue(
            'B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE'
        );
        $B2BREGISTRATION_GROUPS = (int) Tools::getValue('B2BREGISTRATION_GROUPS');
        $B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE = (int) Tools::getValue(
            'B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE'
        );
        $B2BREGISTRATION_ADMIN_EMAIL_ID = pSQL(Tools::getValue('B2BREGISTRATION_ADMIN_EMAIL_ID'));
        $B2BREGISTRATION_ADMIN_EMAIL_SENDER = pSQL(Tools::getValue('B2BREGISTRATION_ADMIN_EMAIL_SENDER'));
        $B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE = (int) Tools::getValue(
            'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE'
        );
        $B2BREGISTRATION_ENABLE_CUSTOM_FIELDS = (int) Tools::getValue(
            'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS'
        );
        $B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE = (int) Tools::getValue('B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE');
        $B2BREGISTRATION_SITE_KEY = pSQL(Tools::getValue('B2BREGISTRATION_SITE_KEY'));
        $B2BREGISTRATION_SECRET_KEY = pSQL(Tools::getValue('B2BREGISTRATION_SECRET_KEY'));
        $B2BREGISTRATION_DOB_ENABLE_DISABLE = pSQL(Tools::getValue('B2BREGISTRATION_DOB_ENABLE_DISABLE'));
        $B2BREGISTRATION_ADDRESS_ENABLE_DISABLE = pSQL(Tools::getValue('B2BREGISTRATION_ADDRESS_ENABLE_DISABLE'));
        $B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE = pSQL(Tools::getValue(
            'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE'
        ));
        $B2BREGISTRATION_WEBSITE_ENABLE_DISABLE = pSQL(Tools::getValue('B2BREGISTRATION_WEBSITE_ENABLE_DISABLE'));
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $lang = $lang->id;
        $B2BREGISTRATION_URL_TEXT = pSQL(Tools::getValue('B2BREGISTRATION_URL_TEXT_' . $lang));
        $B2BREGISTRATION_PERSONAL_TEXT = pSQL(Tools::getValue('B2BREGISTRATION_PERSONAL_TEXT_' . $lang));
        $B2BREGISTRATION_COMPANY_TEXT = pSQL(Tools::getValue('B2BREGISTRATION_COMPANY_TEXT_' . $lang));
        $B2BREGISTRATION_SIGNIN_TEXT = pSQL(Tools::getValue('B2BREGISTRATION_SIGNIN_TEXT_' . $lang));
        $B2BREGISTRATION_ADDRESS_TEXT = pSQL(Tools::getValue('B2BREGISTRATION_ADDRESS_TEXT_' . $lang));
        $B2BREGISTRATION_URL_KEY = pSQL(Tools::getValue('B2BREGISTRATION_URL_KEY_' . $lang));
        $B2BREGISTRATION_CUSTOM_FIELD_TEXT = pSQL(Tools::getValue(
            'B2BREGISTRATION_CUSTOM_FIELD_TEXT_' . $lang
        ));
        $B2BREGISTRATION_ERROR_MSG_TEXT = pSQL(Tools::getValue(
            'B2BREGISTRATION_ERROR_MSG_TEXT_' . $lang
        ));
        if (empty($B2BREGISTRATION_URL_KEY)) {
            $this->context->controller->errors[] = $this->l('Please enter the url key');
        } elseif (empty($B2BREGISTRATION_URL_TEXT)) {
            $this->context->controller->errors[] = $this->l('Please enter the text of top link');
        } elseif (empty($B2BREGISTRATION_PERSONAL_TEXT)) {
            $this->context->controller->errors[] = $this->l('Please enter the text for personal data heading');
        } elseif (empty($B2BREGISTRATION_COMPANY_TEXT)) {
            $this->context->controller->errors[] = $this->l('Please enter the text for company data heading');
        } elseif (empty($B2BREGISTRATION_SIGNIN_TEXT)) {
            $this->context->controller->errors[] = $this->l('Please enter the text for signin data heading');
        } elseif (empty($B2BREGISTRATION_ADDRESS_TEXT)) {
            $this->context->controller->errors[] = $this->l('Please enter the text for address data heading');
        } elseif (empty($B2BREGISTRATION_CUSTOM_FIELD_TEXT)) {
            $this->context->controller->errors[] = $this->l('Please enter the text for Custom Field heading');
        } elseif (empty($B2BREGISTRATION_ERROR_MSG_TEXT)) {
            $this->context->controller->errors[] = $this->l('Please enter the text for error message');
        } elseif (empty($B2BREGISTRATION_NAME_PREFIX_OPTIONS)) {
            $this->context->controller->errors[] = $this->l('Please check name prefix options');
        } elseif (empty($B2BREGISTRATION_ADMIN_EMAIL_ID) && $B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE) {
            $this->context->controller->errors[] = $this->l('Please enter email for Admin');
        } elseif (!Validate::isEmail($B2BREGISTRATION_ADMIN_EMAIL_ID) && $B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE) {
            $this->context->controller->errors[] = $this->l('Please enter valid email for Admin');
        } else {
            $languages = Language::getLanguages(false);
            if ($B2BREGISTRATION_NAME_PREFIX_OPTIONS != null) {
                $B2BREGISTRATION_NAME_PREFIX_OPTIONS = implode(
                    ",",
                    Tools::getValue('B2BREGISTRATION_NAME_PREFIX_OPTIONS')
                );
            }
            $values = array();
            foreach ($languages as $lang) {
                $values['B2BREGISTRATION_URL_TEXT'][$lang['id_lang']] =
                Tools::getValue('B2BREGISTRATION_URL_TEXT_' . $lang['id_lang']);
                $values['B2BREGISTRATION_PERSONAL_TEXT'][$lang['id_lang']] =
                Tools::getValue('B2BREGISTRATION_PERSONAL_TEXT_' . $lang['id_lang']);
                $values['B2BREGISTRATION_COMPANY_TEXT'][$lang['id_lang']] =
                Tools::getValue('B2BREGISTRATION_COMPANY_TEXT_' . $lang['id_lang']);
                $values['B2BREGISTRATION_SIGNIN_TEXT'][$lang['id_lang']] =
                Tools::getValue('B2BREGISTRATION_SIGNIN_TEXT_' . $lang['id_lang']);
                $values['B2BREGISTRATION_ADDRESS_TEXT'][$lang['id_lang']] =
                Tools::getValue('B2BREGISTRATION_ADDRESS_TEXT_' . $lang['id_lang']);
                $values['B2BREGISTRATION_URL_KEY'][$lang['id_lang']] =
                Tools::getValue('B2BREGISTRATION_URL_KEY_' . $lang['id_lang']);
                $values['B2BREGISTRATION_CUSTOM_FIELD_TEXT'][$lang['id_lang']] =
                Tools::getValue('B2BREGISTRATION_CUSTOM_FIELD_TEXT_' . $lang['id_lang']);
                $values['B2BREGISTRATION_ERROR_MSG_TEXT'][$lang['id_lang']] =
                Tools::getValue('B2BREGISTRATION_ERROR_MSG_TEXT_' . $lang['id_lang']);

                $meta = Meta::getMetaByPage('module-b2bregistration-business', (int) $lang['id_lang']);
                $id_meta = $meta['id_meta'];
                $meta_url = new Meta($id_meta, (int) $lang['id_lang']);
                $meta_url->url_rewrite = Tools::getValue('B2BREGISTRATION_URL_KEY_' . $lang['id_lang']);
                $meta_url->update();
            }
            Configuration::updateValue(
                'B2BREGISTRATION_ENABLE_DISABLE',
                $B2BREGISTRATION_ENABLE_DISABLE,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
                $B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_AUTO_APPROVEL',
                $B2BREGISTRATION_AUTO_APPROVEL,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_CMS_PAGES',
                $B2BREGISTRATION_CMS_PAGES,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
                $B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
                $B2BREGISTRATION_ENABLE_CUSTOM_FIELDS,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
                $B2BREGISTRATION_NAME_PREFIX_OPTIONS,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_NORMAL_REGISTRATION',
                $B2BREGISTRATION_NORMAL_REGISTRATION,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE',
                $B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_NAME_SUFFIX_OPTIONS',
                $B2BREGISTRATION_NAME_SUFFIX_OPTIONS,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE',
                $B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_GROUPS',
                $B2BREGISTRATION_GROUPS,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE',
                $B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_ADMIN_EMAIL_SENDER',
                $B2BREGISTRATION_ADMIN_EMAIL_SENDER,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_ADMIN_EMAIL_ID',
                $B2BREGISTRATION_ADMIN_EMAIL_ID,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE',
                $B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE',
                $B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_SITE_KEY',
                $B2BREGISTRATION_SITE_KEY,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_SECRET_KEY',
                $B2BREGISTRATION_SECRET_KEY,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_DOB_ENABLE_DISABLE',
                $B2BREGISTRATION_DOB_ENABLE_DISABLE,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
                $B2BREGISTRATION_ADDRESS_ENABLE_DISABLE,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
                $B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
                $B2BREGISTRATION_WEBSITE_ENABLE_DISABLE,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            // MultiLang Fields
            Configuration::updateValue(
                'B2BREGISTRATION_URL_KEY',
                $values['B2BREGISTRATION_URL_KEY'],
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_URL_TEXT',
                $values['B2BREGISTRATION_URL_TEXT'],
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_PERSONAL_TEXT',
                $values['B2BREGISTRATION_PERSONAL_TEXT'],
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_COMPANY_TEXT',
                $values['B2BREGISTRATION_COMPANY_TEXT'],
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_SIGNIN_TEXT',
                $values['B2BREGISTRATION_SIGNIN_TEXT'],
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_ADDRESS_TEXT',
                $values['B2BREGISTRATION_ADDRESS_TEXT'],
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_CUSTOM_FIELD_TEXT',
                $values['B2BREGISTRATION_CUSTOM_FIELD_TEXT'],
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'B2BREGISTRATION_ERROR_MSG_TEXT',
                $values['B2BREGISTRATION_ERROR_MSG_TEXT'],
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $this->context->controller->confirmations[] = $this->l('Update Successfully');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Module::isInstalled('b2bregistration') &&
            Module::isEnabled('b2bregistration') &&
            Tools::getvalue("configure")
        ) {
            $current_index = $this->context->link->getAdminLink('AdminModules', false);
            $current_token = Tools::getAdminTokenLite('AdminModules');
            $action_url = $current_index .
            '&configure=' .
            $this->name .
            '&token=' .
            $current_token .
            '&tab_module=' .
            $this->tab .
            '&module_name=' .
            $this->name;
            Media::addJsDef(array(
                'config_url' => $action_url,
                'admin_url' => $this->context->link->getAdminLink('AdminB2BCustomers', true),
            ));
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $enable_module = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        if ($enable_module) {
            $controller = Dispatcher::getInstance()->getController();
            $site_key = pSQL(Configuration::get(
                'B2BREGISTRATION_SITE_KEY',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));
            $normal_form = (int) Configuration::get(
                'B2BREGISTRATION_NORMAL_REGISTRATION',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $action = Tools::getValue('create_account');
            if ($normal_form == 1 && $controller == 'authentication' && $action == 1) {
                Tools::redirect($this->context->link->getModuleLink('b2bregistration', 'business'));
            }
            Media::addJsDef(array(
                'controller_link' => $this->context->link->getModuleLink('b2bregistration', 'business'),
                'site_key' => $site_key,
                'controller' => $controller,
                'ps_version' => _PS_VERSION_,
                'create_account' => $this->l('Now you can create account as B2B'),
                'normal_form' => $normal_form,

            ));
            if ($controller == 'business' || $controller == 'b2b') {
                $this->context->controller->addJS($this->_path . '/views/js/front.js');
            }
            $this->context->controller->addJS($this->_path . '/views/js/block_normal_reg.js');
            $this->context->controller->addCSS($this->_path . '/views/css/front.css');
        }
    }

    public function hookDisplayNav2()
    {
        $id_lang = $this->context->language->id;
        $enable_module = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_top_link = (int) Configuration::get(
            'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $top_link_text = pSQL(Configuration::get(
            'B2BREGISTRATION_URL_TEXT',
            (int) $id_lang,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        if ($enable_module == 1 &&
            !($this->context->customer->logged) &&
            !empty($top_link_text) &&
            $enable_top_link == 1) {
            $page_link = $this->context->link->getModuleLink('b2bregistration', 'business');
            $this->context->smarty->assign(array(
                'top_link_text' => $top_link_text,
                'page_link' => $page_link,
            ));
            return $this->display(__FILE__, 'display_nav2.tpl');
        }
    }

    public function hookDisplayNav()
    {
        $id_lang = $this->context->language->id;
        $enable_module = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_top_link = (int) Configuration::get(
            'B2BREGISTRATION_TOP_LINK_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $top_link_text = pSQL(Configuration::get(
            'B2BREGISTRATION_URL_TEXT',
            (int) $id_lang,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        if ($enable_module == 1 &&
            !($this->context->customer->logged) &&
            !empty($top_link_text) &&
            $enable_top_link == 1) {
            $page_link = $this->context->link->getModuleLink('b2bregistration', 'business');
            $this->context->smarty->assign(array(
                'top_link_text' => $top_link_text,
                'page_link' => $page_link,
            ));
            return $this->display(__FILE__, 'display_nav.tpl');
        }
    }

    protected function createB2BGroup()
    {
        $b2b_group = new Group();
        $b2b_group->reduction = 0;
        $b2b_group->price_display_method = 1;
        $b2b_group->show_prices = 1;
        $b2b_group->date_add = date('Y-m-d H:i:s');
        foreach (Language::getLanguages() as $lang) {
            $b2b_group->name[$lang['id_lang']] = $this->l('B2B');
        }

        if ($b2b_group->add()) {
            Configuration::updateValue(
                'B2BREGISTRATION_GROUPS',
                $b2b_group->id,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $shops = Shop::getShops(true, null, true);
            $modules = Module::getModulesInstalled();
            $module_permissions = array();
            foreach ($modules as $val) {
                $module_permissions[] = $val['id_module'];
            }
            Group::addModulesRestrictions((int) Configuration::get(
                'B2BREGISTRATION_GROUPS',
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ), $module_permissions, $shops);
            $categories = BusinessAccountModel::getAllCategories();
            foreach ($categories as $id_category) {
                BusinessAccountModel::addB2BGroupToCategory(
                    $id_category,
                    (int) Configuration::get(
                        'B2BREGISTRATION_GROUPS',
                        null,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    )
                );
            }
            return true;
        }
        return false;
    }

    public function deleteB2BGroup()
    {
        $b2b_group = new Group((int) Configuration::get(
            'B2BREGISTRATION_GROUPS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        if ($b2b_group->delete()) {
            Configuration::deleteByName('B2BREGISTRATION_GROUPS');
            return true;
        }
        return false;
    }

    public function hookModuleRoutes()
    {
        $url_link = Configuration::get(
            'B2BREGISTRATION_URL_KEY',
            (int) $this->context->language->id,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        if (empty($url_link)) {
            $url_link = 'b2bregistration';
        }
        return array(
            'module-' . $this->name . '-business' => array(
                'controller' => 'business',
                'rule' => $url_link,
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+', 'param' => 'id'),
                    'rewrite' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*', 'param' => 'rewrite'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),
        );
    }

    /**
     * GDPR Compliance Hooks
     */
    public function hookActionDeleteGDPRCustomer($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            $sql = "DELETE FROM " . _DB_PREFIX_ . "customer WHERE id_customer = " . (int) $customer['id'];
            $sql &= "DELETE FROM " . _DB_PREFIX_ . "b2bregistration WHERE id_customer = " . (int) $customer['id'];
            $sql &= "DELETE FROM " . _DB_PREFIX_ . "address WHERE id_customer = " . (int) $customer['id'];
            if (Db::getInstance()->execute($sql)) {
                return json_encode(true);
            }
            return json_encode($this->l('B2B Registration: Unable to delete customer using customer id.'));
        }
    }

    public function hookActionExportGDPRData($customer)
    {
        if (!Tools::isEmpty($customer['email']) && Validate::isEmail($customer['email'])) {
            $res = BusinessAccountModel::getB2BCustomers($this->context->language->id, $customer['id']);
            $result = array();
            foreach ($res as $key => $res1) {
                $result[$key][$this->l('ID')] = $customer['id'];
                $result[$key][$this->l('First Name')] = $customer['firstname'];
                $result[$key][$this->l('Middle Name')] = $res1['middle_name'];
                $result[$key][$this->l('Last Name')] = $customer['lastname'];
                $result[$key][$this->l('Email')] = $customer['email'];
                $result[$key][$this->l('Siret')] = $customer['siret'];
                $result[$key][$this->l('Company')] = $customer['website'];
                $result[$key][$this->l('Address')] = $res1['address1'];
                $result[$key][$this->l('City')] = $res1['city'];
            }
            if ($result) {
                return json_encode($result);
            }
            return json_encode($this->l('B2B Registration: Unable to export customer using email.'));
        }
    }

    public function hookDisplayCustomerAccount()
    {
        $enable_module = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        if ($enable_module) {
            $id_customer = (int) $this->context->cookie->id_customer;
            $b2b = BusinessAccountModel::getRegisteredB2B($id_customer);
            if (empty($b2b)) {
                if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                    return $this->display(__FILE__, 'hook_customer_account_17.tpl');
                } else {
                    return $this->display(__FILE__, 'hook_customer_account_16.tpl');
                }
            } else {
                $links = $this->context->link->getModuleLink(
                    'b2bregistration',
                    'b2b',
                    array('id_b2b' => $b2b['id_b2bregistration']),
                    true
                );
                $this->context->smarty->assign('links', $links);
                if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                    return $this->display(__FILE__, 'hook_b2b_customer.tpl');
                } else {
                    return $this->display(__FILE__, 'hook_b2b_customer_16.tpl');
                }
            }
        }
    }

    public function hookActionObjectCustomerUpdateAfter($object)
    {
        $id_customer = (int) $object['object']->id;
        $customer = new Customer($id_customer);
        if (!empty($id_customer)) {
            $obj = BusinessAccountModel::getBusinessStatus($id_customer);
            $objs = new BusinessAccountModel($obj['id_b2bregistration']);
            if ($obj) {
                if ($obj['active'] == 1) {
                    $objs->active = 0;
                } else {
                    $objs->active = 1;
                }
                $res = $objs->update();
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
                    Mail::Send(
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
        }
    }

    public function hookActionObjectCustomerDeleteAfter($object)
    {
        if ($object) {
            $id_customer = (int) Tools::getValue('id_customer');
            if ($id_customer) {
                BusinessAccountModel::extraFieldsDeletion($id_customer);
                BToBFieldsData::customFieldsDeletion($id_customer);
            }
        }
    }
}
