<?php

Broadcast::channel('record-{id}', function ($user) {
    return $user;
});