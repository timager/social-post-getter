<?php


use VK\Client\VKApiClient;

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
            $post = $this->getRandomPost($_GET['group'] ?? 'jumoreski');
            $this->printPost($post);
        } catch (Exception $e) {
            $this->printHeader();
            $this->printError($e);
        }
    }

    private function printError(Exception $e): void
    {
        ?>
        <p>Выберите другой источник, этот не сработал :(</p>
        <?php
    }

    private function getImages(array $post): array
    {
        if(!array_key_exists('attachments', $post)){
            return [];
        }
        $images = array_filter($post['attachments'], static fn($attachment) => $attachment['type'] === 'photo');
        $urls = [];
        foreach ($images as $image) {
            $sizes = $image['photo']['sizes'];
            $maxSize = null;
            foreach ($sizes as $size) {
                if ($maxSize === null || ($size['width'] > $maxSize['width'] && $size['width'] < 600)) {
                    $maxSize = $size;
                }
            }
            $urls[] = $maxSize['url'];
        }
        return $urls;
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
        return $this->getPosts($group, 1, random_int(0, $count - 1))['items'][0];
    }

    private function printHeader(string $group = ''): void
    {
        ?>
        <h1 style="font-size: 10vh">Рандомный пост</h1>
        <div style="font-size: 4vh">
            <form>
                <label>
                    <input style="font-size: 4vh;" placeholder="вк айди" name="group" value="<?= $group ?>" type="text">
                </label>
                <button style="font-size: 4vh;">Получить новый</button>
            </form>
        </div>
        <?php
    }

    private function printPost(array $post): void
    {
        $group = $_GET['group'] ?? 'jumoreski';
        $this->printHeader($group)
        ?>
        <div style="margin: auto; font-size: 5vh; max-width: 80%;">
            <p style="white-space: pre-wrap"><?= print_r($post['text'], true) ?></p>
            <?php
            foreach ($this->getImages($post) as $url) {
                ?>
                <div><img style="width: 60%; height: auto" src="<?= $url ?>" alt="pic"/></div>
                <?php
            }
            ?>
        </div>
        <?php
    }
}