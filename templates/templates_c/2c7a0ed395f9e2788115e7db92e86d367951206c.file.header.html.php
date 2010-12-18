<?php /* Smarty version Smarty-3.0.6, created on 2010-12-17 11:42:36
         compiled from "/Applications/MAMP/htdocs/paperless2/templates/templates/header.html" */ ?>
<?php /*%%SmartyHeaderCode:9191049084d0baf1c756dd4-08467625%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2c7a0ed395f9e2788115e7db92e86d367951206c' => 
    array (
      0 => '/Applications/MAMP/htdocs/paperless2/templates/templates/header.html',
      1 => 1292611341,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9191049084d0baf1c756dd4-08467625',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<html>
<head>
<title>Paperless</title>

<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
static/css/style.css">
</head>
<body>
<?php if (isset($_smarty_tpl->getVariable('code',null,true,false)->value)){?>
<div id="code_wrapper">
<?php }else{ ?>
<div id="wrapper">
<?php }?>
    <div class="title link">
        <a href="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
">paperless</a>
    </div>
    
    <div id="username">Hi <?php echo $_smarty_tpl->getVariable('username')->value;?>
!</div>

