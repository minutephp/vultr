<?php
/**
 * Created by: MinutePHP framework
 */
namespace App\Controller\Admin\Vultr {

    use Minute\Cache\FileCache;
    use Minute\Config\Config;
    use Minute\Error\VultrError;
    use Minute\Event\Dispatcher;
    use Minute\Event\DockerEvent;
    use Minute\Zip\ZipFile;
    use StringTemplate\Engine;
    use Vultr\Adapter\GuzzleHttpAdapter;
    use Vultr\VultrClient;

    class Deploy {
        const VultrKey = 'vultr';
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
         * @var Engine
         */
        private $engine;
        /**
         * @var FileCache
         */
        private $cache;

        /**
         * Deploy constructor.
         *
         * @param Config $config
         *
         * @param ZipFile $zipFile
         * @param Dispatcher $dispatcher
         *
         * @param Engine $engine
         *
         * @param FileCache $cache
         */
        public function __construct(Config $config, ZipFile $zipFile, Dispatcher $dispatcher, Engine $engine, FileCache $cache) {
            $this->config     = $config;
            $this->zipFile    = $zipFile;
            $this->dispatcher = $dispatcher;
            $this->engine     = $engine;
            $this->cache      = $cache;
        }

        public function index() {
            $vultr = $this->config->get(self::VultrKey);

            if ($apiKey = $vultr['api_key'] ?? null) {
                $servers = @$vultr['cron'] == 'standalone' ? ['web', 'worker'] : ['web'];
                $client  = new VultrClient(new GuzzleHttpAdapter($apiKey));
                $tags    = $this->config->getPublicVars();;
                $tags['email'] = $this->config->get('private/owner_email', sprintf('webmaster@%s', $tags['domain']));

                $dir    = realpath(__DIR__ . '/data');
                $db     = $vultr['database'];
                $domain = $tags['domain'];
                $aws    = $this->config->get('aws');
                $dns    = 'curl -H \'API-Key: {apiKey}\' https://api.vultr.com/v1/dns/create_record --data \'domain=' . $domain . '\' --data \'name=\' --data \'type=A\' --data "data=$IP"' . "\n" .
                          'curl -H \'API-Key: {apiKey}\' https://api.vultr.com/v1/dns/create_record --data \'domain=' . $domain . '\' --data \'name=*\' --data \'type=A\' --data "data=$IP"' . "\n";
                $cron   = '';

                $defaults = ['rds' => ['RDS_DB_NAME' => $db['name'], 'RDS_HOSTNAME' => $db['host'], 'RDS_PASSWORD' => $db['password'], 'RDS_USERNAME' => $db['username']],
                             'etc' => ['dataDir' => $dir]];

                if ($uploadCdn = $aws['uploads']['cdn_host'] ?? null) {
                    $client->dns()->createRecord($domain, $uploadCdn, 'CNAME', $aws['uploads']['cdn_cname']);
                }

                if ($assetsCdn = $aws['static']['cdn_host'] ?? null) {
                    $client->dns()->createRecord($domain, $assetsCdn, 'CNAME', $aws['static']['cdn_cname']);
                }

                if (!empty($vultr['clear_dns'])) {
                    $records = $client->dns()->getRecords($domain);

                    foreach ($records as $record) {
                        if ($record['type'] == 'A') {
                            $dns .= sprintf("curl -H 'API-Key: %s' https://api.vultr.com/v1/dns/delete_record --data 'domain=%s' --data 'RECORDID=%d'\n", $apiKey, $domain, $record['RECORDID']);
                        }
                    }
                }

                foreach ($servers as $server) {
                    $event = new DockerEvent(array_merge($aws['deployment'], $defaults), $server, $tags);
                    $this->dispatcher->fire(DockerEvent::DOCKER_INCLUDE_FILES, $event);
                    $zip = $this->zipFile->create($event->getFiles(), "$domain-deploy.zip", $event->getTags());

                    if (($server == 'worker') || (@$vultr['cron'] == 'shared')) {
                        $job  = 'docker exec -t \$(docker ps -q) php /var/www/vendor/minutephp/cron/cli/bin/cron-runner > /var/log/cron-runner';
                        $cron = sprintf('crontab -l | { cat; echo "* * * * * %s"; } | crontab -', $job);
                    }

                    $data    = file_get_contents("$dir/setup.sh");
                    $data    = $this->engine->render($data, array_merge(['domain' => $domain, 'update' => `base64 "$zip"`, 'dns' => $server == 'web' ? $dns : '', 'apiKey' => $apiKey,
                                                                         'cron' => $cron]));
                    $startup = "$domain-$server";

                    $scripts = $client->startupScript()->getList();

                    foreach ($scripts as $script) {
                        if ($script['name'] == $startup) {
                            $scriptId = $script['SCRIPTID'];
                            break;
                        }
                    }

                    if (!empty($scriptId)) {
                        $client->startupScript()->update($scriptId, $startup, $data);
                    } else {
                        $scriptId = $client->startupScript()->create($startup, $data);
                    }

                    if ($scriptId > 0) {
                        if ($vultr['dryRun'] == 'true') {
                            printf('<fieldset><leged>%s server</leged>', ucfirst($server));
                            printf('<p>Created script: <a href="https://my.vultr.com/startup/manage/?SCRIPTID=%d">%s</a></p>', $scriptId, $startup);
                            printf('<p><a class="btn btn-default" href="https://my.vultr.com/deploy/">Deploy server</a></p>');
                            printf('<hr><p><h3>Script:</h3><pre>%s</pre></p></fieldset>', $data);
                        } else {
                            $config = ['DCID' => $this->getRegionId($client), 'VPSPLANID' => $this->getPlanId($client), 'SCRIPTID' => $scriptId, 'label' => $domain,
                                       'SSHKEYID' => $this->getSshKeyId($client), 'APPID' => $this->getDockerAppId($client), 'OSID' => $this->getDockerOsId($client),
                                       'hostname' => "www.$domain", 'tag' => $domain, 'FIREWALLGROUPID' => $this->getFirewallId($client)];
                            $result = $client->server()->create($config);
                            printf('<p>Created server: <a href="https://my.vultr.com/subs/?SUBID=%d">%s</a></p>', $result['SUBID']);
                        }
                    }
                }
            } else {
                throw new VultrError("Api key is required for deployment");
            }
        }

