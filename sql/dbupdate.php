<#1>
<?php
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/UdfEditor/classes/Settings/class.xudfSetting.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/UdfEditor/classes/Content/class.xudfContentElement.php";
xudfSetting::updateDB();
xudfContentElement::updateDB();
?>
<#2>
<?php
global $DIC;
$DIC->database()->modifyTableColumn('copg_pobj_def', 'component', array('length' => 120));
$DIC->database()->insert('copg_pobj_def', array(
    'parent_type' => array('text', 'xudf'),
    'class_name' => array('text', 'xudfPageObject'),
    'directory' => array('text', 'classes/Content/PageEditor'),
    'component' => array('text', 'Customizing/global/plugins/Services/Repository/RepositoryObject/UdfEditor')
));
?>
