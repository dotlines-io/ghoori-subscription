<?php


namespace Dotlines\GhooriSubscription\Models;


/**
 * You will provide an API endpoint to Ghoori
 * After that Ghoori will start pushing notifications to this API endpoint
 *
 * How to use:
 * After Ghoori pushes a notification to your Notification Receiving endpoint
 * Please prepare a notification object from this class
 * and pass it to your NotificationProcessor
 *
 * Class Notification
 * @package Dotlines\GhooriSubscription\Models
 */
class Notification
{
    public string $notificationID;
    public string $invoiceID;
    public string $type;
    public string $status;
    public string $subscriptionID;
    public string $paymentID; // comes empty only when $type = 'SUBSCRIPTION'
    public string $timestamp;

    public function __construct(string $notificationID, string $invoiceID, string $type, string $status, string $subscriptionID, string $paymentID, string $timestamp)
    {
        $this->notificationID = $notificationID;
        $this->invoiceID = $invoiceID;
        $this->type = $type;
        $this->status = $status;
        $this->subscriptionID = $subscriptionID;
        $this->paymentID = $paymentID;
        $this->timestamp = $timestamp;
    }
}
