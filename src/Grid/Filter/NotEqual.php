<?php

namespace Wiledia\Backport\Grid\Filter;

class NotEqual extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function condition($inputs)
    {
        $value = \Illuminate\Support\Arr::get($inputs, $this->column);

        if (!isset($value)) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, '!=', $this->value);
    }
}
