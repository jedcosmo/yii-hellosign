<?php

/**
 * The MIT License (MIT)
 *
 * Copyright (C) 2014 hellosign.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace HelloSign\Test;

use HelloSign\Account;
use HelloSign\Error;
use HelloSign\Client;
use HelloSign\SignatureRequest;
use HelloSign\Signer;
use HelloSign\TemplateSignatureRequest;

class OAuthTest extends AbstractTest
{
    private function getOAuthClient($token) {
        $api_url = $_ENV['API_URL'] == null ? Client::API_URL : $_ENV['API_URL'];
        $oauth_token_url = $_ENV['OAUTH_TOKEN_URL'] == null ? Client::OAUTH_TOKEN_URL : $_ENV['OAUTH_TOKEN_URL'];
        $oauth_client = new Client($token, null, $api_url, $oauth_token_url);
        // $oauth_client->enableDebugMode();

        if ($api_url != Client::API_URL) {
            $oauth_client->disableCertificateCheck();
        }

        return $oauth_client;
    }

    /**
     * @group create
     */
    public function testCreateAccount()
    {
        $response = $this->client->createAccount(
            new Account($this->team_member_1),
            $_ENV['CLIENT_ID'],
            $_ENV['CLIENT_SECRET']
        );

        $this->assertInstanceOf('HelloSign\Account', $response);
        $this->assertInstanceOf('HelloSign\OAuthToken', $response->getOAuthData());

        return $response->getOAuthData();
    }

    /**
     * @depends testCreateAccount
     * @group oauth
     */
    public function testRefreshToken($token)
    {
        $oauth_client = $this->getOAuthClient($token);
        $response = $oauth_client->refreshOAuthToken($token);
        $this->assertInstanceOf('HelloSign\OAuthToken', $response);

        return $response;
    }

    /**
     * @depends testRefreshToken
     * @group oauth
     */
    public function testSendSignatureRequest($token)
    {

        // Commenting out the next two tests because these
        // will not work as long as we cannot confirm a user
        // email address via the API.

        // $oauth_client = $this->getOAuthClient($token);
        // $request = new SignatureRequest;
        // $request->enableTestMode();

        // // Set Request Param Signature Request
        // $request->setTitle("NDA with Acme Co.");
        // $request->setSubject("The NDA we talked about");
        // $request->setMessage("Please sign this NDA and then we can discuss more. Let me know if you have any questions.");
        // $request->addSigner("jack@example.com", "Jack", 0);
        // $request->addSigner(new Signer(array(
        //     'name'          => "Jill",
        //     'email_address' => "jill@example.com",
        // 	'order'			=> 1
        // )));
        // $request->addCC("lawyer@example.com");
        // $request->addFile(__DIR__ . '/nda.docx');

        // // Send Signature Request
        // $response = $oauth_client->sendSignatureRequest($request);

        // $this->assertInstanceOf('HelloSign\SignatureRequest', $response);
        // $this->assertNotNull($response->getId());
        // $this->assertEquals($request, $response);
        // $this->assertEquals($response->getTitle(), $response->title);

        return $token;
    }

    /**
     * @depends testSendSignatureRequest
     * @group oauth
     */
    public function testGetSignatureRequestList($token)
    {
        // $oauth_client = $this->getOAuthClient($token);
        // $signature_requests = $oauth_client->getSignatureRequests();
        // $signature_request = $signature_requests[0];

        // $signature_request2 = $oauth_client->getSignatureRequest($signature_request->getId());


        // $this->assertInstanceOf('HelloSign\SignatureRequestList', $signature_requests);
        // $this->assertGreaterThan(0, count($signature_requests));

        // $this->assertInstanceOf('HelloSign\SignatureRequest', $signature_request);
        // $this->assertNotNull($signature_request->getId());

        // $this->assertInstanceOf('HelloSign\SignatureRequest', $signature_request2);
        // $this->assertNotNull($signature_request2->getId());

        // $this->assertEquals($signature_request, $signature_request2);

        return $token;
    }
}