        protected function getDockerAppId(VultrClient $client): string {
            return $this->cache->get("vultr-app-id", function () use ($client) {
                $result = current(array_filter($client->metaData()->getAppList(), function ($app) { return $app['name'] == 'Docker'; }));
                sleep(1);

                return $result['APPID'];
            }, 86400);
        }

        protected function getDockerOsId(VultrClient $client): string {
            return $this->cache->get("vultr-os-id", function () use ($client) {
                $result = current(array_filter($client->metaData()->getOsList(), function ($os) { return $os['name'] == 'Application'; }));
                sleep(1);

                return $result['OSID'];
            }, 86400 * 10);
        }

        protected function getRegionId(VultrClient $client, $region = 'Los Angeles'): string {
            return $this->cache->get("vultr-region-id", function () use ($client, $region) {
                $result = current(array_filter($client->region()->getList(), function ($r) use ($region) { return $r['name'] == $region; }));
                sleep(1);

                return $result['DCID'];
            }, 86400 * 365);
        }

        protected function getPlanId(VultrClient $client, $price = 5): string {
            return $this->cache->get("vultr-plan-id", function () use ($client, $price) {
                $result = current(array_filter($client->metaData()->getPlansList(), function ($plan) use ($price) {
                    return (empty($plan['deprecated']) && ($plan['plan_type'] == 'SSD') && ($plan['price_per_month'] == $price));
                }));
                sleep(1);

                return $result['VPSPLANID'];
            }, 86400 * 365);
        }

        protected function getFirewallId(VultrClient $client): string {
            return $this->cache->get("vultr-firewall-id", function () use ($client) {
                $result = current($client->firewall()->getGroupList());
                sleep(1);

                return $result['FIREWALLGROUPID'];
            }, 86400);
        }

        protected function getSshKeyId(VultrClient $client): string {
            return $this->cache->get("vultr-ssh-id", function () use ($client) {
                $result = current($client->sshKey()->getList());
                sleep(1);

                return $result['SSHKEYID'];
            }, 86400);
        }
    }
}