<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\Test\Constraint;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseStatusCodeSame;

class ResponseStatusCodeSameTest extends TestCase
{
    public function testConstraint()
    {
        $constraint = new ResponseStatusCodeSame(200);
        $this->assertTrue($constraint->evaluate(new Response(), '', true));
        $this->assertFalse($constraint->evaluate(new Response('', 404), '', true));
        $constraint = new ResponseStatusCodeSame(404);
        $this->assertTrue($constraint->evaluate(new Response('', 404), '', true));

        $constraint = new ResponseStatusCodeSame(200);

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageMatches('/Failed asserting that the Response status code is 200.\nHTTP\/1.0 404 Not Found.+Response body/s');

        $constraint->evaluate(new Response('Response body', 404));
    }

    public function testReducedVerbosity()
    {
        $constraint = new ResponseStatusCodeSame(200, verbose: false);

        try {
            $constraint->evaluate(new Response('Response body', 404));
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString("Failed asserting that the Response status code is 200.\nHTTP/1.0 404 Not Found", $e->getMessage());
            $this->assertStringNotContainsString('Response body', $e->getMessage());

            return;
        }

        $this->fail();
    }
}
