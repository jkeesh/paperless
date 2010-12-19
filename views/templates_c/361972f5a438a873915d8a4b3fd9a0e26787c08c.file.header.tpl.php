<?php /* Smarty version Smarty-3.0.6, created on 2010-12-16 17:09:08
         compiled from "/Applications/MAMP/htdocs/paperless1/templates/templates/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11944031634d0aaa24beb575-42477260%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '361972f5a438a873915d8a4b3fd9a0e26787c08c' => 
    array (
      0 => '/Applications/MAMP/htdocs/paperless1/templates/templates/header.tpl',
      1 => 1292280612,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11944031634d0aaa24beb575-42477260',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_popup_init')) include '/Applications/MAMP/htdocs/paperless1/lib/smarty/plugins/function.popup_init.php';
?><HTML>
<HEAD>
<?php echo smarty_function_popup_init(array('src'=>"/javascripts/overlib.js"),$_smarty_tpl);?>

<TITLE><?php echo $_smarty_tpl->getVariable('title')->value;?>
 - <?php echo $_smarty_tpl->getVariable('Name')->value;?>
</TITLE>
</HEAD>
<BODY bgcolor="#ffffff">
