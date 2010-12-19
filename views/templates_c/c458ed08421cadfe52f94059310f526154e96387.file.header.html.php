<?php /* Smarty version Smarty-3.0.6, created on 2010-12-17 10:27:44
         compiled from "/afs/ir.stanford.edu/class/cs198/cgi-bin/paperless1/templates/templates/header.html" */ ?>
<?php /*%%SmartyHeaderCode:4432316024d0baba0f2d935-82570132%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c458ed08421cadfe52f94059310f526154e96387' => 
    array (
      0 => '/afs/ir.stanford.edu/class/cs198/cgi-bin/paperless1/templates/templates/header.html',
      1 => 1292610457,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4432316024d0baba0f2d935-82570132',
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
    <div class="title">
        <a href="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
">Paperless</a>
    </div>
    
    <div id="username">Hi <?php echo $_smarty_tpl->getVariable('username')->value;?>
!</div>

