<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterSchedule extends Model
{
    protected $table = 'newsletter_schedule';
    protected $fillable = ['day_of_week', 'send_time',  'is_active'];
}
