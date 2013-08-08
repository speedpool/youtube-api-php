<?php

class Youtube
{
    const CHANNELS_LIST_URL = 'https://www.googleapis.com/youtube/v3/channels';

    const PLAYLIST_ITEMS_URL = 'https://www.googleapis.com/youtube/v3/playlistItems';

    protected $apiKey;

    public function __construct(array $config)
    {
        $this->apiKey = $config['apiKey'];

    }

    public function listChannels($part, $params = array())
    {
        return $this->request(self::CHANNELS_LIST_URL, $part, $params);
    }

    public function listPlaylistItems($part, $params = array())
    {
        return $this->request(self::PLAYLIST_ITEMS_URL, $part, $params);
    }

    protected function request($url, $part, $params = array())
    {
        $queryString = http_build_query(array('part' => $part) + $params
            + array('key' => $this->apiKey));
        $data = file_get_contents($url . '?' . $queryString);

        return json_decode($data, true);
    }
}
