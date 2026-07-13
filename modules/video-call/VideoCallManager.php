<?php
class VideoCallManager {
    public function __construct() {}

    public function createRoom($topic) {
        return ['roomId' => uniqid('room_'), 'topic' => $topic];
    }
}
