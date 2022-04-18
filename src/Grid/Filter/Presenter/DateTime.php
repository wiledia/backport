<?php

namespace Wiledia\Backport\Grid\Filter\Presenter;

use Wiledia\Backport\Backport;

class DateTime extends Presenter
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $format = 'YYYY-MM-DD HH:mm:ss';

    /**
     * DateTime constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $this->getOptions($options);
    }

    /**
     * @param array $options
     *
     * @return mixed
     */
    protected function getOptions(array  $options) : array
    {
        $options['format'] = \Illuminate\Support\Arr::get($options, 'format', $this->format);
        $options['locale'] = \Illuminate\Support\Arr::get($options, 'locale', config('app.locale'));

        return $options;
    }

    protected function prepare()
    {
        $script = "$('#{$this->filter->getId()}').datetimepicker(".json_encode($this->options).');';

        Backport::script($script);
    }

    public function variables() : array
    {
        $this->prepare();

        return [
            'group' => $this->filter->group,
        ];
    }
}
