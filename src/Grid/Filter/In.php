<?php

namespace Wiledia\Backport\Grid\Filter;

class In extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected $query = 'whereIn';

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return mixed
     */
    public function condition($inputs)
    {
        $value = \Illuminate\Support\Arr::get($inputs, $this->column);

        if (is_null($value)) {
            return;
        }

        $this->value = (array) $value;

        return $this->buildCondition($this->column, $this->value);
    }
}
