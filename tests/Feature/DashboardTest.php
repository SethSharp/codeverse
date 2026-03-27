<?php

test('dashboard redirects to home', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect('/');
});
