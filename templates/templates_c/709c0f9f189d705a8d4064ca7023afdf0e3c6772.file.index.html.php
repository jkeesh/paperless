<?php /* Smarty version Smarty-3.0.6, created on 2010-12-16 22:22:02
         compiled from "/Applications/MAMP/htdocs/paperless1/templates/templates/index.html" */ ?>
<?php /*%%SmartyHeaderCode:16465429854d0af37a2a2314-24821298%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '709c0f9f189d705a8d4064ca7023afdf0e3c6772' => 
    array (
      0 => '/Applications/MAMP/htdocs/paperless1/templates/templates/index.html',
      1 => 1292563321,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16465429854d0af37a2a2314-24821298',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_template = new Smarty_Internal_Template("header.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>

<div class="list">
    <div class="header">Students</div>
        <?php  $_smarty_tpl->tpl_vars['user'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('users')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['user']->key => $_smarty_tpl->tpl_vars['user']->value){
?>
        <div class="link"><a href="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
student/<?php echo $_smarty_tpl->tpl_vars['user']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['user']->value;?>
</a></div>
        <?php }} ?>
    </div>
</div>

<div class="list">
    <div class="header">Assignments</div>
        <?php  $_smarty_tpl->tpl_vars['assignment'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('assignments')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['assignment']->key => $_smarty_tpl->tpl_vars['assignment']->value){
?>
        <div class="link"><a href="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
assignment/<?php echo $_smarty_tpl->tpl_vars['assignment']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['assignment']->value;?>
</a></div>
        <?php }} ?>
    </div>
</div>

<?php $_template = new Smarty_Internal_Template("footer.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
