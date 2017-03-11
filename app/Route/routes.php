<?php

/** @var Router $router */
use Minute\Model\Permission;
use Minute\Routing\Router;

$router->get('/admin/vultr', null, 'admin', 'm_configs[type] as configs')
       ->setReadPermission('configs', 'admin')->setDefault('type', 'vultr');
$router->post('/admin/vultr', null, 'admin', 'm_configs as configs')
       ->setAllPermissions('configs', 'admin');

$router->get('/admin/vultr/deploy', 'Admin/Vultr/Deploy', 'admin')->setDefault('_noView', true);

$router->get('/admin/vultr/db-format', 'Admin/Vultr/DbCopy', 'admin')->setDefault('_noView', true);