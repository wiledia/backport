<?php

namespace Wiledia\Backport\Form\Field;

use Wiledia\Backport\Form\Field;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Avatar extends Field
{
    use UploadField;

    /**
     * {@inheritdoc}
     */
    protected $view = 'backport::form.avatar';

    /**
     *  Validation rules.
     *
     * @var string
     */
    protected $rules = '';



    /**
     * Create a new File instance.
     *
     * @param string $column
     * @param array  $arguments
     */
    public function __construct($column, $arguments = [])
    {
        $this->initStorage();

        parent::__construct($column, $arguments);
    }

    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function defaultDirectory()
    {
        return config('backport.upload.directory.image');
    }

    /**
     * {@inheritdoc}
     */
    public function getValidator(array $input)
    {
        if (request()->has(static::FILE_DELETE_FLAG)) {
            return false;
        }

        if ($this->validator) {
            return $this->validator->call($this, $input);
        }

        /*
         * If has original value, means the form is in edit mode,
         * then remove required rule from rules.
         */
        if ($this->original() || is_string($input)) {
            $this->removeRule('required');
        }

        /*
         * Make input data validatable if the column data is `null`.
         */
        if (\Illuminate\Support\Arr::has($input, $this->column) && is_null(\Illuminate\Support\Arr::get($input, $this->column))) {
            $input[$this->column] = '';
        }

        $rules = $attributes = [];

        if (!$fieldRules = $this->getRules()) {
            return false;
        }

        $rules[$this->column] = $fieldRules;
        $attributes[$this->column] = $this->label;

        return Validator::make($input, $rules, $this->validationMessages, $attributes);
    }

    /**
     * Preview html for file-upload plugin.
     *
     * @return string
     */
    protected function preview()
    {
        return $this->objectUrl($this->value);
    }

    /**
     * Initialize the caption.
     *
     * @param string $caption
     *
     * @return string
     */
    protected function initialCaption($caption)
    {
        return basename($caption);
    }

    /**
     * @return array
     */
    protected function initialPreviewConfig()
    {
        return [
            ['caption' => basename($this->value), 'key' => 0],
        ];
    }

    /**
     * Render file upload field.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $this->options(['overwriteInitial' => true]);

        $this->setupDefaultOptions();

        if (!empty($this->value)) {
            $this->attribute('data-initial-preview', filter_var($this->preview(), FILTER_VALIDATE_URL));
            $this->attribute('data-initial-caption', $this->initialCaption($this->value));

            $this->setupPreviewOptions();
        }

        $options = json_encode($this->options);

        $this->script = <<<EOT
new bpAvatar('{$this->getElementClassString()}');
EOT;

        return parent::render();
    }
}
