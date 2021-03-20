<?php


use VK\Client\VKApiClient;
use VK\Exceptions\Api\VKApiBlockedException;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;

class Core
{
    private ?VKApiClient $vk = null;

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

    private function work(): void
    {
        $this->vk = new VKApiClient();
        try {
            print_r(array_map(static fn($post) => $post['text'], $this->getPosts()));
        } catch (Exception $e) {
            print_r($e);
        }
    }

    /**
     * @return array
     * @throws VKApiBlockedException
     * @throws VKApiException
     * @throws VKClientException
     */
    private function getPostsWithText(): array
    {
        $posts = $this->getPosts();
        return array_filter($posts['items'], static fn($post) => $post['text'] !== '');
    }

    /**
     * @return array
     * @throws VKApiBlockedException
     * @throws VKApiException
     * @throws VKClientException
     */
    private function getPosts(): array
    {
        return $this->vk->wall()->get(
            $_ENV['SERVICE_KEY'],
            ['domain' => 'jumoreski', 'count' => 100, 'filter' => 'owner']
        );
    }

    /**
     * @return int
     * @throws VKApiBlockedException
     * @throws VKApiException
     * @throws VKClientException
     */
    private function getCountPosts(): int
    {
        return $this->getPosts()['count'];
    }
}