<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 11/5/2016
 * Time: 11:04 AM
 */
namespace Minute\Todo {

    use App\Model\MPage;
    use Minute\Config\Config;
    use Minute\Event\ImportEvent;

    class VultrTodo {
        /**
         * @var TodoMaker
         */
        private $todoMaker;
        /**
         * @var Config
         */
        private $config;

        /**
         * MailerTodo constructor.
         *
         * @param TodoMaker $todoMaker - This class is only called by TodoEvent (so we assume TodoMaker is be available)
         * @param Config $config
         */
        public function __construct(TodoMaker $todoMaker, Config $config) {
            $this->todoMaker = $todoMaker;
            $this->config    = $config;
        }

        public function getTodoList(ImportEvent $event) {
            $todos[] = ['name' => 'Setup Vultr API', 'description' => 'Allows deployment to Vultr.com',
                        'status' => $this->config->get('vultr/api') ? 'complete' : 'incomplete', 'link' => '/admin/vultr'];

            $event->addContent(['Support' => $todos]);
        }
    }
}