<?php

test('guest is redirected to login from home page', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});