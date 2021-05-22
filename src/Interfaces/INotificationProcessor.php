<?php

/** @noinspection UnknownInspectionInspection */
/** @noinspection PhpUnused */

namespace Dotlines\GhooriSubscription\Interfaces;

use Dotlines\GhooriSubscription\Models\Notification;
use Dotlines\GhooriSubscription\Models\NotificationResponse;

/**
 * After Ghoori pushes a notification to your Notification Receiving endpoint
 * Please prepare a notification object from Notification class
 * and pass it to your NotificationProcessor (extends this interface)
 *
 * Interface INotificationProcessor
 * @package Dotlines\GhooriSubscription\Interfaces
 */
interface INotificationProcessor
{
    public function process(Notification $notification, array $others = []): NotificationResponse;
}
