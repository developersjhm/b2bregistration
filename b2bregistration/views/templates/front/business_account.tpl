{**
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
*}
{extends file='page.tpl'}
{block name='page_content'}
	{if $enable_captcha }
	    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
	{/if}
	<section class="register-form">
		<p>{l s='Already have an account? ' mod='b2bregistration'}<a href="{$urls.pages.authentication|escape:'htmlall':'UTF-8'}">{l s='Log in instead!' d='Shop.Theme.Customeraccount'}</a></p>
		<form id="customer-form" method="post" action="{$url_link|escape:'htmlall':'UTF-8'}">
			<div id="b2b_registation_required" class="alert alert-danger error" style="display:none;"></div>
			<section>
				<h3>{if !empty($personal_heading)}{$personal_heading|escape:'htmlall':'UTF-8'}{/if}</h3><hr>
				{if $enable_prefix}
					<div class="form-group col-md-2">
				      <label for="name-prefix">{l s='Name Prefix' mod='b2bregistration'}</label>
				    	<select class="form-control" id="name-prefix" name="name_prefix">
			         		{if !empty($name_prefix)}
			         			{foreach from=$genders item=gender}
			         				{if in_array($gender.id_gender, $name_prefix)}
			         					<option value="{$gender.id_gender|escape:'htmlall':'UTF-8'}">{$gender.name|escape:'htmlall':'UTF-8'}</option>
			         				{/if}	
				         		{/foreach}	
			         		{/if}
			         	</select>
				    </div>	
				{/if}
				<div class="form-group col-md-3">
				    <label for="first-name">{l s='First Name' mod='b2bregistration'}<sup style="color:red;" class="required">*</sup></label>
			    	<input type="text" name="first_name" id="first-name" class="form-control" value="{if !empty($smarty.post.first_name)}{$smarty.post.first_name|escape:'htmlall':'UTF-8'}{/if}">
			    </div>
			    {if $middle_name}
				 	<div class="form-group col-md-3">
					    <label for="middle-name">{l s='Middle Name' mod='b2bregistration'}</label>
				    	<input type="text" name="middle_name" id="middle-name" class="form-control" value="{if !empty($smarty.post.middle_name)}{$smarty.post.middle_name|escape:'htmlall':'UTF-8'}{/if}">
				    </div>
				{/if}
			    <div class="form-group col-md-3">
				    <label for="last-name">{l s='Last Name' mod='b2bregistration'}<sup style="color:red;" class="required">*</sup></label>
			    	<input type="text"  name="last_name" id="last-name" class="form-control" value="{if !empty($smarty.post.last_name)}{$smarty.post.last_name|escape:'htmlall':'UTF-8'}{/if}">
			    </div>
			    {if $enable_suffix}
					<div class="form-group col-md-2">
				      <label for="name-suffix">{l s='Name Suffix' mod='b2bregistration'}</label>
				    	<select class="form-control" id="name-suffix" name="name_suffix">
			         		{if !empty($name_suffix)}
			         			{foreach from=$name_suffix item=name_suffixes}
			         				<option value="{$name_suffixes|escape:'htmlall':'UTF-8'}">{$name_suffixes|escape:'htmlall':'UTF-8'}</option>
			         			{/foreach}
			         		{/if}
			         	</select>
				    </div>	
				{/if}

				{if $enable_birthdate}
					<div class="form-group col-md-3">
					    <label for="birthdate">{l s='Birth Date' mod='b2bregistration'}<sup style="color:red;" class="required">*</sup></label>
				    	<input type="text" name="birthday" id="birthdate" class="form-control" placeholder="{l s='YYYY-MM-DD' mod='b2bregistration'}" value="{if !empty($smarty.post.birthday)}{$smarty.post.birthday|escape:'htmlall':'UTF-8'}{/if}">
				    	<span class="form-control-comment">{l s='(E.g.: 1970-12-31)' mod='b2bregistration'}</span>
				    </div>
			    {/if}

			    {if $enable_website}
					<div class="form-group col-md-3">
				     	<label for="gender">{l s='Website' mod='b2bregistration'}<sup style="color:red;" class="required">*</sup></label>
				    	<input type="text" name="website" id="company-website" class="form-control" value="{if !empty($smarty.post.website)}{$smarty.post.website|escape:'htmlall':'UTF-8'}{/if}" placeholder="{l s='https://www.google.com' mod='b2bregistration'}">
				    </div>	
				{/if}

				{if $enable_address}
					<div class="col-md-12 row">
				    	<h3>{if !empty($address_heading)}{$address_heading|escape:'htmlall':'UTF-8'}{/if}</h3><hr>
				    </div>
					<div class="form-group col-md-3">
					    <label for="address-alias">{l s='Address Alias' mod='b2bregistration'}<sup style="color:red;" class="required">*</sup></label>
				    	<input type="text" name="address_alias" id="address-alias" placeholder="{l s='e.g Home' mod='b2bregistration'}" class="form-control" value="{if !empty($smarty.post.address_alias)}{$smarty.post.address_alias|escape:'htmlall':'UTF-8'}{/if}">
				    </div>
					
					<div class="form-group col-md-3">
					    <label for="city">{l s='City' mod='b2bregistration'}<sup style="color:red;" class="required">*</sup></label>
				    	<input type="text" name="city" id="city" class="form-control" value="{if !empty($smarty.post.city)}{$smarty.post.city|escape:'htmlall':'UTF-8'}{/if}">
				    </div>

					<div class="form-group col-md-3">
					    <label for="address">{l s='Address' mod='b2bregistration'}<sup style="color:red;" class="required">*</sup></label>
				    	<input type="text" name="address" id="address" class="form-control" value="{if !empty($smarty.post.address)}{$smarty.post.address|escape:'htmlall':'UTF-8'}{/if}">
				    </div>

					<div class="form-group col-md-3">
					    <label for="vat_num">{l s='Vat Number' mod='b2bregistration'}</label>
				    	<input type="text" name="vat_number" id="vat_num" class="form-control" value="{if !empty($smarty.post.vat_num)}{$smarty.post.vat_number|escape:'htmlall':'UTF-8'}{/if}">
				    </div>
				{/if}

			    <div class="col-md-12 row">
			    	<h3>{if !empty($company_heading)}{$company_heading|escape:'htmlall':'UTF-8'}{/if}</h3><hr>
			    </div>

			    <div class="form-group col-md-5">
				    <label for="company-name">{l s='Company Name' mod='b2bregistration'}<sup style="color:red;" class="required">*</sup></label>
			    	<input type="text" name="company_name" id="company-name" class="form-control" value="{if !empty($smarty.post.company_name)}{$smarty.post.company_name|escape:'htmlall':'UTF-8'}{/if}" >
			    </div>

				{if $enable_identification_number}
				    <div class="form-group col-md-5">
					    <label for="identification-number">{l s='Identification/Siret Number' mod='b2bregistration'}<sup style="color:red;" class="required">*</sup></label>
				    	<input type="text" name="identification_number" id="identification-number" class="form-control" value="{if !empty($smarty.post.identification_number)}{$smarty.post.identification_number|escape:'htmlall':'UTF-8'}{/if}">
				    </div>
				{/if}

				<div class="col-md-12 row">
			    	<h3>{if !empty($signin_heading)}{$signin_heading|escape:'htmlall':'UTF-8'}{/if}</h3><hr>
			    </div>

			    <div class="form-group col-md-4">
				    <label for="email">{l s='Email' mod='b2bregistration'}<sup style="color:red;" class="required">*</sup></label>
			    	<input type="email" name="email" id="email" class="form-control" value="{if !empty($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'}{/if}">
			    </div>

			    <div class="form-group col-md-4">
				    <label for="password">{l s='Password' mod='b2bregistration'}<sup style="color:red;" class="required">*</sup></label>
				    <div class="input-group js-parent-focus">
				    	<input type="password" name="password" id="business-password" class="form-control js-child-focus js-visible-password" value="{if !empty($smarty.post.password)}{$smarty.post.password|escape:'htmlall':'UTF-8'}{/if}">
				    	<span class="input-group-btn">
			              <button class="btn" type="button" data-action="show-password" data-text-show="Show" data-text-hide="Hide">
			                {l s='Show' mod='b2bregistration'}
			              </button>
			            </span>
			        </div>    
			    </div>

			    <div class="form-group col-md-4">
				    <label for="confirm-password">{l s='Confirm Password' mod='b2bregistration'}<sup style="color:red;" class="required">*</sup></label>
				    <div class="input-group js-parent-focus">
				    	<input type="password" name="confirm_password" id="confirm-password" class="form-control js-child-focus js-visible-password" value="{if !empty($smarty.post.confirm_password)}{$smarty.post.confirm_password|escape:'htmlall':'UTF-8'}{/if}">
				    	<span class="input-group-btn">
			              <button class="btn" type="button" data-action="show-password" data-text-show="Show" data-text-hide="Hide">
			                {l s='Show' mod='b2bregistration'}
			              </button>
			            </span>
			        </div>    
			    </div>

				{if !empty($custom_fields) AND $enable_custom}
					<div class="col-md-12 row">
			    	<h3>{if !empty($custom_heading)}{$custom_heading|escape:'htmlall':'UTF-8'}{/if}</h3><hr>
				    </div>
					{foreach from=$custom_fields item=fields}
						<div class="form-group col-md-4">
							<input type="hidden" name="id_fields[]" value="{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">
							<input type="hidden" name="label_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" value="{$fields.b2b_field_name|escape:'htmlall':'UTF-8'}">
							{if $fields.b2b_field_type == 'text'}
								<label for="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">{$fields.b2b_field_name|escape:'htmlall':'UTF-8'}{if $fields.field_required}<sup style="color:red;" class="required">*</sup>{/if}</label>
								<input type="text" name="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" id="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" class="form-control" {if $fields.field_required}required=""{/if}>
							{else}
								<label for="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">{$fields.b2b_field_name|escape:'htmlall':'UTF-8'}{if $fields.field_required}<sup style="color:red;" class="required">*</sup>{/if}</label>
								<textarea name="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" class="form-control" {if $fields.field_required}required=""{/if} id="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}"></textarea>
							{/if}
						</div>
					{/foreach}
					<div class="col-md-12 row">
				</div>
				{/if}
				<div class="form-group col-md-4" >
		          <span class="custom-checkbox">
		            <label class="label-control col-md-12">
		              <input name="partner_option" type="checkbox" id="partner-option" value="0">
		              <span><i class="material-icons rtl-no-flip checkbox-checked"></i></span>
		              {l s='Receive offers from our partners' mod='b2bregistration'}
		            </label>
		          </span>
    			</div>

    			<div class="form-group col-md-4" >
		          <span class="custom-checkbox">
		            <label class="label-control col-md-12">
		              <input name="newsletter" value="0" type="checkbox" id="newsletter">
		              <span><i class="material-icons rtl-no-flip checkbox-checked"></i></span>
		              {l s='Sign up for our newsletter' mod='b2bregistration'}
		            </label>
		          </span>
    			</div>

    			<div class="form-group col-md-4" >
		          <span class="custom-checkbox">
		            <label class="label-control col-md-12">
		              	<input name="terms" type="checkbox" id="terms" value="1" >
		              	<span><i class="material-icons rtl-no-flip checkbox-checked"></i></span>
		              	{l s='I agree to the terms and conditions and the privacy policy' mod='b2bregistration'}
						{if isset($cms) AND $cms}
							<a class="read-b2b-conditions" href="#b2b-cond" class="thickbox" title="{l s='Conditions of the B2B registration program' mod='b2bregistration'}" rel="nofollow">{l s='Read conditions.' mod='b2bregistration'}</a>
							<div style="display:none;">
								<div id="b2b-cond">
									{include file="module:b2bregistration/views/templates/front/cms-page.tpl"}
								</div>
							</div>
						{/if}
					</label>
		          </span>
    			</div>
    			<!-- consent box -->
				<div class="form-group row">
					
					<div class="col-lg-9">
						{hook h='displayGDPRConsent' mod='psgdpr' id_module=$id_module}
					</div>
				</div>
    			<div class="form-group col-md-6">
                	<div class='g-recaptcha' id='Gcaptcha'></div>
    			</div>
    			<div class="col-md-12"><hr></div>
    			<footer class="form-footer clearfix">
    				 <button title="{l s='Check terms to enable button' mod='b2bregistration' }" class="btn btn-primary form-control-submit float-xs-right" type="submit" id="b2b_add_data" name="b2b_add_data" {if isset($site_key) AND $enable_captcha}style='display:none;'{/if}>
			          {l s='Save' mod='b2bregistration'}
			        </button>
    			</footer>
			</section>		
		</form>
	</section>
{/block}