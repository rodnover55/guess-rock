<?php
namespace App\Tests;

/**
 * @author Sergei Melnikov <me@rnr.name>
 */
class AuthControllerTest extends TestCase
{
    public function testRoutes() {
        $this->get('/qqq');

        $this->assertResponseOk();
    }
}