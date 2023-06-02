<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Livewire\WithPagination;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithFileUploads;
class StudentShow extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $photo, $name, $email, $course, $student_id;
    public $search = '';

    protected function rules()
    {
        return [
            'photo' => 'image|max:1024',
            'name' => 'required|string|min:6',
            'email' => ['required','email'],
            'course' => 'required|string',
        ];
        
    }

    public function updated($fields)
    {
        $this->validateOnly($fields);
    }

    public function saveStudent()
    {
        $validatedData = $this->validate();

        Student::create($validatedData);
        
        session()->flash('message','Student Added Successfully');
        $this->resetInput();
        $this->dispatchBrowserEvent('close-modal');
    

       // $students = new Student();
        
       // $imageName = Carbon::now()->timestamp. '.' .$this->photo->extension();
       // $this->photo->storeAs('images', $imageName);
        // students-> photo = $imageName;
        //$students->save();
    }

    public function editStudent(int $student_id)
    {
        $student = Student::find($student_id);
        if($student){

            $this->student_id = $student->id;
            $this->photo = $student->photo;
            $this->name = $student->name;
            $this->email = $student->email;
            $this->course = $student->course;
        }else{
            return redirect()->to('/students');
        }
    }

    public function updateStudent()
    {
        $validatedData = $this->validate();

        Student::where('id',$this->student_id)->update([
            'photo' => $validatedData['photo'],
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'course' => $validatedData['course']
        ]);
        session()->flash('message','Student Updated Successfully');
        $this->resetInput();
        $this->dispatchBrowserEvent('close-modal');
    }

    public function deleteStudent(int $student_id)
    {
        $this->student_id = $student_id;
    }

    public function destroyStudent()
    {
        Student::find($this->student_id)->delete();
        session()->flash('message','Student Deleted Successfully');
        $this->dispatchBrowserEvent('close-modal');
    }

    public function closeModal()
    {
        $this->resetInput();
    }

    public function resetInput()
    {
        $this->name = '';
        $this->email = '';
        $this->course = '';
    }

    public function render()
    {
        $students = Student::where('name', 'like', '%'.$this->search.'%')-> orderBy ('id','DESC')->paginate(5);
        return view('livewire.student-show', ['students' => $students]);
    }
}