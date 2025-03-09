<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginEmailNotification extends Notification
{
	use Queueable;

	/**
	 * The details of the login attempt.
	 *
	 * @var array
	 */
	protected $details;

	/**
	 * Create a new notification instance.
	 */
	public function __construct($details)
	{
		$this->details = $details;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @return array<int, string>
	 */
	public function via(object $notifiable): array
	{
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 */
	public function toMail(object $notifiable): MailMessage
	{
		return (new MailMessage)
			->subject('New Login Activity Detected')
			->line('A new login attempt has been detected on your account.')
			->line('Here are the details:')
			->line('IP Address: ' . $this->details['ip'])
			->line('Location: ' . $this->details['location'])
			->line('Browser: ' . $this->details['browser'])
			->line('Date & Time: ' . $this->details['date_time'])
			->line('If this was not you, please secure your account immediately.')
			->action('Secure Your Account', url('/'));
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(object $notifiable): array
	{
		return [
			//
		];
	}
}
