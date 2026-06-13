<?php

namespace App\Events;

use App\Models\Notice;
use App\Support\NoticeRealtimePayload;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NoticeBroadcasted implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public bool $afterCommit = true;

    public function __construct(
        public Notice $notice,
        public string $action = 'created',
    ) {
    }

    public function broadcastAs(): string
    {
        return 'notice.updated';
    }

    public function broadcastOn(): array
    {
        return collect(NoticeRealtimePayload::rolesFor($this->notice))
            ->map(function (string $role) {
                if ($role === 'user' && $this->notice->target_user_id) {
                    return new PrivateChannel("notices.user.{$this->notice->target_user_id}");
                }

                return new PrivateChannel("notices.{$role}");
            })
            ->all();
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'notice' => NoticeRealtimePayload::serialize($this->notice),
        ];
    }
}
