<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 8/28/2016
 * Time: 1:21 AM
 */
namespace Minute\Docker {

    use Minute\Aws\Client;
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
            $domain   = $this->config->getPublicVars('domain');
            $tags     = $event->getTags();

            $event->addContent('ssl.sh', file_get_contents("$dataDir/ssl.sh"));

            $event->addContent('Dockerfile', 'RUN a2enmod ssl');
            $event->addContent('Dockerfile', 'ADD ssl.sh /tmp/ssl.sh');
            $event->addContent('Dockerfile', 'RUN chmod +x /tmp/ssl.sh');
            $event->addContent('Dockerfile', 'RUN /tmp/ssl.sh');

            $tags['rewrites'] .= "\n\tRewriteCond %{HTTPS} off\n\t" .
                                 "RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]\n";

            if ($cdn = $this->config->get(Client::AWS_KEY . '/uploads/cdn_cname')) {
                $tags['rewrites'] .= "\n\tRewriteCond %{HTTP_HOST} ^uploads.$domain$\n\t" .
                                     "RewriteRule ^(.*)$ %{ENV:REQUEST_SCHEME}://$cdn$1 [R=301,L]\n\n\t";

            }

            $event->setTags($tags);
        }
    }
}
