<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class IndexProjects extends Component
{
    use WithPagination;

    public $orderByColumn = 'TimeStamp';
    public $orderDirection = 'desc';
    public $itemsPerPage = 20;
    public $arrayCheck = ["Name", "Deadline", "Deadline2", "Organisation", "ShortDescription", "TimeStamp"];

    protected $updatesQueryString = ['order', 'field'];

    public function mount()
    {
        if (request()->has('order')) {
            if (in_array(request('order'), ['asc', 'desc'])) {
                $this->orderDirection = request('order');
            } else {
                return redirect("/projects");
            }
        }

        if (request()->has('field')) {
            if (in_array(request('field'), $this->arrayCheck)) {
                $this->orderByColumn = request('field');
            } else {
                return redirect("/projects");
            }
        }
    }

    public function render()
    {
        $projects = Project::getSortedAndPaginatedProjects($this->orderByColumn, $this->orderDirection, $this->itemsPerPage, $this->arrayCheck);
        return view('livewire.index-projects', ['projects' => $projects]);
    }
}

