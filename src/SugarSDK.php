<?php

namespace Altoros\Sugar;


use Altoros\Sugar\Traits\Singleton;
use SugarAPI\SDK\SugarAPI;

class SugarSDK
{

    use Singleton;

    /**
     * @param bool $reinit
     *
     * @return SugarAPI
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    public static function getInstance(bool $reinit = false): SugarAPI
    {
        if (self::$instance === null || $reinit) {
            $config = [
                'server'      => getenv('SUGAR_SDK_SERVER'),
                'version'     => getenv('SUGAR_SDK_VERSION'),
                'credentials' => [
                    'username'      => getenv('SUGAR_SDK_USERNAME'),
                    'password'      => getenv('SUGAR_SDK_PASSWORD'),
                    'client_id'     => getenv('SUGAR_SDK_CLIENT_ID'),
                    'client_secret' => getenv('SUGAR_SDK_CLIENT_SECRET'),
                    'platform'      => getenv('SUGAR_SDK_PLATFORM'),
                ],
            ];

            $api = new SugarAPI($config['server'], $config['credentials']);
            $api->setVersion($config['version']);

            $tokenFile = (base_path() ?? __DIR__) . getenv('SUGAR_SDK_TOKEN_FILE');

            /**
             * Инициализация соединения
             */

            /** Проверка наличия token файла */
            if (!file_exists($tokenFile)) {
                if (!is_dir(dirname($tokenFile))) {
                    mkdir(dirname($tokenFile), 755, true);
                }
                file_put_contents($tokenFile, '');
            }

            /** Авторизация по токену */
            $token = unserialize(file_get_contents($tokenFile), ['allowed_classes' => ['stdClass']]);
            if ($token instanceof \stdClass) {
                $api->setToken($token);
            } else {
                $api->login();

                if ($api->getToken()) {
                    file_put_contents($tokenFile, serialize($api->getToken()));
                }
            }

            /** Авторизация без токена */
            if (!$api->authenticated()) {
                $api->oauth2Refresh();

                if ($api->getToken()) {
                    file_put_contents($tokenFile, serialize($api->getToken()));
                }
            }

            $me = $api->me()->execute()->getResponse();
            if (!$api->authenticated() || $me->getStatus() === 401) {
                $api->login();

                if ($api->getToken()) {
                    file_put_contents($tokenFile, serialize($api->getToken()));
                }
            }

            self::$instance = $api;
        }

        return self::$instance;
    }

}
