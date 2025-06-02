<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskVersion;
use Illuminate\View\View;

class TaskVersionWebController extends Controller
{
    /**
     * Summary of show
     * @param \App\Models\Task $task
     * @param \App\Models\TaskVersion $version
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Task $task, TaskVersion $version): View
    {
        abort_unless(
            $version->task_id === $task->id && $task->user_id === auth()->id(),
            404
        );

        return view('tasks.version-show', compact('task', 'version'));
    }
}

