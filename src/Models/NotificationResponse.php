<?php

/** @noinspection UnknownInspectionInspection */
/** @noinspection PhpUnused */

namespace Dotlines\GhooriSubscription\Models;

/**
 * After Ghoori pushes a notification to your Notification Receiving endpoint
 * And after you've done necessary processing for the notification
 * Please prepare an object of this class
 * And send it as a valid response
 *
 * Class NotificationResponse
 * @package Dotlines\GhooriSubscription\Models
 */
class NotificationResponse
{
    public string $recordID;
    public string $status;
    public string $timestamp;

    public function __construct(string $recordID, string $status, string $timestamp)
    {
        $this->recordID = $recordID;
        $this->status = $status;
        $this->timestamp = $timestamp;
    }
}
