<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApiKeyCreated extends Notification
{
    use Queueable;

    /**
     * @var array
     */
    private $token;

    /**
     * @var User
     */
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, array $key)
    {
        $this->token = $key;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
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
            ->subject('API Key created')
            ->markdown('mail.api.key-created', [
                'name' => $this->user->name_first,
                'key_name' => $this->token['key_name'],
                'permissions' => $this->token['permission_count'],
                'token' => $this->token['token_value'],
                'id' => $this->token['token_id']
            ]);
    }
}
