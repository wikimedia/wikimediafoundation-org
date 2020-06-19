<?php
/**
 * Created by PhpStorm.
 * User: xedin
 * Date: 4/2/2019
 * Time: 9:16 PM
 */

namespace Inpsyde\MultilingualPress2to3\Handler;


use cli\Progress;

class CompositeProgressHandler extends CompositeHandler
{
    /**
     * @var Progress
     */
    protected $progress;

    public function __construct($handlers, Progress $progress)
    {
        parent::__construct($handlers);
        $this->progress = $progress;
    }

    protected function _afterRun(HandlerInterface $handler)
    {
        $this->_getProgress()->tick();
    }

    protected function _beforeAll(array $handlers)
    {
        $this->_getProgress()->reset(count($handlers));
        $this->_getProgress()->display();
    }

    protected function _getProgress(): Progress
    {
        return $this->progress;
    }
}