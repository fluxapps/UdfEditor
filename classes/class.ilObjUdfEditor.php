<?php

use srag\Plugins\UdfEditor\Exception\UDFNotFoundException;

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilObjUdfEditor
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilObjUdfEditor extends ilObjectPlugin
{
    protected xudfSetting $settings;

    protected function initType(): void
    {
        $this->type = ilUdfEditorPlugin::PLUGIN_ID;
    }

    protected function doCreate(bool $clone_mode = false): void
    {
        $xudfSetting = new xudfSetting();
        $xudfSetting->setObjId($this->getId());
        $xudfSetting->create();
    }

    protected function beforeDelete(): bool
    {
        xudfSetting::find($this->getId())->delete();
        return true;
    }

    /**
     * @param self $new_obj
     * @param      $a_target_id
     * @param null $a_copy_id
     */
    protected function doCloneObject($new_obj, $a_target_id, $a_copy_id = null): void
    {
        $this->cloneSettings($new_obj);
        $this->cloneContentElements($new_obj);
        $this->clonePageObject($new_obj);
    }

    public function getStyleSheetId(): void
    {
        ilObjStyleSheet::lookupObjectStyle($this->getId());
    }

    public function getSettings(): xudfSetting
    {
        $this->settings = xudfSetting::find($this->id);
        return $this->settings;
    }

    protected function cloneSettings(ilObjUdfEditor $new_obj): void
    {
        $old_settings = $this->getSettings();
        $new_settings = $new_obj->getSettings();

        $new_settings->setAdditionalNotification($old_settings->getAdditionalNotification());
        $new_settings->setMailNotification($old_settings->hasMailNotification());
        $new_settings->setShowInfoTab($old_settings->isShowInfoTab());
        $new_settings->update();
    }

    protected function cloneContentElements(ilObjUdfEditor $new_obj): void
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

    protected function clonePageObject(ilObjUdfEditor $new_obj): void
    {
        $old_page_object = new xudfPageObject($this->getId());
        $old_page_object->copy($new_obj->getId());
    }
}