<?php /* Smarty version Smarty-3.0.6, created on 2010-12-17 10:28:28
         compiled from "/afs/ir.stanford.edu/class/cs198/cgi-bin/paperless1/templates/templates/code.html" */ ?>
<?php /*%%SmartyHeaderCode:12300696114d0babcc0163e3-89148348%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5e60325473e4e28c52e8350800ae3d82613147a1' => 
    array (
      0 => '/afs/ir.stanford.edu/class/cs198/cgi-bin/paperless1/templates/templates/code.html',
      1 => 1292610504,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12300696114d0babcc0163e3-89148348',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_template = new Smarty_Internal_Template("header.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>

<script type="text/javascript" src="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
static/js/jquery-1.4.1.min.js"></script>
<script type="text/javascript" src="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
static/js/jquery-1.4.2.js"></script>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script> 
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/themes/base/jquery-ui.css"> 
	
<script type="text/javascript" src="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
static/js/feedback.js"></script>
<script type="text/javascript" src="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
static/js/shortcut.js"></script>

<script type="text/javascript" src="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
static/js/syntaxhighlighter/scripts/shCore.js"></script>
<script type="text/javascript" src="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
static/js/syntaxhighlighter/scripts/shBrushJScript.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
static/js/syntaxhighlighter/styles/shCoreDefault.css"/>
<script type="text/javascript">SyntaxHighlighter.all();</script>

<h2>
<span class="link"><a href="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
assignment/<?php echo $_smarty_tpl->getVariable('assignment')->value;?>
"><?php echo $_smarty_tpl->getVariable('assignment')->value;?>
</a></span>
by 
<span class="link"><a href="<?php echo $_smarty_tpl->getVariable('root_url')->value;?>
student/<?php echo $_smarty_tpl->getVariable('student')->value;?>
"><?php echo $_smarty_tpl->getVariable('student')->value;?>
</a></span> 
</h2>

<script type="text/javascript">

    var files = new Array();
    <?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('files')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value){
 $_smarty_tpl->tpl_vars['i']->value = $_smarty_tpl->tpl_vars['file']->key;
?>
    files[<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
] = '<?php echo $_smarty_tpl->tpl_vars['file']->value;?>
';
    <?php }} ?>
    
    // we need to wait for the syntax highlighter to finish before we access the modified dom
    $(document).ready(function(){
       setTimeout("setupCodeFiles()", 1000); 
    });
    
    function setupCodeFiles(){
        <?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('files')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value){
 $_smarty_tpl->tpl_vars['i']->value = $_smarty_tpl->tpl_vars['file']->key;
?>
    	code_files.push(new CodeFile('<?php echo $_smarty_tpl->tpl_vars['file']->value;?>
', <?php echo $_smarty_tpl->tpl_vars['i']->value;?>
));
    	<?php }} ?>
    }
    
    function hideall(){
    	 for(var i = 0; i < files.length; i++){	 
    		 document.getElementById("file" + i).className = "hidden";
    		 document.getElementById("comments" + i).className = "hidden";
    	 }
    }

    function display(fileNum){
    	 hideall();
    	 document.getElementById("file" + fileNum).className = "visible codeposition";	 
    	 document.getElementById("comments" + fileNum).className = "visible";
    }
    
    function getRangeFromComment(commentID){
    	///the commentID is of the form: cLOWER-HIGHER
    	var pattern = /c(\d+)-(\d+)/;
    	var result = pattern.exec(commentID);
    	if(result == null){
    		result = /c(\d+)/.exec(commentID);
    		var range = new LineRange(parseInt(result[1]), parseInt(result[1]));
    	}else{
    		var range = new LineRange(parseInt(result[1]), parseInt(result[2]));		
    	}
    	return range;
    }

    function edit(fileID, commentID){
    	var file = code_files[fileID];
    	var range = getRangeFromComment(commentID);
    	file.editComment(range, fileID); 
    }
</script>

<?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('files')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value){
 $_smarty_tpl->tpl_vars['i']->value = $_smarty_tpl->tpl_vars['file']->key;
?>
<div id="comments<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
"></div>
<?php }} ?>

<div id="filelist">
    <?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('files')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value){
 $_smarty_tpl->tpl_vars['i']->value = $_smarty_tpl->tpl_vars['file']->key;
?>
    <div class="link"><a href="javascript:display('<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
')"><?php echo $_smarty_tpl->tpl_vars['file']->value;?>
</a></div>
    <?php }} ?>
</div>

<?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('files')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value){
 $_smarty_tpl->tpl_vars['i']->value = $_smarty_tpl->tpl_vars['file']->key;
?>
<div id="file<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" class="hidden">
    <pre class="brush: js; highlight: [10,11,101,102]">
        
        <?php echo $_smarty_tpl->getVariable('file_contents')->value[$_smarty_tpl->tpl_vars['i']->value];?>

    </pre>
</div>
<?php }} ?>

<script>
display(0);
</script>

<!-- <a href="http://alexgorbatchev.com/SyntaxHighlighter/">Syntax Highlighter Credit</a> -->

<?php $_template = new Smarty_Internal_Template("footer.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
