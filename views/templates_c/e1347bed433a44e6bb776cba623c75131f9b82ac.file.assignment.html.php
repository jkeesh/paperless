<?php /* Smarty version Smarty-3.0.6, created on 2010-12-16 22:04:42
         compiled from "/Applications/MAMP/htdocs/paperless1/templates/templates/assignment.html" */ ?>
<?php /*%%SmartyHeaderCode:14396296804d0aef6a3eb765-81004977%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e1347bed433a44e6bb776cba623c75131f9b82ac' => 
    array (
      0 => '/Applications/MAMP/htdocs/paperless1/templates/templates/assignment.html',
      1 => 1292562281,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14396296804d0aef6a3eb765-81004977',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_template = new Smarty_Internal_Template("header.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>

<h2>Assignment: <?php echo $_smarty_tpl->getVariable('assignment')->value;?>
</h2>

<?php  $_smarty_tpl->tpl_vars['student'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('students')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['student']->key => $_smarty_tpl->tpl_vars['student']->value){
?>
<div class="link">
    <a href="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
code/<?php echo $_smarty_tpl->tpl_vars['student']->value;?>
/<?php echo $_smarty_tpl->getVariable('assignment')->value;?>
"><?php echo $_smarty_tpl->getVariable('assignment')->value;?>
 by <?php echo $_smarty_tpl->tpl_vars['student']->value;?>
</a>
</div>
<?php }} ?>

<?php $_template = new Smarty_Internal_Template("footer.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>