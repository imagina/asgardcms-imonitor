<?php

Broadcast::channel('register-{id}', function ($user) {
    return $user;
});