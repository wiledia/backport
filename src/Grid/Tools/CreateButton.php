<?php

namespace Wiledia\Backport\Grid\Tools;

use Wiledia\Backport\Grid;

class CreateButton extends AbstractTool
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Create a new CreateButton instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render()
    {
        if (!$this->grid->allowCreation()) {
            return '';
        }

        $new = trans('admin.new');

        return <<<EOT

<div class="btn-group pull-right ml-1">
    <a href="{$this->grid->getCreateUrl()}" class="btn btn-sm btn-success" title="{$new}">
        <i class="fa fa-plus"></i><span class="d-none d-sm-inline-block">&nbsp;&nbsp;{$new}</span>
    </a>
</div>

EOT;
    }
}
