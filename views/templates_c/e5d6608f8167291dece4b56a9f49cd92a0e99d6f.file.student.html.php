<?php /* Smarty version Smarty-3.0.6, created on 2010-12-16 21:52:35
         compiled from "/Applications/MAMP/htdocs/paperless1/templates/templates/student.html" */ ?>
<?php /*%%SmartyHeaderCode:4874798144d0aec93360885-75154831%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e5d6608f8167291dece4b56a9f49cd92a0e99d6f' => 
    array (
      0 => '/Applications/MAMP/htdocs/paperless1/templates/templates/student.html',
      1 => 1292561512,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4874798144d0aec93360885-75154831',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_template = new Smarty_Internal_Template("header.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>

<div class="list">
    <div class="header">Students</div>
        <?php  $_smarty_tpl->tpl_vars['assignment'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('assignments')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['assignment']->key => $_smarty_tpl->tpl_vars['assignment']->value){
?>
        <div class="link"><a href="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
code/<?php echo $_smarty_tpl->getVariable('student')->value;?>
/<?php echo $_smarty_tpl->tpl_vars['assignment']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['assignment']->value;?>
</a></div>
        <?php }} ?>
    </div>
</div>

<?php $_template = new Smarty_Internal_Template("footer.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
