<?php

namespace Tests\Feature;

use App\Login;
use Tests\TestCase;

class LoginFeatureTest extends TestCase
{
    protected $endpoint = "/api/logins";


    /** @test */
    public function a_user_can_create_a_Login_record()
    {
        /* Create a new user */
//        $userFeatureTest = new UserFeatureTest();
//        $user = $userFeatureTest->testCreateAUser($userFeatureTest->testMakeAUserObject());
//
//        $obj = $this->testMakeALoginObject();
//        $obj['user_id'] = $user['id'];
//
//        $this->json('POST', $this->endpoint, $obj, $this->header)
//            ->assertStatus(201)
//            ->assertJsonStructure(
//                $this->testAssertDataObject()
//            );

    }

    /** @test */
    public function a_user_can_update_a_Login_record()
    {
        $obj = $this->testCreateALogin($this->testMakeALoginObject());
        $this->json('POST', $this->endpoint, $obj, $this->header)
            ->assertStatus(200)
            ->assertJsonStructure(
                $this->testAssertDataObject()
            );
    }

    /** @test */
    public function a_user_can_delete_a_Login_record()
    {
        $obj = $this->testCreateALogin($this->testMakeALoginObject());
        $this->json('DELETE', $this->endpoint . '/' . $obj['id'], [], $this->header)
            ->assertStatus(204);
    }

    /** @test */
    public function a_user_can_view_Login_records()
    {
        $assertData = $this->testAssertDataObject();
        $assertData['data'] = [$assertData['data']];

        $this->json('POST', $this->endpoint. '/fs', [], $this->header)
            ->assertStatus(200)
            ->assertJsonStructure(
                $assertData
            );
    }

    /** @test */
    public function a_user_cannot_create_a_duplicate_Login_record()
    {
        $obj = $this->testMakeALoginObject();

        $this->json('POST', $this->endpoint, $obj, $this->header)
            ->assertStatus(201);

        $this->json('POST', $this->endpoint, $obj, $this->header)
            ->assertStatus(400);
    }

    /** @test */
    public function a_user_can_filter_records_of_Login()
    {
        $filter = $this->testMakeFilterObject();
        $this->json('POST', $this->endpoint . '/fs', $filter, $this->header)
            ->assertStatus(200)
            ->assertJson(["data" => [["username" => "30355511"]]]
            );
    }

    /** @test */
    public function a_user_can_login()
    {
        $obj = ['username' => '30355511', 'password' => '123456'];
        $this->json('POST', '/api/login', $obj, $this->headerLogin)
            ->assertStatus(200)
            ->assertJsonStructure(
                ['access_token','token_type','expires_in', 'user' => ['id', 'username']]
            );
    }

    /** @test */
    public function a_user_cannot_login()
    {
        $obj = ['username' => '30355511', 'password' => '1234567'];
        $this->json('POST', '/api/login', $obj, $this->headerLogin)
            ->assertStatus(401);
    }

    /** @test */
    public function a_user_cannot_login_if_username_is_not_filled()
    {
        $obj = ['password' => '1234567'];
        $this->json('POST', '/api/login', $obj, $this->headerLogin)
            ->assertStatus(402);
    }

    /** @test */
    public function a_user_cannot_login_if_password_is_not_filled()
    {
        $obj = ['username' => '30355511'];
        $this->json('POST', '/api/login', $obj, $this->headerLogin)
            ->assertStatus(402);
    }

    public function testAssertDataObject()
    {
        return ['data' => ['id', 'username', 'user_id']];
    }

    public function testMakeALoginObject()
    {
        return ['username' => '99999999', 'password' => bcrypt('123456'), 'user_id' => 3];
    }

    /**
     * @group ignore
     */
    public function testCreateALogin($obj)
    {
        return Login::create($obj)->toArray();
    }

    public function testMakeFilterObject()
    {
        return [
            "filters" => [
                ['filter_column' => 'username', 'filter_value' => "30355511"],
            ]
        ];
    }

}
