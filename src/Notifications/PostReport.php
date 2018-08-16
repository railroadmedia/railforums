<?php

namespace Railroad\Railforums\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Railroad\Railforums\Services\ConfigService;

class PostReport extends Notification
{
    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * The post that is reported
     *
     * @var array
     */
    public $post;

    /**
     * Create a notification instance.
     *
     * @param  array $post
     * @return void
     */
    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [ConfigService::$postReportNotificationChannel];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->line('The following post has been reported: ')
            ->line($this->post['content'])
            ->action(
                'View Post',
                url(
                    config('app.url') .
                    route(
                        ConfigService::$postReportNotificationViewPostRoute,
                        $this->post['id'],
                        false
                    )
                )
            );
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
