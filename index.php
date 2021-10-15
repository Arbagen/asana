<?php

require './vendor/autoload.php';

$clientId = getenv('CLIENT_ID');

$clientSecret = getenv('CLIENT_SECRET');

$redirectUrl = getenv('REDIRECT_URL');

function generateGrantAccessButton(string $clientId, string $redirectUrl) {
    //generate code
    $button = "<a href=\"https://app.asana.com/-/oauth_authorize
?client_id={$clientId}
&redirect_uri={$redirectUrl}
&response_type=code
&scope=default\">Authenticate with Asana</a>";

    echo $button;
}

function getAccessToken(string $clientId, string $clientSecret, string $redirectUrl, string $code) {
    $client = new GuzzleHttp\Client();

    try {
        $endpoint = 'https://app.asana.com/-/oauth_token';
        $tokenParameters = [
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUrl,
            'code' => $code,
        ];

        $response = $client->post($endpoint, ['form_params' => $tokenParameters]);
        $content = $response->getBody()->getContents();
        dd($content);
    } catch (\Exception $clientException) {
        dd($clientException->getMessage());
    }

}

function createTask(string $candidateName = 'Dante', string $accessToken) {

    $client = new GuzzleHttp\Client();

    try {
        $taskMessage = "Please, review {$candidateName}'s integration";
        $taskEndpoint = 'https://app.asana.com/api/1.0/tasks';


        $taskData = [
            'data' => [
                'name' => $taskMessage,
                'projects' => ['1201134145523440']
            ],
        ];

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$accessToken}",
            ],
            'body' => json_encode($taskData)
        ];

        $response = $client->post($taskEndpoint, $options);
        $content = $response->getBody()->getContents();
        dd($content);
    }catch (\Exception $exception) {
        #FIXME
        dd($exception->getMessage());
    }
}

function attachToTask(string $accessToken, string $taskId, string $filePath = './whiteboard.txt') {
    $client = new GuzzleHttp\Client();

    try {
        $attachEndpoint = "https://app.asana.com/api/1.0/tasks/{$taskId}/attachments";

        $fileContent = file_get_contents($filePath);

        $attachmentData = [
            'data' => [
                'file' => base64_encode($fileContent),
                'name' => 'dante\'s attachment',
                'projects' => ['1201134145523440']
            ]
        ];

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$accessToken}",
            ],
            'body' => json_encode($attachmentData)
        ];

        $response = $client->post($attachEndpoint, $options);
        $content = $response->getBody()->getContents();
        dd($content);
    } catch (\Exception $exception) {
        #FIXME
        dd($exception->getMessage());
    }
}