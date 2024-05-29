<?php
namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class Projects extends Component
{
    use WithPagination;

    public $orderByColumn = 'TimeStamp';
    public $orderDirection = 'desc';
    public $itemsPerPage = 20;
    public $arrayCheck = ["Name", "Deadline", "Deadline2", "Organisation", "ShortDescription", "TimeStamp"];

    protected $updatesQueryString = ['order', 'field'];

    public function mount()
    {
        if (request()->has('order') && in_array(request('order'), ['asc', 'desc'])) {
            $this->orderDirection = request('order');
        }

        if (request()->has('field') && in_array(request('field'), $this->arrayCheck)) {
            $this->orderByColumn = request('field');
        }
    }

    public function render()
    {
        $projects = Project::orderBy($this->orderByColumn, $this->orderDirection)->paginate($this->itemsPerPage);
        return view('livewire.projects', ['projects' => $projects]);
    }
}
