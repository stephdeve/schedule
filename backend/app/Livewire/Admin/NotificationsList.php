<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;

class NotificationsList extends Component
{
    use WithPagination;

    public bool $onlyUnread = false;
    public int $perPage = 10;

    public function toggleUnread(): void
    {
        $this->onlyUnread = ! $this->onlyUnread;
        $this->resetPage();
    }

    public function markAsRead(int $id): void
    {
        $n = Notification::findOrFail($id);
        $n->update(['lu' => true]);
    }

    public function getRowsProperty()
    {
        return Notification::query()
            ->when($this->onlyUnread, fn (Builder $q) => $q->where('lu', false))
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.notifications-list', [
            'notifications' => $this->rows,
        ]);
    }
}
