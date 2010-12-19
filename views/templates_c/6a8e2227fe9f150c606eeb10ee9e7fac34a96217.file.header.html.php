<?php /* Smarty version Smarty-3.0.6, created on 2010-12-16 22:10:30
         compiled from "/Applications/MAMP/htdocs/paperless1/templates/templates/header.html" */ ?>
<?php /*%%SmartyHeaderCode:20131997424d0af0c6e218d7-28908501%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6a8e2227fe9f150c606eeb10ee9e7fac34a96217' => 
    array (
      0 => '/Applications/MAMP/htdocs/paperless1/templates/templates/header.html',
      1 => 1292562627,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20131997424d0af0c6e218d7-28908501',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<html>
<head>
<title>Paperless</title>

<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
style.css">
</head>
<body>
<?php if (isset($_smarty_tpl->getVariable('code',null,true,false)->value)){?>
<div id="code_wrapper">
<?php }else{ ?>
<div id="wrapper">
<?php }?>
    <div class="title">
        <a href="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
">Paperless</a>
    </div>
    
    <div id="username">Hi <?php echo $_smarty_tpl->getVariable('username')->value;?>
!</div>

