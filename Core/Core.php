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
            print_r($this->getRandomPost('jumoreski')['text']);
        } catch (Exception $e) {
            print_r($e);
        }
    }

    /**
     * @param  string  $group
     * @param  int  $count
     * @param  int  $offset
     * @return array
     * @throws Exception
     */
    private function getPosts(string $group, int $count = 1, int $offset = 0): array
    {
        return $this->vk->wall()->get(
            $_ENV['SERVICE_KEY'],
            ['domain' => $group, 'count' => $count, 'offset' => $offset]
        );
    }

    /**
     * @param  string  $group
     * @return int
     * @throws Exception
     */
    private function getCountPosts(string $group): int
    {
        return $this->getPosts($group)['count'];
    }

    /**
     * @param  string  $group
     * @return array
     * @throws Exception
     */
    private function getRandomPost(string $group): array
    {
        $count = $this->getCountPosts($group);
        do {
            $post = $this->getPosts($group, 1, random_int(0, $count - 1))['items'][0];
        } while (empty($post['text']));
        return $post;
    }
}