<?php

test('health check retorna ok', function () {
    $this->getJson('/api/v1/health')->assertOk()->assertJson(['status' => 'ok']);
});
