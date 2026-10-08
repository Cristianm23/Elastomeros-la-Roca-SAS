<?php

namespace App\Modules\Notifications;

final class NotificationChannel
{
    public const EMAIL = 'mail';
    public const WEB = 'database';
    public const PUSH = 'push';

    private function __construct() {}
}
