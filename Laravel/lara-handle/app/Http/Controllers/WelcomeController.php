<?php  
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Container\Container;

class WelcomeController
{
    public function index()
    {
        $student = Student::first();
        $data = $student->getAttributes();
        $app = Container::getInstance();
        $factory = $app->make('view');  // 视图工厂类 Illuminate\View\Factory
        return $factory->make('welcome')->with('data', $data);
    }
}