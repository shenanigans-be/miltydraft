<?php

namespace App\Http;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class HttpRequestTest extends TestCase
{
    public static function requestParameters(): iterable
    {
        yield "When set in get" => [
            "param" => "key",
            "get" => [
                "key" => "value"
            ],
            "post" => [],
            "expectedValue" => "value"
        ];
        yield "When set in post" => [
            "param" => "key",
            "post" => [
                "key" => "value"
            ],
            "get" => [],
            "expectedValue" => "value"
        ];
        yield "When not set anywhere" => [
            "param" => "key",
            "post" => [],
            "get" => [],
            "expectedValue" => null
        ];
    }

    #[DataProvider("requestParameters")]
    #[Test]
    public function itCanRetrieveParameters(string $param, array $get, array $post, $expectedValue)
    {
        $request = new HttpRequest($get, $post);
        $this->assertSame($expectedValue, $request->get($param));
    }

    #[Test]
    public function itCanReturnDefaultValueForParameter()
    {
        $request = new HttpRequest([], []);
        $this->assertSame("bar", $request->get("foo", "bar"));
    }

    #[Test]
    public function itCanBeInitialisedFromGetRequest()
    {
       $_GET["foo"] = "bar";
       $request = HttpRequest::fromRequest();
       $this->assertSame("bar", $request->get("foo"));
    }

    #[Test]
    public function itCanBeInitialisedFromPostRequest()
    {
        $_POST["foo"] = "bar";
        $request = HttpRequest::fromRequest();
        $this->assertSame("bar", $request->get("foo"));
    }
}