<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class B2BRegistrationBusinessModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->display_column_left = false;
        $this->display_column_right = false;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme = false);
        $controller = Dispatcher::getInstance()->getController();
        if ($controller == 'business') {
            $this->addjQueryPlugin(array(
                'fancybox',
            ));
        }
    }

    public function initContent()
    {
        parent::initContent();

        if ($this->context->customer->logged) {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        } else {
            $enable_module = (int) Configuration::get(
                'B2BREGISTRATION_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_prefix = (int) Configuration::get(
                'B2BREGISTRATION_NAME_PREFIX_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_suffix = (int) Configuration::get(
                'B2BREGISTRATION_NAME_SUFFIX_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_address = (int) Configuration::get(
                'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_website = (int) Configuration::get(
                'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_custom = (int) Configuration::get(
                'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_birthdate = (int) Configuration::get(
                'B2BREGISTRATION_DOB_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_identification_number = (int) Configuration::get(
                'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $enable_captcha = (int) Configuration::get(
                'B2BREGISTRATION_CAPTCHA_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $name_prefix = explode(',', Configuration::get(
                'B2BREGISTRATION_NAME_PREFIX_OPTIONS',
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
            $middle_name = (int) Configuration::get(
                'B2BREGISTRATION_MIDDLE_NAME_ENABLE_DISABLE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $personal_heading = pSQL(Configuration::get(
                'B2BREGISTRATION_PERSONAL_TEXT',
                $this->context->language->id,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));
            $site_key = pSQL(Configuration::get(
                'B2BREGISTRATION_SITE_KEY',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));
            $company_heading = pSQL(Configuration::get(
                'B2BREGISTRATION_COMPANY_TEXT',
                $this->context->language->id,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));
            $signin_heading = pSQL(Configuration::get(
                'B2BREGISTRATION_SIGNIN_TEXT',
                $this->context->language->id,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));
            $address_heading = pSQL(Configuration::get(
                'B2BREGISTRATION_ADDRESS_TEXT',
                $this->context->language->id,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));
            $custom_heading = pSQL(Configuration::get(
                'B2BREGISTRATION_CUSTOM_FIELD_TEXT',
                $this->context->language->id,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ));
            $cms_page = (int) Configuration::get(
                'B2BREGISTRATION_CMS_PAGES',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $cms_page_link = '';
            if ($cms_page) {
                $cms_page_link = new CMS($cms_page, $this->context->cookie->id_lang);
            }
            $custom_fields = BToBCustomFields::selectCustomFields($this->context->language->id);
            $genders = BusinessAccountModel::getAllGenders($this->context->language->id);
            $url_link = $this->context->link->getModuleLink('b2bregistration', 'business');
            $this->context->smarty->assign(array(
                'name_prefix' => $name_prefix,
                'enable_prefix' => $enable_prefix,
                'enable_suffix' => $enable_suffix,
                'enable_address' => $enable_address,
                'enable_website' => $enable_website,
                'enable_birthdate' => $enable_birthdate,
                'enable_captcha' => $enable_captcha,
                'cms' => $cms_page_link,
                'site_key' => $site_key,
                'enable_identification_number' => $enable_identification_number,
                'name_suffix' => $name_suffix,
                'middle_name' => $middle_name,
                'personal_heading' => $personal_heading,
                'company_heading' => $company_heading,
                'signin_heading' => $signin_heading,
                'address_heading' => $address_heading,
                'custom_heading' => $custom_heading,
                'enable_custom' => $enable_custom,
                'genders' => $genders,
                'url_link' => $url_link,
                'id_module' => $this->module->id,
                'custom_fields' => $custom_fields,
            ));
            if ($enable_module && true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                $this->setTemplate('module:b2bregistration/views/templates/front/business_account.tpl');
            } else {
                $this->setTemplate('business_account_16.tpl');
            }
        }
    }

    public function init()
    {
        parent::init();
        $result = false;
        $enable_identification = (int) Configuration::get(
            'B2BREGISTRATION_IDENTIFICATION_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_wesbsite = (int) Configuration::get(
            'B2BREGISTRATION_WEBSITE_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_address = (int) Configuration::get(
            'B2BREGISTRATION_ADDRESS_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $default_group = (int) Configuration::get(
            'B2BREGISTRATION_GROUPS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $default_country = (int) Configuration::get(
            'PS_COUNTRY_DEFAULT',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_email_customer = (int) Configuration::get(
            'B2BREGISTRATION_CUSTOMER_EMAIL_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_email_admin = (int) Configuration::get(
            'B2BREGISTRATION_ADMIN_EMAIL_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_custom = (int) Configuration::get(
            'B2BREGISTRATION_ENABLE_CUSTOM_FIELDS',
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
        $auto_approvel = (int) Configuration::get(
            'B2BREGISTRATION_AUTO_APPROVEL',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $enable_birth = (int) Configuration::get(
            'B2BREGISTRATION_DOB_ENABLE_DISABLE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $error_message = pSQL(Configuration::get(
            'B2BREGISTRATION_ERROR_MSG_TEXT',
            $this->context->language->id,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));
        if (Tools::isSubmit('b2b_add_data')) {
            $name_prefix = pSQL(Tools::getValue('name_prefix'));
            $name_suffix = pSQL(Tools::getValue('name_suffix'));
            $first_name = pSQL(Tools::getValue('first_name'));
            $middle_name = pSQL(Tools::getValue('middle_name'));
            $last_name = pSQL(Tools::getValue('last_name'));
            $address_alias = pSQL(Tools::getValue('address_alias'));
            $city = pSQL(Tools::getValue('city'));
            $address = pSQL(Tools::getValue('address'));
            $birthdate = pSQL(Tools::getValue('birthday'));
            $website = pSQL(Tools::getValue('website'));
            $company_name = pSQL(Tools::getValue('company_name'));
            $email = pSQL(Tools::getValue('email'));
            $vat = pSQL(Tools::getValue('vat_number'));
            $password = pSQL(Tools::getValue('password'));
            $newsletter = (int) Tools::getValue('newsletter');
            $confirm_password = pSQL(Tools::getValue('confirm_password'));
            $id_fields = Tools::getValue('id_fields');
            if (!empty($password)) {
                $passwd = Tools::encrypt($password);
            }
            if ($auto_approvel == 1) {
                $active = 1;
            } else {
                $active = 0;
            }
            $partner_option = (int) Tools::getValue('partner_option');
            $identification_number = pSQL(Tools::getValue('identification_number'));
            $customer = new Customer();
            $b2b = new BusinessAccountModel();
            if (empty($first_name)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'first_name_required'
                ];
            } elseif (!Validate::isName($first_name)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'first_name_valid'
                ];
            } elseif (empty($last_name)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'last_name_required'
                ];
            } elseif (!Validate::isName($last_name)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'last_name_valid'
                ];
            } elseif (empty($birthdate) && $enable_birth) {
                $this->context->controller->errors[] = $this->module->translations[
                    'empty_birthday'
                ];
            } elseif (!Validate::isBirthDate($birthdate) && $enable_birth) {
                $this->context->controller->errors[] = $this->module->translations[
                    'invalid_birthday'
                ];
            } elseif ($enable_wesbsite && empty($website)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'website_required'
                ];
            } elseif (empty($address_alias) && $enable_address) {
                $this->context->controller->errors[] = $this->module->translations[
                    'address_alias_required'
                ];
            } elseif (empty($city) && $enable_address) {
                $this->context->controller->errors[] = $this->module->translations[
                    'city_required'
                ];
            } elseif (!Validate::isCityName($city)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'city_valid'
                ];
            } elseif (empty($address) && $enable_address) {
                $this->context->controller->errors[] = $this->module->translations[
                    'address_required'
                ];
            } elseif (!Validate::isAddress($address) && $enable_address) {
                $this->context->controller->errors[] = $this->module->translations[
                    'address_valid'
                ];
            } elseif (empty($company_name)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'company_required'
                ];
            } elseif (empty($identification_number) && $enable_identification) {
                $this->context->controller->errors[] = $this->module->translations[
                    'siret_required'
                ];
            } elseif ($enable_identification &&
                !Validate::isSiret($identification_number) &&
                _PS_VERSION_ < '1.7.0.0') {
                $this->context->controller->errors[] = $this->module->translations[
                    'siret_valid'
                ];
            } elseif (empty($email)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'email_required'
                ];
            } elseif (!Validate::isEmail($email)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'email_valid'
                ];
            } elseif ($customer->customerExists($email, false, true)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'email_exist'
                ];
            } elseif (empty($password)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'password_required'
                ];
            } elseif (!Validate::isPasswd($password)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'password_valid'
                ];
            } elseif (empty($confirm_password)) {
                $this->context->controller->errors[] = $this->module->translations[
                    'confirm_required'
                ];
            } elseif ($password != $confirm_password) {
                $this->context->controller->errors[] = $this->module->translations[
                    'confirm_valid'
                ];
            } else {
                $customer->id_shop = $this->context->shop->id;
                $customer->id_shop_group = $this->context->shop->id_shop_group;
                $customer->id_default_group = $default_group;
                $customer->id_lang = $this->context->language->id;
                $customer->id_gender = $name_prefix;
                $customer->firstname = $first_name;
                $customer->lastname = $last_name;
                $customer->birthday = $birthdate;
                $customer->email = $email;
                $customer->newsletter = $newsletter;
                $customer->optin = $partner_option;
                $customer->website = $website;
                $customer->company = $company_name;
                $customer->siret = $identification_number;
                $customer->passwd = $passwd;
                $customer->active = $active;

                $res = $customer->save();
                $result = true;
                if ($res == true) {
                    $b2b->id_customer = $customer->id;
                    $b2b->middle_name = $middle_name;
                    $b2b->name_suffix = $name_suffix;
                    $b2b->flag = 1;
                    $b2b->active = $active;
                    $b2b->save();
                    $result = true;
                }
                if ($res == true && $enable_address) {
                    $addres = new Address();
                    $addres->id_customer = $customer->id;
                    $addres->company = $company_name;
                    $addres->id_country = $default_country;
                    $addres->firstname = $first_name;
                    $addres->lastname = $last_name;
                    $addres->address1 = $address;
                    $addres->alias = $address_alias;
                    $addres->city = $city;
                    $addres->vat_number = $vat;
                    $addres->dni = $identification_number;
                    $addres->save();
                    $result = true;
                }
                if (!empty($id_fields) && $res == true && $enable_custom) {
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
                if ($enable_email_customer == 1 && $result == true) {
                    $subject = Mail::l('Customer Registration');
                    $templateVars = array(
                        '{first_name}' => $first_name,
                        '{last_name}' => $last_name,
                        '{company_name}' => $company_name,
                        '{website}' => $website,
                        '{email}' => $email,
                    );
                    if ($auto_approvel == 1) {
                        $template_name = 'b2b_customer_registration';
                    } else {
                        $template_name = 'b2b_customer_pending';
                    }
                    $title = $subject;
                    $from = Configuration::get('PS_SHOP_EMAIL');
                    if ($email_sender == "") {
                        $email_sender = Configuration::get('PS_SHOP_NAME');
                    }
                    $fromName = $email_sender;
                    $mailDir = _PS_MODULE_DIR_ . 'b2bregistration/mails/';
                    $toName = $first_name;
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
                        $this->context->controller->confirmations[] = $this->module->translations[
                            'email_send'
                        ];
                    }
                    $result = true;
                }
                if ($enable_email_admin == 1 && $result == true) {
                    $admin_email = pSQL(Configuration::get(
                        'B2BREGISTRATION_ADMIN_EMAIL_ID',
                        false,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    ));
                    $subject = Mail::l('New Customer Registration');
                    $templateVars = array(
                        '{first_name}' => $first_name,
                        '{last_name}' => $last_name,
                        '{company_name}' => $company_name,
                        '{website}' => $website,
                        '{email}' => $email,
                    );
                    $template_name = 'customer_registration_admin_notify';
                    $title = $subject;
                    $from = Configuration::get('PS_SHOP_EMAIL');
                    if ($email_sender == "") {
                        $email_sender = Configuration::get('PS_SHOP_NAME');
                    }
                    $fromName = $email_sender;
                    $mailDir = _PS_MODULE_DIR_ . 'b2bregistration/mails/';
                    $toName = 'Admin';
                    $send = Mail::Send(
                        Context::getContext()->language->id,
                        $template_name,
                        $title,
                        $templateVars,
                        $admin_email,
                        $toName,
                        $from,
                        $fromName,
                        null,
                        null,
                        $mailDir
                    );
                    $result = true;
                }

                if ($result == true && $customer->id && $auto_approvel == 1) {
                    $ps_version = _PS_VERSION_;
                    if ($ps_version >= '1.7.0.0') {
                        $this->context->updateCustomer($customer);
                        Hook::exec(
                            'actionAuthentication',
                            array(
                                'customer' => $this->context->customer,
                            )
                        );
                    } else {
                        $this->context->cookie->id_customer = (int) $customer->id;
                        $this->context->cookie->customer_firstname = $customer->firstname;
                        $this->context->cookie->customer_lastname = $customer->lastname;
                        $this->context->cookie->logged = 1;
                        Tools::redirect('index.php?controller=authentication?back=my-account');
                    }
                } else {
                    if (!empty($error_message)) {
                        $this->context->controller->errors[] = $error_message;
                    } else {
                        $this->context->controller->errors[] = $this->module->translations[
                            'validate_account'
                        ];
                    }
                }
            }
        }
    }
}
