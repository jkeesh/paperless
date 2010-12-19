<?php /* Smarty version Smarty-3.0.6, created on 2010-12-17 16:32:30
         compiled from "/Applications/MAMP/htdocs/paperless2/templates/templates/assignment.html" */ ?>
<?php /*%%SmartyHeaderCode:18177406024d0be4fed76458-15056896%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b3d1949bef811126107042f9205ca8a5f15b820' => 
    array (
      0 => '/Applications/MAMP/htdocs/paperless2/templates/templates/assignment.html',
      1 => 1292562281,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18177406024d0be4fed76458-15056896',
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