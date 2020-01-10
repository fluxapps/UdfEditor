<?php

use srag\Plugins\UdfEditor\Exception\UDFNotFoundException;

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilObjUdfEditor
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilObjUdfEditor extends ilObjectPlugin {

    /**
     * @var xudfSetting
     */
    protected $settings;

    /**
     * @return string
     */
    protected function initType() {
        $this->type = ilUdfEditorPlugin::PLUGIN_ID;
    }

    /**
     *
     */
    protected function doCreate() {
        $xudfSetting = new xudfSetting();
        $xudfSetting->setObjId($this->getId());
        $xudfSetting->create();
    }

    /**
     *
     */
    protected function beforeDelete() {
        xudfSetting::find($this->getId())->delete();
    }


    /**
     * @param self $new_obj
     * @param      $a_target_id
     * @param null $a_copy_id
     */
    protected function doCloneObject($new_obj, $a_target_id, $a_copy_id = null)
    {
        $this->cloneSettings($new_obj);
        $this->cloneContentElements($new_obj);
        $this->clonePageObject($new_obj);
    }


    /**
     *
     */
    public function getStyleSheetId() {
        ilObjStyleSheet::lookupObjectStyle($this->getId());
    }


    /**
     * @return xudfSetting
     */
    public function getSettings()
    {
        if (!($this->settings instanceof xudfSetting)) {
            $this->settings = xudfSetting::find($this->id);
        }
        return $this->settings;
    }


    /**
     * @param self $new_obj
     */
    protected function cloneSettings($new_obj)
    {
        $old_settings = $this->getSettings();
        $new_settings = $new_obj->getSettings();

        $new_settings->setAdditionalNotification($old_settings->getAdditionalNotification());
        $new_settings->setMailNotification($old_settings->hasMailNotification());
        $new_settings->setShowInfoTab($old_settings->isShowInfoTab());
        $new_settings->update();
    }


    /**
     * @param self $new_obj
     */
    protected function cloneContentElements($new_obj)
    {
        /** @var xudfContentElement $old_content_element */
        foreach (xudfContentElement::where(['obj_id' => $this->getId()])->get() as $old_content_element) {
            $new_content_element = new xudfContentElement();
            $new_content_element->setObjId($new_obj->getId());
            try {
                $new_content_element->setTitle($old_content_element->getTitle());
            } catch (UDFNotFoundException $e) {
                $new_content_element->setTitle('UDF not found');
            }
            $new_content_element->setDescription($old_content_element->getDescription());
            $new_content_element->setIsSeparator($old_content_element->isSeparator());
            $new_content_element->setSort($old_content_element->getSort());
            $new_content_element->setUdfFieldId($old_content_element->getUdfFieldId());
            $new_content_element->create();
        }
    }


    /**
     * @param self $new_obj
     */
    protected function clonePageObject($new_obj)
    {
        $old_page_object = new xudfPageObject($this->getId());
        $old_page_object->copy($new_obj->getId());
    }
}