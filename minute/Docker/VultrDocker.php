<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 8/28/2016
 * Time: 1:21 AM
 */
namespace Minute\Docker {

    use Minute\Config\Config;
    use Minute\Event\DockerEvent;

    class VultrDocker {
        /**
         * @var Config
         */
        private $config;

        /**
         * DockerFile constructor.
         *
         * @param Config $config
         */
        public function __construct(Config $config) {
            $this->config = $config;
        }

        public function setup(DockerEvent $event) {
            $settings = $event->getSettings();
            $dataDir  = $settings['etc']['dataDir'];

            $event->addContent('ssl.sh', file_get_contents("$dataDir/ssl.sh"));

            $event->addContent('Dockerfile', 'RUN a2enmod ssl');
            $event->addContent('Dockerfile', 'ADD ssl.sh /tmp/ssl.sh');
            $event->addContent('Dockerfile', 'RUN chmod +x /tmp/ssl.sh');
            $event->addContent('Dockerfile', 'RUN /tmp/ssl.sh');
            $event->addContent('Dockerfile', 'RUN ./letsencrypt-auto --noninteractive --agree-tos --apache --email=admin@{domain} -d www.{domain}');
        }
    }
}
