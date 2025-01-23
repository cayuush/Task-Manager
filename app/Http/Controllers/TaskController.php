<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Http;
class TaskController extends Controller
{
    //store
  
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'completed' => 'required|boolean',
        ]);
    
        // Store the task in the local database
        $task = Task::create([
            'title' => $request->title,
            'completed' => $request->completed,
        ]);
    
        // Sync the task with the external API
        $response = Http::post('https://jsonplaceholder.typicode.com/todos', [
            'title' => $task->title,
            'completed' => $task->completed,
        ]);
    
        if ($response->successful()) {
            $task->external_id = $response->json()['id'];
            $task->save(); // Update the local task with the external ID
        }
    
        return response()->json($task); // Return the task details as JSON
    }
    
//get

    /**
     * Retrieve tasks from the local database and the external API, combine them,
     * and return as a JSON response.
     */
    public function index()
    {
        // Step 1: Fetch all tasks from the local database
        $localTasks = Task::all();

        // Step 2: Fetch additional tasks from the external API
        $externalTasksResponse = Http::get('https://jsonplaceholder.typicode.com/todos');
        $externalTasks = [];
        if ($externalTasksResponse->successful()) {
            $externalTasks = $externalTasksResponse->json();
        }

        // Step 3: Format the local tasks
        $formattedLocalTasks = $localTasks->map(function ($task) {
            return [
                'id' => $task->id,              // Local task ID
                'title' => $task->title,        // Task title
                'completed' => $task->completed, // Completion status
                'source' => 'Local',            // Origin of task
                'external_id' => $task->external_id, // External ID (if available)
            ];
        })->toArray();

        // Step 4: Format the external tasks
        $formattedExternalTasks = array_map(function ($externalTask) {
            return [
                'id' => null,                    // No local ID for external tasks
                'title' => $externalTask['title'],  // Task title
                'completed' => $externalTask['completed'], // Completion status
                'source' => 'External API',      // Origin of task
                'external_id' => $externalTask['id'], // External task ID
            ];
        }, $externalTasks);

        // Step 5: Combine both task lists
        $combinedTasks = array_merge($formattedLocalTasks, $formattedExternalTasks);

        // Step 6: Return the combined list as a JSON response
        return response()->json($combinedTasks);
    }
}




