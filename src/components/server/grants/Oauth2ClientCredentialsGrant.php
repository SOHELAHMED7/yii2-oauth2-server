<?php

namespace rhertogh\Yii2Oauth2Server\components\server\grants;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use rhertogh\Yii2Oauth2Server\interfaces\components\authorization\Oauth2ClientAuthorizationRequestInterface;
use rhertogh\Yii2Oauth2Server\interfaces\components\authorization\Oauth2ScopeAuthorizationRequestInterface;
use rhertogh\Yii2Oauth2Server\interfaces\components\server\grants\Oauth2ClientCredentialsGrantInterface;
use rhertogh\Yii2Oauth2Server\interfaces\models\Oauth2ClientInterface;
use rhertogh\Yii2Oauth2Server\Oauth2Module;
use Yii;
use yii\web\ServerErrorHttpException;

class Oauth2ClientCredentialsGrant extends ClientCredentialsGrant implements Oauth2ClientCredentialsGrantInterface
{
    /** @var Oauth2Module */
    public $module;

    /**
     * @inheritDoc
     * @param Oauth2ClientInterface $client
     */
    protected function issueAccessToken(
        \DateInterval $accessTokenTTL,
        ClientEntityInterface $client,
        $userIdentifier,
        array $scopes = []
    ) {
        if ($userIdentifier === null) {
            $clientCredentialsGrantUserId = $client->getClientCredentialsGrantUserId();
            if ($clientCredentialsGrantUserId) {
                $clientCredentialsGrantUser = $this->module->getUserRepository()
                    ->getUserEntityByIdentifier($clientCredentialsGrantUserId);
                $scopeIdentifiers = array_map(fn($scope) => $scope->getIdentifier(), $scopes);

                /** @var Oauth2ClientAuthorizationRequestInterface $clientAuthorizationRequest */
                $clientAuthorizationRequest = Yii::createObject([
                    'class' => Oauth2ClientAuthorizationRequestInterface::class,
                    'module' => $this->module,
                    'client' => $client,
                    'userIdentity' => $clientCredentialsGrantUser,
                    'requestedScopeIdentifiers' => $scopeIdentifiers,
                    'grantType' => $this->getIdentifier(),
                ]);

                if ($clientAuthorizationRequest->isClientAuthorizationNeeded()) {
                    throw new ServerErrorHttpException('User id "' . $clientCredentialsGrantUserId
                        . '" is set as default "client credentials grant user" for client "' . $client->getIdentifier()
                        . '" but the client is not authorized for this user.');
                }

                if ($clientAuthorizationRequest->isScopeAuthorizationNeeded()) {
                    $unapprovedScopes = array_map(
                        fn($scopeApprovalRequest) => $scopeApprovalRequest->getScope()->getIdentifier(),
                        $clientAuthorizationRequest->getApprovalPendingScopes()
                    );
                    throw new ServerErrorHttpException('User id "' . $clientCredentialsGrantUserId
                        . '" is set as default "client credentials grant user" for client "' . $client->getIdentifier()
                        . '" but the following scopes are not approved: ' . implode(', ', $unapprovedScopes));
                }

                $userIdentifier = $clientCredentialsGrantUser->getIdentifier();
                $previouslyApprovedScopes = $clientAuthorizationRequest->getPreviouslyApprovedScopes();
                $scopes = array_map(
                    fn(Oauth2ScopeAuthorizationRequestInterface $request) => $request->getScope(),
                    array_intersect_key($previouslyApprovedScopes, array_flip($scopeIdentifiers))
                );
                $scopes = array_merge($scopes, $clientAuthorizationRequest->getScopesAppliedByDefaultAutomatically());
                $scopes = array_values($scopes);
            }
        }


        return parent::issueAccessToken($accessTokenTTL, $client, $userIdentifier, $scopes);
    }
}
