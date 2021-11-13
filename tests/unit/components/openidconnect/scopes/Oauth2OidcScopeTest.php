<?php

namespace Yii2Oauth2ServerTests\unit\components\openidconnect\scopes;

use rhertogh\Yii2Oauth2Server\components\openidconnect\claims\Oauth2OidcClaim;
use rhertogh\Yii2Oauth2Server\components\openidconnect\scopes\Oauth2OidcScope;
use rhertogh\Yii2Oauth2Server\interfaces\components\openidconnect\scope\Oauth2OidcClaimInterface;
use rhertogh\Yii2Oauth2Server\Oauth2Module;
use Yii2Oauth2ServerTests\unit\TestCase;

/**
 * @covers \rhertogh\Yii2Oauth2Server\components\openidconnect\scopes\Oauth2OidcScope
 */
class Oauth2OidcScopeTest extends TestCase
{
    public function testGetSetIdentifier()
    {
        $oidcScope = new Oauth2OidcScope();
        $identifier = 'test-claim';
        $this->assertEquals($oidcScope, $oidcScope->setIdentifier($identifier));
        $this->assertEquals($identifier, $oidcScope->getIdentifier());
    }

    public function testGetIdentifierWithoutItBeingSet()
    {
        $oidcScope = new Oauth2OidcScope();

        $this->expectExceptionMessage('Trying to get scope identifier without it being set.');
        $oidcScope->getIdentifier();
    }

    public function testGetSetAddClearClaims()
    {
        $this->mockConsoleApplication();

        $oidcScope = new Oauth2OidcScope();
        $testClaim = new Oauth2OidcClaim([
            'identifier' => 'test-claim-object',
        ]);
        $claims = [
            'ignored-claim-identifier' => $testClaim,
            new Oauth2OidcClaim([
                'identifier' => 'test-duplicate',
                'determiner' => 'test-duplicate-determiner',
            ]),
            new Oauth2OidcClaim([
                'identifier' => 'test-duplicate',
                'determiner' => 'test-duplicate-determiner-overwritten',
            ]),
            'test-claim-string-indexed',
            'test-claim-string-associative' => 'test-claim-string-associative-determiner',
            [
                'identifier' => 'test-claim-array-indexed',
                'determiner' => 'test-claim-array-indexed-determiner',
            ],
            'test-claim-array-associative' => [
                'determiner' => 'test-claim-array-associative-determiner',
            ],
            'test-claim-array-associative-ignored' => [
                'identifier' => 'test-claim-array-associative-identifier',
                'determiner' => 'test-claim-array-associative-identifier-determiner',
            ],
        ];

        // phpcs:disable Generic.Files.LineLength.TooLong -- readability acually better on single line

        // Dummy that should be cleared by `setClaims`
        $oidcScope->setClaims([new Oauth2OidcClaim([
            'identifier' => 'dummy',
        ])]);

        $this->assertTrue($oidcScope->hasClaim('dummy'));
        $this->assertEquals($oidcScope, $oidcScope->setClaims($claims));
        $this->assertFalse($oidcScope->hasClaim('dummy'));

        // Oauth2OidcClaim
        $this->assertNull($oidcScope->getClaim('ignored-claim-identifier'));
        $this->assertEquals($testClaim, $oidcScope->getClaim('test-claim-object'));
        // Duplicate identifier
        $this->assertEquals('test-duplicate-determiner-overwritten', $oidcScope->getClaim('test-duplicate')->getDeterminer());
        // Indexed string
        $testClaimIndexedString = $oidcScope->getClaim('test-claim-string-indexed');
        $this->assertInstanceOf(Oauth2OidcClaimInterface::class, $testClaimIndexedString);
        $this->assertEquals('test-claim-string-indexed', $testClaimIndexedString->getIdentifier());
        // Associative string
        $testClaimAssociativeString = $oidcScope->getClaim('test-claim-string-associative');
        $this->assertInstanceOf(Oauth2OidcClaimInterface::class, $testClaimAssociativeString);
        $this->assertEquals('test-claim-string-associative', $testClaimAssociativeString->getIdentifier());
        $this->assertEquals('test-claim-string-associative-determiner', $testClaimAssociativeString->getDeterminer());
        // Indexed array
        $testClaimIndexedArray = $oidcScope->getClaim('test-claim-array-indexed');
        $this->assertInstanceOf(Oauth2OidcClaimInterface::class, $testClaimIndexedArray);
        $this->assertEquals('test-claim-array-indexed', $testClaimIndexedArray->getIdentifier());
        $this->assertEquals('test-claim-array-indexed-determiner', $testClaimIndexedArray->getDeterminer());
        // Associative array
        $testClaimAssociativeArray = $oidcScope->getClaim('test-claim-array-associative');
        $this->assertInstanceOf(Oauth2OidcClaimInterface::class, $testClaimAssociativeArray);
        $this->assertEquals('test-claim-array-associative', $testClaimAssociativeArray->getIdentifier());
        $this->assertEquals('test-claim-array-associative-determiner', $testClaimAssociativeArray->getDeterminer());
        // Associative array with its own identifier
        $testClaimAssociativeArrayIdentifier = $oidcScope->getClaim('test-claim-array-associative-identifier');
        $this->assertNull($oidcScope->getClaim('test-claim-array-associative-ignored'));
        $this->assertInstanceOf(Oauth2OidcClaimInterface::class, $testClaimAssociativeArrayIdentifier);
        $this->assertEquals('test-claim-array-associative-identifier', $testClaimAssociativeArrayIdentifier->getIdentifier());
        $this->assertEquals('test-claim-array-associative-identifier-determiner', $testClaimAssociativeArrayIdentifier->getDeterminer());

        // Add extra claims
        $extraClaims = [
            new Oauth2OidcClaim([
                'identifier' => 'test-duplicate',
                'determiner' => 'test-duplicate-determiner-overwritten-2',
            ]),
            new Oauth2OidcClaim([
                'identifier' => 'test-claim-extra',
            ]),
        ];
        $this->assertEquals($oidcScope, $oidcScope->addClaims($extraClaims));
        $this->assertEquals('test-duplicate-determiner-overwritten-2', $oidcScope->getClaim('test-duplicate')->getDeterminer());
        $this->assertEquals($testClaim, $oidcScope->getClaim('test-claim-object'));
        $this->assertTrue($oidcScope->hasClaim('test-claim-extra'));

        // Clear Claims
        $this->assertEquals($oidcScope, $oidcScope->clearClaims());
        $this->assertEquals([], $oidcScope->getClaims());

        // phpcs:enable Generic.Files.LineLength.TooLong
    }

