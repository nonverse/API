<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmail extends Notification
{
    use Queueable;

    /**
     * @var User
     */
    private User $user;

    /**
     * @var string
     */
    private string $jwt;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, string $jwt)
    {
        $this->user = $user;
        $this->jwt = $jwt;
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
            ->from('no-reply@account.nonverse.net', 'Nonverse Account')
            ->subject('Verify your e-mail address')
            ->markdown('mail.verify-email', [
                'name' => $this->user->name_first,
                'url' => env('ACCOUNT_APP_URL') . '/verify?token=' . $this->jwt
            ]);
    }
}
