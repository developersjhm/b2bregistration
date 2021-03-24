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
	{if $input.type == 'name_suffix'}
		<div class="col-lg-4" style="margin-left: -0.8%;">
			<select name="name_suffix" class="form-control">
				{foreach from=$name_suffix item=suffix}
					<option value="{$suffix|escape:'htmlall':'UTF-8'}" {if isset($selected_name_sufix) AND !empty($selected_name_sufix)}selected{/if}>{$suffix|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
			</select>
		</div>
	{else if $input.name == 'custom_fields'}
		{if !empty($custom_fields) AND $enable_custom}
			{if !empty($id_customer) AND $id_customer}
				</div>
				{foreach from=$fields_custom item=fields}
					<input type="hidden" name="id_fields[]" value="{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">
					<input type="hidden" name="label_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" value="{if !empty($fields.b2b_field_title)}{$fields.b2b_field_title|escape:'htmlall':'UTF-8'} {/if}">
					
					{if $fields.b2b_field_type == 'text'}
						<div class="form-group">
							<label class="control-label col-lg-3" for="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">{$fields.b2b_field_title|escape:'htmlall':'UTF-8'}</label>
							<div class="col-lg-4">
								<input type="text" name="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" value="{if !empty($fields.b2b_field_name)}{$fields.b2b_field_name|escape:'htmlall':'UTF-8'} {/if}" id="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" class="form-control" {if $fields.field_required}required="required"{/if}>
							</div>
						</div>
					{else}
						<div class="form-group">
							<label class="control-label col-lg-3" for="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">{$fields.b2b_field_title|escape:'htmlall':'UTF-8'}</label>
							<div class="col-lg-4">
								<textarea name="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" class="form-control" {if $fields.field_required}required=""{/if} id="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">{if !empty($fields.b2b_field_title)}{$fields.b2b_field_name|escape:'htmlall':'UTF-8'} {/if}</textarea>
							</div>
						</div>
					{/if}
				{/foreach}
				{else}
					</div>
					{foreach from=$custom_fields item=fields}
						<input type="hidden" name="id_fields[]" value="{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">
						<input type="hidden" name="label_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" value="{$fields.b2b_field_name|escape:'htmlall':'UTF-8'}">
						
						{if $fields.b2b_field_type == 'text'}
							<div class="form-group">
								<label class="control-label col-lg-3" for="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">{$fields.b2b_field_name|escape:'htmlall':'UTF-8'}</label>
								<div class="col-lg-4">
									<input type="text" name="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" id="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" class="form-control" {if $fields.field_required}required="required"{/if}>
								</div>
							</div>
						{else}
							<div class="form-group">
								<label class="control-label col-lg-3" for="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">{$fields.b2b_field_name|escape:'htmlall':'UTF-8'}</label>
								<div class="col-lg-4">
									<textarea name="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" class="form-control" {if $fields.field_required}required=""{/if} id="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}"></textarea>
								</div>
							</div>
						{/if}
					{/foreach}
				{/if}
			<div class="form-group">
		{/if}
	{else}
    	{$smarty.block.parent}
	{/if}
{/block}