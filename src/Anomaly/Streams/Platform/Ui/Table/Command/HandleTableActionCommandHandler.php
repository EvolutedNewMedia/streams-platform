<?php namespace Anomaly\Streams\Platform\Ui\Table\Command;

use Anomaly\Streams\Platform\Ui\Table\Contract\TableActionInterface;
use Anomaly\Streams\Platform\Ui\Table\Table;

/**
 * Class HandleTableActionCommandHandler
 *
 * Runs the executed table action handler.
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Ui\Table\Command
 */
class HandleTableActionCommandHandler
{

    /**
     * Handle the command.
     *
     * @param HandleTableActionCommand $command
     * @return null
     */
    public function handle(HandleTableActionCommand $command)
    {
        $table = $command->getTable();

        $actions = $table->getActions();

        $key = $table->getPrefix() . 'action';

        /**
         * If there is a submitted action to execute
         * then go ahead and handle it.
         */
        if ($executing = app('request')->get($key)) {

            $presets  = $table->getPresets();
            $expander = $table->getExpander();

            /**
             * Look through actions and find a match.
             */
            foreach ($actions as $slug => $action) {

                // Expand and automate.
                $action = $expander->expand($slug, $action);
                $action = $presets->setActionPresets($action);

                // Found the executing action? Nice, run it.
                if ($executing == $table->getPrefix() . $action['slug']) {

                    $action['handler'] = $this->getHandler($action, $table);

                    $this->runHandler($action, $table);

                    app('streams.messages')->flash();
                }
            }

            // Make sure we go back to where we came from.
            $table->setResponse(redirect(referer(url(app('request')->path()))));
        }
    }

    /**
     * Get the handler.
     *
     * @param array $action
     * @param Table $table
     * @return mixed
     */
    protected function getHandler(array $action, Table $table)
    {
        /**
         * If the handler is a string then auto complete
         * the class path if needed based on the table
         * object being used.
         */
        if (is_string($action['handler'])) {

            $utility = $table->getUtility();

            return $utility->autoComplete('Action\\' . $action['handler'], $table);
        }

        return $action['handler'];
    }

    /**
     * Run the handler.
     *
     * @param array $action
     * @param Table $table
     */
    protected function runHandler(array $action, Table $table)
    {
        $ids = (array)app('request')->get($table->getPrefix() . 'id');

        /**
         * If the handler is a string call it
         * through the container.
         */
        if (is_string($action['handler'])) {

            app()->call($action['handler'], compact('table', 'ids'));
        }

        /**
         * If the handler is a closure call it
         * through the container.
         */
        if ($action['handler'] instanceof \Closure) {

            app()->call($action['handler'], compact('table', 'ids'));
        }

        /**
         * If the handler is an instance of the interface
         * then authorize and run it's handle method.
         */
        if ($action['handler'] instanceof TableActionInterface) {

            if ($action['handler']->authorize($table) !== false) {

                $action['handler']->handle($table, $ids);
            }
        }
    }
}
 