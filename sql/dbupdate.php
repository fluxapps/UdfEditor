<#1>
<?php
xudfSetting::updateDB();
xudfContentElement::updateDB();
?>
<#2>
<?php
global $DIC;
$DIC->database()->modifyTableColumn('copg_pobj_def', 'component', array('length' => 120));
$sql_query = $DIC->database()->query('SELECT * FROM copg_pobj_def WHERE parent_type = "xudf"');
if ($DIC->database()->numRows($sql_query) === 0) {
	$DIC->database()->insert('copg_pobj_def', array(
		'parent_type' => array('text', 'xudf'),
		'class_name' => array('text', 'xudfPageObject'),
		'directory' => array('text', 'classes/Content/PageEditor'),
		'component' => array('text', 'Customizing/global/plugins/Services/Repository/RepositoryObject/UdfEditor')
	));
}
?>
<#3>
<?php
xudfSetting::updateDB();
?>
<#4>
<?php
// TODO: if this gets deleted again, just plant it in xudfPageObjectGUI::__construct e.g.
global $DIC;
$sql_query = $DIC->database()->query('SELECT * FROM copg_pobj_def WHERE parent_type = "xudf"');
if ($DIC->database()->numRows($sql_query) === 0) {
	$DIC->database()->insert('copg_pobj_def', array(
		'parent_type' => array('text', 'xudf'),
		'class_name' => array('text', 'xudfPageObject'),
		'directory' => array('text', 'classes/Content/PageEditor'),
		'component' => array('text', 'Customizing/global/plugins/Services/Repository/RepositoryObject/UdfEditor')
	));
}
?>
<#5>
<?php
xudfSetting::updateDB();
?>
<#6>
<?php
xudfContentElement::updateDB();
xudfLogEntry::updateDB();
xudfSetting::updateDB();
?>
