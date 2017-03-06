<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 7/8/2016
 * Time: 7:57 PM
 */
namespace Minute\Menu {

    use Minute\Event\ImportEvent;

    class VultrMenu {
        public function adminLinks(ImportEvent $event) {
            $links = [
                'vultr' => ['title' => 'Vultr cloud', 'icon' => 'fa-cloud', 'priority' => 97, 'href' => '/admin/vultr']
            ];

            $event->addContent($links);
        }
    }
}