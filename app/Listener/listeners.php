<?php

/** @var Binding $binding */
use Minute\Docker\VultrDocker;
use Minute\Event\AdminEvent;
use Minute\Event\Binding;
use Minute\Event\DockerEvent;
use Minute\Event\TodoEvent;
use Minute\Menu\VultrMenu;
use Minute\Todo\VultrTodo;

$binding->addMultiple([
    //support
    ['event' => AdminEvent::IMPORT_ADMIN_MENU_LINKS, 'handler' => [VultrMenu::class, 'adminLinks']],
    //tasks
    ['event' => TodoEvent::IMPORT_TODO_ADMIN, 'handler' => [VultrTodo::class, 'getTodoList']],
    //docker
    ['event' => DockerEvent::DOCKER_INCLUDE_FILES, 'handler' => [VultrDocker::class, 'setup'], 'priority' => 1],
]);