<?php

namespace Wiledia\Backport\Grid\Filter;

class Gt extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected $view = 'backport::filter.gt';

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return array|mixed|void
     */
    public function condition($inputs)
    {
        $value = \Illuminate\Support\Arr::get($inputs, $this->column);

        if (is_null($value)) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, '>=', $this->value);
    }
}
