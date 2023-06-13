<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorEnabled extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    private string $token;

    /**
     * @var User
     */
    private User $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, $token)
    {
        $this->token = $token;
        $this->user = $user;
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
            ->from('no-reply@auth.nonverse.net', 'Nonverse SecureAuth')
            ->subject('2FA Enabled')
            ->markdown('mail.two-factor-enabled', [
                'name' => $this->user->name_first,
                'token' => $this->token
            ]);
    }
}
