<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\User;
use Auth;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationNotification extends Notification
{
    use Queueable;
    // public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($donate)
    {
        $this->donate = $donate;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {

        if($this->donate->email){
        return [
            'message' => $this->donate->email . ' Has Donated', 
            'path'=> url('admin\donation'),
        ];
            }

            if($this->donate->first_name){
                return [
            'message' => $this->donate->first_name . 'Has Donated', 
            'path'=> url('admin\donation'),
        ];  
            }
            else{
        return [
            'message' =>'Anonymouns Donation', 
            'path'=> url('admin\donation'),
        ];
            }
    
    }
}
