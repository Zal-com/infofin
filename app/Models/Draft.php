<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Draft extends Model
{

    protected $fillable = ['content'];
    public $timestamps = true;

    protected $casts = [
        'content' => 'array',
    ];


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_drafts');
    }

    public function getFirstDeadlineAttribute(): string
    {
        $content = $this->attributes['content'] ? json_decode($this->attributes['content'], true) : null;

        if ($content && !empty($content['deadlines'])) {
            $deadlines = array_values($content['deadlines']);
            usort($deadlines, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            $futureDeadlines = array_filter($deadlines, function ($deadline) {
                return Carbon::parse($deadline['date'])->isAfter(today());
            });

            if (!empty($futureDeadlines)) {
                $firstFutureDeadline = reset($futureDeadlines);

                if ($firstFutureDeadline['continuous'] == 1) {
                    return 'Continu';
                } else {
                    return Carbon::parse($firstFutureDeadline['date'])->format('d/m/Y') . '|' . $firstFutureDeadline['proof'];
                }
            } else {
                $lastDeadline = end($deadlines);
                if ($lastDeadline['continuous'] == 1) {
                    return 'Continu';
                } else {
                    return Carbon::parse($lastDeadline['date'])->format('d/m/Y');
                }
            }
        } else {
            return "No deadline";
        }
    }
}
