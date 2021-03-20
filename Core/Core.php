<?php


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use VK\Client\VKApiClient;
use VK\Exceptions\VKClientException;
use VK\Exceptions\VKOAuthException;
use VK\OAuth\Scopes\VKOAuthUserScope;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;

class Core
{
    private const PUBLIC_URL = 'https://vk.com/club203419343';

    private function loadEnv(): void
    {
        $dotenv = Dotenv\Dotenv::createImmutable('../');
        $dotenv->load();
    }

    public function run(): void
    {
        $this->loadEnv();
        $this->work();
    }

    /**
     * @return string
     * @throws VKClientException
     * @throws VKOAuthException
     */
    private function auth(): string
    {
        $oauth = new VKOAuth();
        $response = $oauth->getAccessToken($_ENV['CLIENT_ID'], $_ENV['SECRET'], self::PUBLIC_URL,123);

        return $response['access_token'];
    }

//    private function getBrowserUrl(): string
//    {
//        return (new VKOAuth())->getAuthorizeUrl(
//            VKOAuthResponseType::CODE,
//            self::CLIENT_ID,
//            self::PUBLIC_URL,
//            VKOAuthDisplay::PAGE,
//            [VKOAuthUserScope::WALL],
//            self::SECRET
//        );
//    }

    private function work(): void
    {
        $vk = new VKApiClient();

        try {
            $token = $this->auth();
            $posts = $vk->wall()->get($token, ['domain' => 'jumoreski', 'count' => 100, 'filter' => 'owner']);
            print_r($posts);
        } catch (Exception $e) {
            print_r($e);
        }
    }
}