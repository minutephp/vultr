<?php
/**
 * Created by: MinutePHP framework
 */
namespace App\Controller\Admin\Vultr {

    use Minute\Config\Config;
    use Minute\Db\DbFormatter;

    class DbCopy {
        /**
         * @var Config
         */
        private $config;
        /**
         * @var DbFormatter
         */
        private $dbFormatter;

        /**
         * DbCopy constructor.
         *
         * @param Config $config
         * @param DbFormatter $dbFormatter
         */
        public function __construct(Config $config, DbFormatter $dbFormatter) {
            $this->config      = $config;
            $this->dbFormatter = $dbFormatter;
        }

        public function index($tweak) {
            if ($db = $this->config->get(Deploy::VultrKey . '/database')) { //encrypt in next version
                $remote = ['RDS_DB_NAME' => $db['name'], 'RDS_HOSTNAME' => $db['host'], 'RDS_PASSWORD' => $db['password'], 'RDS_USERNAME' => $db['username']];
                $this->dbFormatter->format($remote, $tweak);
            }

            return 'OK';
        }
    }
}