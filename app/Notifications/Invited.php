<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Invited extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    private $key;

    /**
     * Create new notification instance
     *
     * @return void
     */
    public function __construct($invite)
    {
        $this->invite = $invite;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return MailMessage
     */
    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->from('no-reply@accounts.nonverse.net', 'Nonverse Network')
            ->subject("You've been invited")
            ->markdown('mail.invited', [
                'key' => $this->invite['key'],
                'email' => $this->invite['email']
            ]);
    }
}
