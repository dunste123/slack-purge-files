<?php
set_time_limit(0); // Make sure that the terminal doesn't time out

use GuzzleHttp\Client;

require __DIR__ . '/vendor/autoload.php';

$base = 'https://slack.com/api';
$config = json_decode(file_get_contents(__DIR__ . '/config.json'));
$token = $config->token;

echo "Fetching all files\n";

$client = new Client();

$listFilesBase = "$base/files.list?token=$token";
$total = getJson($client->get($listFilesBase))->paging->total;

echo "Preparing to delete $total files\n";

// Cheat and list all the files so that we can loop over them
$allFilesJson = getJson($client->get("$listFilesBase&count=$total"));
$files = $allFilesJson->files;

echo "Starting the purge\n";

foreach ($files as $file) {

    echo "Deleting file '$file->title' by user '<@$file->user>'\n";
    $query = http_build_query([
        'token' => $token,
        'file' => $file->id,
    ]);

    $res = $client->post("$base/files.delete?$query");

    $data = getJson($res);

    if ($data->ok) {
        echo "Deleted file '$file->title' by user '<@$file->user>'\n";
    } else {
        $error = json_encode($data->error);
        echo "Failed to delete file '$file->title' by user '<@$file->user>': $error\n";
    }

    // Sleep for 2 seconds because of the rate limit
    sleep(2);
}

