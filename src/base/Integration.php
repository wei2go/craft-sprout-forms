<?php

namespace barrelstrength\sproutforms\base;

use barrelstrength\sproutforms\elements\Entry;
use barrelstrength\sproutforms\elements\Form;
use craft\base\Model;
use Craft;

/**
 * Class IntegrationType
 *
 * @package Craft
 */
abstract class Integration extends Model
{
    /**
     * @var Entry
     */
    public $entry;

    /**
     * @var Form
     */
    public $form;

    /**
     * @var boolean
     */
    public $hasFieldMapping = false;

    /**
     * @var array|null The fields mapped
     */
    public $fieldsMapped;

    /**
     * Name of the Integration
     *
     * @return mixed
     */
    abstract public function getName();

    /**
     * Return Class name as Type
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Send the submission to the desired endpoint
     *
     * @return boolean
     */
    abstract public function submit();

    /**
     * Settings that help us customize the Field Mapping Table
     *
     * Each settings template will also call a Twig Field Mapping Table Macro to help with the field mapping (can we just have a Twig Macro that wraps the default Craft Table for this and outputs two columns?)
     */
    public function getSettingsHtml() {}

    /**
     * Process the submission and field mapping settings to get the payload. Resolve the field mapping.
     *
     * @return mixed
     */
    public function resolveFieldMapping() {
        return $this->fieldsMapped ?? [];
    }

    /**
     * Returns a default field mapping html
     *
     * @return string
     * @throws \yii\base\Exception
     */
    public function getFieldMappingSettingsHtml()
    {
        if (!$this->hasFieldMapping){
            return '';
        }

        if (empty($this->fieldsMapped)) {
            // Give it a default row
            // @todo show all the current fields
            $this->fieldsMapped = [['label' => '', 'value' => '']];
        }

        $rendered = Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'editableTableField',
            [
                [
                    'label' => Craft::t('sprout-forms', 'Field Mapping'),
                    'instructions' => Craft::t('sprout-forms', 'Define your field mapping.'),
                    'id' => 'fieldsMapped',
                    'name' => 'fieldsMapped',
                    'addRowLabel' => Craft::t('sprout-forms', 'Add a field mapping'),
                    'cols' => [
                        'label' => [
                            'heading' => Craft::t('sprout-forms', 'Form Field'),
                            'type' => 'select',
                            'options' => $this->getFormFieldsAsOptions()
                        ],
                        'value' => [
                            'heading' => Craft::t('sprout-forms', 'Api Field'),
                            'type' => 'singleline',
                            'class' => 'code'
                        ]
                    ],
                    'rows' => $this->fieldsMapped
                ]
            ]);

        return $rendered;
    }

    /**
     * @return array
     */
    public function getFormFieldsAsOptions()
    {
        $fields = $this->form->getFields();
        $options = [
            [
                'label' => 'Id',
                'value' => 'id'
            ],
            [
                'label' => 'Title',
                'value' => 'title'
            ],
            [
                'label' => 'Ip Address',
                'value' => 'ipAddress'
            ]
        ];

        foreach ($fields as $field) {
            $options[] = [
                'label' => $field->handle,
                'value' => $field->handle
            ];
        }

        return $options;
    }
}
