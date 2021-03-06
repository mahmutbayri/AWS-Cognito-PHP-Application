<?php

use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;

include __DIR__ . '/../bootstrap.php';

$localTemplateVariableMap = [
    //
];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $result = auth($_POST['UserName'], $_POST['password']);
        $token = $result->get('AuthenticationResult')['AccessToken'];
        setAuthCookie($token);

        if($result->get('ChallengeName') === 'NEW_PASSWORD_REQUIRED') {
            header('Location: change-password.php');
            exit();
        }
        header('Location: index.php');
        exit();
    } catch (CognitoIdentityProviderException $e) {
        $localTemplateVariableMap = [
            '<!--__ERROR__-->' => $e->getAwsErrorMessage(),
        ];
    }
}


$content = file_get_contents(__DIR__ . '/../page-templates/sign-in.html');

$templateVariableMap = array_merge(
    $localTemplateVariableMap,
    $templateVariableMap ?? []
);

echo str_replace(
    array_keys($templateVariableMap),
    array_values($templateVariableMap),
    $content
);
