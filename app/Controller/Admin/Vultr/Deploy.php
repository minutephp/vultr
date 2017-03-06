<?php
/**
 * Created by: MinutePHP framework
 */
namespace App\Controller\Admin\Vultr {

    use Minute\Config\Config;
    use Minute\Error\VultrError;
    use Minute\Event\Dispatcher;
    use Minute\Event\DockerEvent;
    use Minute\Zip\ZipFile;
    use Vultr\Adapter\GuzzleHttpAdapter;
    use Vultr\VultrClient;

    class Deploy {
        const VultrKey = 'vultr';
        /**
         * @var VultrClient
         */
        protected $client;
        /**
         * @var string
         */
        protected $apiKey;
        /**
         * @var Config
         */
        private $config;
        /**
         * @var ZipFile
         */
        private $zipFile;
        /**
         * @var Dispatcher
         */
        private $dispatcher;

        /**
         * Deploy constructor.
         *
         * @param Config $config
         *
         * @param ZipFile $zipFile
         * @param Dispatcher $dispatcher
         *
         * @throws VultrError
         */
        public function __construct(Config $config, ZipFile $zipFile, Dispatcher $dispatcher) {
            $this->config     = $config;
            $this->zipFile    = $zipFile;
            $this->dispatcher = $dispatcher;

            if ($this->apiKey = $this->config->get(self::VultrKey . '/api_key')) {
                $this->client = new VultrClient(new GuzzleHttpAdapter($this->apiKey));
            } else {
                throw new VultrError("Api key is required for deployment");
            }
        }

        public function index() {
            $tags = $this->config->getPublicVars();;
            $tags['email'] = $this->config->get('private/owner_email', sprintf('webmaster@%s', $tags['domain']));

            $dir      = realpath(__DIR__ . '/data');
            $domain   = $tags['domain'];
            $settings = $this->config->get('aws');
            $defaults = ['rds' => ['RDS_DB_NAME' => 1, 'RDS_HOSTNAME' => 1, 'RDS_PASSWORD' => 1, 'RDS_USERNAME' => 1], 'etc' => ['dataDir' => $dir]];

            $event = new DockerEvent(array_merge($settings['deployment'], $defaults), 'web', $tags);
            $this->dispatcher->fire(DockerEvent::DOCKER_INCLUDE_FILES, $event);
            $zip = $this->zipFile->create($event->getFiles(), "$domain-deploy.zip", $event->getTags());

            $data    = file_get_contents("$dir/setup.sh");
            $data    = strtr($data, array_merge(['%domain%' => $domain, '%update%' => `base64 "$zip"`, '%apiKey%' => $this->apiKey]));
            $startup = "$domain-docker";

            $scripts = $this->client->startupScript()->getList();

            foreach ($scripts as $script) {
                if ($script['name'] == $startup) {
                    $scriptId = $script['SCRIPTID'];
                    break;
                }
            }

            if (!empty($scriptId)) {
                $this->client->startupScript()->update($scriptId, $startup, $data);
            } else {
                $scriptId = $this->client->startupScript()->create($startup, $data);
            }

            print $scriptId;

            //$this->client->startupScript()->create("$name-startup", $data);

            //print $data;
        }
    }
}