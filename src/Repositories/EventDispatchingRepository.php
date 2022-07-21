<?php

namespace Railroad\Railforums\Repositories;

use Railroad\Resora\Repositories\RepositoryBase;
use Railroad\Railforums\Repositories\Traits\SoftDelete;
use Railroad\Resora\Repositories\Traits\CreateReadUpdateDestroy;
use Illuminate\Events\Dispatcher;

abstract class EventDispatchingRepository extends RepositoryBase
{
    use SoftDelete, CreateReadUpdateDestroy {
        SoftDelete::delete as baseDelete;
        SoftDelete::deleteByUserId as baseDeleteByUserId;
        CreateReadUpdateDestroy::create as baseCreate;
        CreateReadUpdateDestroy::read as baseRead;
        CreateReadUpdateDestroy::update as baseUpdate;
        CreateReadUpdateDestroy::destroy as baseDestroy;
    }

    protected $dispatcher;

    protected $dispatching;

    public function getDispatcher()
    {
        if (!$this->dispatcher) {
            $this->dispatcher = app(Dispatcher::class);
        }

        return $this->dispatcher;
    }

    public function create($attributes)
    {
        $dispatched = $this->dispatching;

        $this->dispatching = true;

        $entity = $this->baseCreate($attributes);

        if ($dispatched || !$entity) {
            return $entity;
        }

        $event = $this->getCreateEvent($entity);

        if ($event) {
            $this->getDispatcher()->fire($event);
        }

        $this->dispatching = false;
        
        return $entity;
    }

    public function read($attributes)
    {
        $dispatched = $this->dispatching;

        $this->dispatching = true;

        $entity = $this->baseRead($attributes);

        if ($dispatched || !$entity) {
            return $entity;
        }

        $event = $this->getReadEvent($entity);

        if ($event) {
            $this->getDispatcher()->fire($event);
        }

        $this->dispatching = false;
        
        return $entity;
    }

    public function update($id, $attributes = null)
    {
        $dispatched = $this->dispatching;

        $this->dispatching = true;

        $entity = $this->baseUpdate($id, $attributes);

        if ($dispatched || !$entity) {

            return $entity;
        }

        $event = $this->getUpdateEvent($entity);

        if ($event) {
            $this->getDispatcher()->fire($event);
        }

        $this->dispatching = false;
        
        return $entity;
    }

    public function destroy($attributes)
    {
        $dispatched = $this->dispatching;

        $this->dispatching = true;

        $entity = $this->baseRead($attributes);

        if ($dispatched || !$entity) {
            return false;
        }

        $event = $this->getDestroyEvent($entity);

        if ($event) {
            $this->getDispatcher()->fire($event);
        }

        $this->dispatching = false;
        
        return $this->baseDestroy($attributes);
    }

    public function delete($id)
    {
        $dispatched = $this->dispatching;

        $this->dispatching = true;

        $deleteResult = $this->baseDelete($id);

        if ($dispatched || !$deleteResult) {
            return $deleteResult;
        }

        $event = $this->getDeleteEvent($id);

        if ($event) {
            $this->getDispatcher()->fire($event);
        }

        $this->dispatching = false;

        return $deleteResult;
    }

    public function deleteByUserId($userId)
    {
        $dispatched = $this->dispatching;

        $this->dispatching = true;

        $deleteResult = $this->baseDeleteByUserId($userId);

        if ($dispatched || !$deleteResult) {
            return $deleteResult;
        }

        $this->dispatching = false;

        return $deleteResult;
    }

    public abstract function getCreateEvent($entity);
    public abstract function getReadEvent($entity);
    public abstract function getUpdateEvent($entity);
    public abstract function getDestroyEvent($entity);
    public abstract function getDeleteEvent($entity);
}
