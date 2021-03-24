/**
 * 2007-2020 PrestaShop
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
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

$(document).ready(function() {
    if (controller == 'authentication' && ps_version < '1.7' && normal_form == 1) {
        $('#create-account_form .form_content').hide();
        var html = '<h4 class="heading"> ' + create_account + '</h3><a href="' + controller_link + '" class="btn btn-default button button-medium exclusive" ><span><i class="icon-user left"></i>Create B2B account</span></a>';
        $('#create-account_form').append(html);
    }
    if (controller == 'authentication' && ps_version >= '1.7.0.0') {
        var html = '<span> Or<a href="' + controller_link + '">  Register as B2B</a></span>';
        $('.register-form p').append(html);
    }
});