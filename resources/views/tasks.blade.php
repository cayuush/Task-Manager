<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
     <!-- Local CSS for DataTables -->
     <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <!-- Local jQuery -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

    <!-- Local DataTables JS -->
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <style>
        
        table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        }

        th, td {
        border: 1px solid #ddd; /* Horizontal and vertical lines */
        padding: 8px;
        text-align: left;
        }

        th {
        background-color: #f4f4f4;
        font-weight: bold;
        }

        tr:nth-child(even) {
        background-color: #f9f9f9; /* Zebra striping */
        }

        tr:hover {
        background-color: #f1f1f1; /* Row hover effect */
        }

     </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Task Manager</h1>

    <!-- Task Form -->
    <div class="card my-4">
        <div class="card-header">
            <h3>Create Task</h3>
        </div>
        <div class="card-body">
            <form id="taskForm">
                <!-- CSRF Token -->
                  <input type="hidden" name="_token" id="csrf_token" value="{{ csrf_token() }}">
                <div class="mb-3">
                    <label for="title" class="form-label">Task Title</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter task title" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="completed" name="completed">
                    <label class="form-check-label" for="completed">Completed</label>
                </div>
                <button type="submit" class="btn btn-primary">Add Task</button>
            </form>
        </div>
    </div>

    <!-- Task List -->
    <div class="card">
        <div class="card-header">
            <h3>Task List</h3>
        </div>
        <div class="card-body">
            <table  id="tblData" width="100%" cellspacing="0">
                <thead>
                <!-- Headers will be dynamically populated -->
                </thead>
                <tbody>
                <!-- Data will be dynamically populated -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Fetch and display tasks
    function loadData(weatherData) {
        // Handle single object vs array
        if (!Array.isArray(weatherData)) {
            weatherData = [weatherData];
        }

        if (weatherData.length === 0) {
            $('#error').text('No data found.').show();
            $('#tblData').hide();
            return;
        }

        // Clear existing table data before inserting new data
        if ($.fn.dataTable.isDataTable('#tblData')) {
            $('#tblData').DataTable().destroy();
        }

        // Show the table
        $('#tblData').show();
        $('#tblData thead').empty();
        $('#tblData tbody').empty();

        // Generate headers dynamically
        const headers = ['#', 'Title', 'Completed', 'Source', 'Local ID', 'External ID']; // Custom headers
        let headerHTML = '<tr>';
        headers.forEach(header => {
            headerHTML += `<th>${header}</th>`;
        });
        headerHTML += '</tr>';
        $('#tblData thead').append(headerHTML);

        // Generate rows dynamically
        let rowsHTML = '';
        weatherData.forEach((item, index) => {
            rowsHTML += '<tr>';
            rowsHTML += `<td>${index + 1}</td>`; // Index as row number
            rowsHTML += `<td>${item.title}</td>`;
            rowsHTML += `<td>${item.completed ? 'Yes' : 'No'}</td>`;
            rowsHTML += `<td>${item.external_id ? 'External API' : 'Local'}</td>`;
            rowsHTML += `<td>${item.id || 'N/A'}</td>`;
            rowsHTML += `<td>${item.external_id || 'N/A'}</td>`;
            rowsHTML += '</tr>';
        });
        $('#tblData tbody').append(rowsHTML);

        // Initialize DataTable
        $('#tblData').DataTable({
            destroy: true,
            responsive: true,
            paging: true,
            searching: true
        });
    }
    // Set up CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('#csrf_token').val() // Get the token from the hidden input
    }
});
    // Add a new task
    $('#taskForm').on('submit', function (e) {
        e.preventDefault();
        
        const taskData = {
            title: $('#title').val(), // Task title from the input field
            completed: $('#completed').is(':checked') ? 1 : 0 // Checkbox value (true/false)
        };

        // AJAX POST request to /api/tasks
        $.ajax({
            url: '/api/tasks',
            method: 'POST',
            data: taskData,
            success: function () {
                fetchTasks(); // Refresh the task list after adding
                $('#taskForm')[0].reset(); // Clear the form
            },
            error: function () {
                alert('Failed to add task.');
            }
        });
    });

    // Fetch and load tasks on page load
    function fetchTasks() {
        $.ajax({
            url: 'tasks', // GET /api/tasks
            method: 'GET',
            success: function (tasks) {
                loadData(tasks); // Call loadData to render the tasks dynamically
            },
            error: function () {
                alert('Failed to load tasks.');
            }
        });
    }

    // Load tasks on page load
    $(document).ready(function () {
        fetchTasks(); // Initially fetch tasks
    });
</script>

</body>
</html>
