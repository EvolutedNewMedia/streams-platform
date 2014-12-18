<?php namespace Anomaly\Streams\Platform\Ui\Table\Filter\Command;

use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ApplyTableFiltersCommand
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Ui\Table\Filter\Command
 */
class ApplyTableFiltersCommand
{

    /**
     * The table builder.
     *
     * @var \Anomaly\Streams\Platform\Ui\Table\TableBuilder
     */
    protected $builder;

    /**
     * The query object.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * Create a new ApplyTableFiltersCommand instance.
     *
     * @param Builder      $query
     * @param TableBuilder $builder
     */
    function __construct(Builder $query, TableBuilder $builder)
    {
        $this->query   = $query;
        $this->builder = $builder;
    }

    /**
     * Get the query object.
     *
     * @return Builder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get the table builder.
     *
     * @return TableBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }
}
