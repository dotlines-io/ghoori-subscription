<?php

/** @noinspection UnknownInspectionInspection */
/** @noinspection PhpUnused */

namespace Dotlines\GhooriSubscription\Models;

use JsonException;

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

    /**
     * @throws JsonException
     * @noinspection PhpUndefinedConstantInspection
     */
    public function __toString(): string
    {
        return json_encode([
            'recordID' => $this->recordID,
            'status' => $this->status,
            'timestamp' => $this->timestamp,
        ], JSON_THROW_ON_ERROR);
    }
}