    public function testAddClaimsInvalidArrayConfig()
    {
        $oidcScope = new Oauth2OidcScope();

        $this->expectExceptionMessage(
            'Elements must either be an array, string or a ' . Oauth2OidcClaimInterface::class
        );
        $oidcScope->addClaims([
            new \stdClass(),
        ]);
    }

    public function testAddClaimsInvalidClaimArrayConfig()
    {
        $oidcScope = new Oauth2OidcScope();

        $this->expectExceptionMessage(
            'If an element is an array it should either be declared as an associative element'
            . ' or contain an "identifier" key.'
        );
        $oidcScope->addClaims([
            [
                'determiner' => 'test-claim-array-indexed-determiner',
            ],
        ]);
    }

    public function testGetAddRemoveHasClaim()
    {
        $this->mockConsoleApplication();

        $oidcScope = new Oauth2OidcScope();

        $claim1 = new Oauth2OidcClaim([
            'identifier' => 'test-claim1',
            'determiner' => 'test-determiner1',
        ]);
        $claim2 = new Oauth2OidcClaim([
            'identifier' => 'test-claim2',
            'determiner' => 'test-determiner2',
        ]);

        $this->assertEquals($oidcScope, $oidcScope->addClaim('test-claim-string'));
        $this->assertEquals($oidcScope, $oidcScope->addClaim([
            'identifier' => 'test-claim-array',
            'determiner' => 'test-claim-array-determiner',
        ]));
        $this->assertEquals($oidcScope, $oidcScope->addClaim($claim1));
        $this->assertEquals($oidcScope, $oidcScope->addClaim($claim2));

        $this->assertTrue($oidcScope->hasClaim('test-claim-string'));
        $this->assertTrue($oidcScope->hasClaim('test-claim-array'));
        $this->assertTrue($oidcScope->hasClaim('test-claim1'));
        $this->assertTrue($oidcScope->hasClaim('test-claim2'));
        $this->assertFalse($oidcScope->hasClaim('test-claim3'));

        $this->assertEquals($claim1, $oidcScope->getClaim('test-claim1'));
        $this->assertNull($oidcScope->getClaim('test-claim3'));

        $this->assertEquals($oidcScope, $oidcScope->removeClaim('test-claim2'));
        $this->assertTrue($oidcScope->hasClaim('test-claim1'));
        $this->assertFalse($oidcScope->hasClaim('test-claim2'));
    }
}
