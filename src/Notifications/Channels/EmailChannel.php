<?php

namespace Railroad\Railforums\Notifications\Channels;

use Illuminate\Mail\Mailer;
use Railroad\Railnotifications\Channels\ChannelInterface;
use Railroad\Railnotifications\Entities\NotificationBroadcast;
use Railroad\Railnotifications\Services\NotificationBroadcastService;

class EmailChannel implements ChannelInterface
{
    private $notificationBroadcastService;
    private $mailer;

    public function __construct(NotificationBroadcastService $notificationBroadcastService, Mailer $mailer)
    {
        $this->notificationBroadcastService = $notificationBroadcastService;
        $this->mailer = $mailer;
    }

    public function send(NotificationBroadcast $notificationBroadcast)
    {
        // Ex. send email using notification broadcast

        $this->notificationBroadcastService->markSucceeded($notificationBroadcast->getId());
    }

    public function sendAggregated(array $notificationBroadcasts)
    {
        // Ex. send email using notification broadcasts

        foreach ($notificationBroadcasts as $notificationBroadcast) {
            $this->notificationBroadcastService->markSucceeded($notificationBroadcast->getId());
        }
    }
}