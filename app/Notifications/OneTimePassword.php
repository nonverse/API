<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OneTimePassword extends Notification
{
    use Queueable;

    /**
     * @var array
     */
    private $otp;

    /**
     * @var User
     */
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, array $otp)
    {
        $this->otp = $otp;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
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
            ->from('no-reply@api.nonverse.net', 'Nonverse API')
            ->subject('Account OTP')
            ->markdown('mail.user.one-time-password', [
                'name' => $this->user->name_first,
                'otp' => $this->otp['value'],
                'time' => $this->otp['request_time'],
                'ip' => $this->otp['request_ip']
            ]);
    }
}
