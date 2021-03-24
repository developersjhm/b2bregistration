{*
* 2007-2019 PrestaShop
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
*  @author    FMM Modules
*  @copyright 2019 FME Modules
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
{extends file="helpers/form/form.tpl"}
{block name = 'input'}
	{if $input.type == 'B2BREGISTRATION_GROUPS'}
		{if $ps_version < '1.7.0.0'}
			<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
			rel="stylesheet">
		{/if}
		<div class="col-lg-6" style="margin-left: -0.8%;">
			<select name="B2BREGISTRATION_GROUPS" class="form-control">
				{foreach from=$groups item=group}
					<option value="{$group.id_group|escape:'htmlall':'UTF-8'}" {if isset($cpGroups) AND $cpGroups AND in_array($group.id_group, $cpGroups)}selected{/if}>{$group.name|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
			</select>
		</div>
	{else if $input.type == 'B2BREGISTRATION_ADMIN_EMAIL_SENDER'}
		<div class="col-lg-6" style="margin-left: -0.8%;">
			<select name="B2BREGISTRATION_ADMIN_EMAIL_SENDER" class="form-control">
				<option value="Admin" {if !empty($admin_email_sender) AND $admin_email_sender == "Admin"}selected{/if}>{l s='Admin' mod='b2bregistration'}</option>
				<option value="General Contact" {if !empty($admin_email_sender) AND $admin_email_sender == "General Contact"}selected{/if}>{l s='General Contact' mod='b2bregistration'}</option>
				<option value="Sales Representative" {if !empty($admin_email_sender) AND $admin_email_sender == "Sales Representative"}selected{/if}>{l s='Sales Representative' mod='b2bregistration'}</option>
				<option value="Customer Support" {if !empty($admin_email_sender) AND $admin_email_sender == "Customer Support"}selected{/if}>{l s='Customer Support' mod='b2bregistration'}</option>
			</select>
		</div>
	{else if $input.type == 'B2BREGISTRATION_CUSTOMER_EMAIL_SENDER'}
    	<div class="col-lg-6" style="margin-left: -0.8%;">
			<select name="B2BREGISTRATION_CUSTOMER_EMAIL_SENDER" class="form-control">
				<option value="General Contact" {if !empty($customer_email_sender) AND $customer_email_sender == "General Contact"}selected{/if}>{l s='General Contact' mod='b2bregistration'}</option>
				<option value="Sales Representative" {if !empty($customer_email_sender) AND $customer_email_sender == "Sales Representative"}selected{/if}>{l s='Sales Representative' mod='b2bregistration'}</option>
				<option value="Customer Support" {if !empty($customer_email_sender) AND $customer_email_sender == "Customer Support"}selected{/if}>{l s='Customer Support' mod='b2bregistration'}</option>
			</select>
		</div>
	{else if $input.type == 'B2BREGISTRATION_NAME_PREFIX_OPTIONS'}
		<div class="col-lg-5" style="margin-left: -0.8%;" id="auto-scroll">
	      <table cellspacing="0" cellpadding="0" class="table std panel">
	          <thead>
	              <tr>
	                <th></th>
	                <th>{l s='ID' mod='b2bregistration'}</th>
	                <th>{l s='Name Prefix' mod='b2bregistration'}</th>
	                <th>{l s='Action' mod='b2bregistration'}</th>
	              </tr>
	          </thead>
	          <tbody>
	          	{foreach from=$genders item=gender}
	              	<tr id ="delete_{$gender.id_gender|escape:'htmlall':'UTF-8'}">
		                <td>
		                  <input type="checkbox" class="cpgender" name="B2BREGISTRATION_NAME_PREFIX_OPTIONS[]" id="gender_{$gender.id_gender|escape:'htmlall':'UTF-8'}" value="{$gender.id_gender|escape:'htmlall':'UTF-8'}" {if isset($cpGender) AND $cpGender AND in_array($gender.id_gender, $cpGender)}checked="checked"{/if}/>
		                </td>
		                <td>{$gender.id_gender|escape:'htmlall':'UTF-8'}</td>
		                <td>
		                  <label for="gender_{$gender.id_gender|escape:'htmlall':'UTF-8'}">{$gender.name|escape:'htmlall':'UTF-8'}</label>
		                </td>
		                <td><i id="delete-prefix" class="material-icons" style="color:red;" onclick="deletePrefix({$gender.id_gender|escape:'htmlall':'UTF-8'});">delete_forever</i></td>
	              	</tr>
	          {/foreach}
	          </tbody>
	      </table>
	       <p class="help-block hint-block">{l s='Selected customer pefix dropdown options.' mod='b2bregistration'}</p>
	    </div>	
	    <div class="col-lg-2">
       		<button type="submit" class="btn btn-primary" id="add-prefixes">
       			{l s='Add More Prefixes' mod='b2bregistration'}
       		</button>
	   	</div>
	{else if $input.type == 'B2BREGISTRATION_CMS_PAGES'}
		<div class="col-lg-6 form-group margin-form">
			{if !empty($cms_pages) AND $cms_pages}
				<select id="B2BREGISTRATION_CMS_PAGES" name="B2BREGISTRATION_CMS_PAGES" class="form-control fixed-width-xxl ">
					{foreach from=$cms_pages item=page}
						<option value="{$page.id|escape:'htmlall':'UTF-8'}" {if $selected_page AND $selected_page == $page.id} selected="selected"{/if}>{$page.name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				</select>
			{else}
				<div class="alert alert-warning warning">{l s='There is no cms page available. Please add a cms page first.' mod='b2bregistration'}</div>
			{/if}
		</div>
	{else if $input.type == 'B2BREGISTRATION_SITE_KEY'}
		<div class="col-lg-6 form-group margin-form">
			<input type="text" name="B2BREGISTRATION_SITE_KEY" class="form-control" value="{if !empty($fields_value) And !empty($fields_value['B2BREGISTRATION_SITE_KEY'])}{$fields_value['B2BREGISTRATION_SITE_KEY']|escape:'htmlall':'UTF-8'}{/if}">
	    	<p class="help-block hint-block">{l s='Register here to get site key ' mod='b2bregistration'}<a href="https://www.google.com/recaptcha/admin/create" target="_blank">{l s='Google reCAPTCHA' mod='b2bregistration'}</a></p>
		</div>
	{else if $input.type == 'B2BREGISTRATION_SECRET_KEY'}
		<div class="col-lg-6 form-group margin-form">
			<input type="text" name="B2BREGISTRATION_SECRET_KEY" class="form-control" value="{if !empty($fields_value) And !empty($fields_value['B2BREGISTRATION_SECRET_KEY'])}{$fields_value['B2BREGISTRATION_SECRET_KEY']|escape:'htmlall':'UTF-8'}{/if}">
	    	<p class="help-block hint-block">{l s='Register here to get site key ' mod='b2bregistration'}<a href="https://www.google.com/recaptcha/admin/create" target="_blank">{l s='Google reCAPTCHA' mod='b2bregistration'}</a></p>
		</div>
    {else}
    	{$smarty.block.parent}
	{/if}
{/block}