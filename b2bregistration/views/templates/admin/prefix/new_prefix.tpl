{*
* B2B Registration
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @category  FMM Modules
* @package   b2bregistration
* @author    FMM Modules
* @copyright Copyright 2019 © fmemodules
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<div class="bootstrap">
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-male"></i>
            {l s='Social Titles' mod='b2bregistration'}<hr>
        </div> 
        <form method="post" id ="prefix-form" action="{$action_url|escape:'htmlall':'UTF-8'}">
            <div class="col-lg-12">
                <input type="hidden" name="ajax" value="1">
                <input type="hidden" name="action" value="savePrefix">
                <div class="col-lg-10">
                    {foreach from=$languages item=language}
                        {if $languages|count > 1}
                            <div class="translatable-field row lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                <div class="col-lg-9">
                        {/if}
                        <div class="form-group row">
                            <label class="form-control-label col-lg-3">{l s='Title' mod='b2bregistration'}</label>
                            <div class="col-lg-9">
                                <div class="col-lg-12">
                                    <input type="text" id= "prefix_text_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="prefix_text_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="form-control">
                                </div>
                                <p style="margin-left:2.6%;" class="help-block hint-block">{l s='e.g Mr.' mod='b2bregistration'}</p>
                            </div>
                        </div>
                        {if $languages|count > 1}
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        {$language.iso_code|escape:'htmlall':'UTF-8'}
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        {foreach from=$languages item=language}
                                            <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'htmlall':'UTF-8'});">{$language.name|escape:'htmlall':'UTF-8'}</a></li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>

            <div class="form-group">
                <label class="form-control-label col-lg-2">{l s='Gender' mod='b2bregistration'}</label>
                <div class="col-lg-5" {if $ps_version < '1.7.0.0'}style="margin-left:5.8%;"{else}style="margin-left:0.6%;"{/if}>
                    <select name="gender" class="form-control">
                        <option value="0">{l s='Male' mod='b2bregistration'}</option>
                        <option value="1">{l s='Female' mod='b2bregistration'}</option>
                        <option value="2">{l s='Neutral' mod='b2bregistration'}</option>
                    </select>
                </div>
                
            </div>
           <div class="form-group">
            <div class="col-lg-12">
                <hr>
                <input type="submit" class="col-lg-2 btn btn-success pull-right" id="save-prefix" value="{l s='Save' mod='b2bregistration'}">
            </div>
            </div>
        </form>
    </div>        
</div>