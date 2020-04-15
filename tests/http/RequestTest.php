<?php
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {

    protected $client;

    public function setUp(): void {
        $this->client = new SimplecURL\Client('https://httpbin.org');
    }

    public function testClient(): void {
        $this->assertInstanceOf(SimplecURL\Client::class, $this->client);
    }

    public function testGet(): void {
        $res = $this->client->request('GET', '/get');
        $this->assertInstanceOf(SimplecURL\Response::class, $res);
    }

    public function testPost(): void {
        $res = $this->client->request('POST', '/post', [
            'postfields' => [
                'Unit' => 'Test'
            ]
        ]);
        $this->assertInstanceOf(SimplecURL\Response::class, $res);
        
        $json = $res->json();
        $this->assertObjectHasAttribute('Unit', $json->form);
    }

    public function testHeaders(): void {
        $res = $this->client->request('GET', '/headers', [
            'headers' => [
                'X-Unit-Test: True'
            ]
        ])->json()->headers;

        $this->assertObjectHasAttribute('X-Unit-Test', $res);
    }

    public function testRedirectsAllowed(): void {
        try {
            $res = $this->client->request('GET', '/headers', [
                'redirects' => [
                    'allow' => true
                ]
            ]);

            $this->assertInstanceOf(SimplecURL\Response::class, $res);
        } catch(SimplecURL\HttpRequestException $e) {
            $this->fail('Redirects were allowed, but the client didn\'t follow the redirect.');
        }
    }

    public function testUseragent(): void {
        $this->client->setUseragent('UnitTestRequest/0.1.0');
        $res = $this->client->request('GET', '/user-agent')->json()->{'user-agent'};

        $this->assertEquals('UnitTestRequest/0.1.0', $res);
    }

}
