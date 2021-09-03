<?php

namespace App\Jobs;

use App\Events\UserStartedSponsoring;
use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SynchronizeSponsorStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->user->isGithubSponsor()) {
            $sponsor = Sponsor::create(['github_api_id' => $this->user->github_api_id]);
            $this->user->sponsor_id = $sponsor->id;
            $this->user->save();

            UserStartedSponsoring::dispatch($this->user);
        }
    }
}
