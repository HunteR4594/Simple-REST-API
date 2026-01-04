<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notepad App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .note-item { cursor: pointer; transition: all 0.2s; }
        .note-item:hover { background-color: #e9ecef; }
        .note-item.active { background-color: #0d6efd; color: white; }
        .todo-item.completed .todo-title { text-decoration: line-through; opacity: 0.6; }
        .sidebar { height: calc(100vh - 120px); overflow-y: auto; }
        .content-area { height: calc(100vh - 120px); }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="bi bi-journal-text me-2"></i>Notepad App</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-tab="notes">Notes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-tab="todos">To-Do List</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Notes Section -->
        <div id="notes-section">
            <div class="row">
                <div class="col-md-4 col-lg-3">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">My Notes</h5>
                            <button class="btn btn-primary btn-sm" id="btn-new-note">
                                <i class="bi bi-plus-lg"></i> New
                            </button>
                        </div>
                        <div class="card-body sidebar p-0">
                            <div class="list-group list-group-flush" id="notes-list">
                                <!-- Notes will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-lg-9">
                    <div class="card shadow-sm content-area">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0" id="note-editor-title">Select or Create a Note</h5>
                            <div id="note-actions" style="display: none;">
                                <button class="btn btn-success btn-sm me-2" id="btn-save-note">
                                    <i class="bi bi-check-lg"></i> Save
                                </button>
                                <button class="btn btn-danger btn-sm" id="btn-delete-note">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="note-editor" style="display: none;">
                                <input type="hidden" id="note-id">
                                <div class="mb-3">
                                    <input type="text" class="form-control form-control-lg" id="note-title" placeholder="Note Title">
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" id="note-description" rows="15" placeholder="Write your note here..."></textarea>
                                </div>
                            </div>
                            <div id="note-placeholder" class="text-center text-muted py-5">
                                <i class="bi bi-journal-text" style="font-size: 4rem;"></i>
                                <p class="mt-3">Select a note from the list or create a new one</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Todos Section -->
        <div id="todos-section" style="display: none;">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-check2-square me-2"></i>To-Do List</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#todoModal" id="btn-new-todo">
                                <i class="bi bi-plus-lg"></i> Add Task
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="todos-list">
                                <!-- Todos will be loaded here -->
                            </div>
                            <div id="todos-empty" class="text-center text-muted py-5" style="display: none;">
                                <i class="bi bi-check2-square" style="font-size: 4rem;"></i>
                                <p class="mt-3">No tasks yet. Add your first task!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Todo Modal -->
    <div class="modal fade" id="todoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="todoModalLabel">Add New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="todo-id">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" id="todo-title" placeholder="Task title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="todo-description" rows="3" placeholder="Task description (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btn-save-todo">Save Task</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_URL = '/api';
        let currentNoteId = null;

        // Fetch helper
        async function fetchAPI(endpoint, options = {}) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            };
            const response = await fetch(`${API_URL}${endpoint}`, { ...defaultOptions, ...options });
            if (response.status === 204) return null;
            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'API Error');
            }
            return response.json();
        }

        // Tab Navigation
        document.querySelectorAll('[data-tab]').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('[data-tab]').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const tabName = this.dataset.tab;
                document.getElementById('notes-section').style.display = tabName === 'notes' ? 'block' : 'none';
                document.getElementById('todos-section').style.display = tabName === 'todos' ? 'block' : 'none';
                
                if (tabName === 'notes') loadNotes();
                else loadTodos();
            });
        });

        // ==================== NOTES ====================
        async function loadNotes() {
            try {
                const notes = await fetchAPI('/notes');
                console.log(notes); // test
                const notesList = document.getElementById('notes-list');
                
                if (notes.length === 0) {
                    notesList.innerHTML = '<div class="p-3 text-center text-muted">No notes yet</div>';
                    return;
                }
                
                notesList.innerHTML = notes.map(note => `
                    <a href="#" class="list-group-item list-group-item-action note-item ${note.id === currentNoteId ? 'active' : ''}" data-id="${note.id}">
                        <div class="fw-bold text-truncate">${escapeHtml(note.title)}</div>
                        <small class="text-truncate d-block ${note.id === currentNoteId ? 'text-white-50' : 'text-muted'}">${escapeHtml(note.description || 'No description')}</small>
                    </a>
                `).join('');
                
                // Add click handlers
                notesList.querySelectorAll('.note-item').forEach(item => {
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        openNote(parseInt(item.dataset.id));
                    });
                });
            } catch (error) {
                console.error('Error loading notes:', error);
                alert('Failed to load notes: ' + error.message);
            }
        }

        async function openNote(id) {
            console.log('opening note: ' + id);
            try {
                const note = await fetchAPI(`/notes/${id}`);
                console.log(note);
                currentNoteId = note.id;
                document.getElementById('note-id').value = note.id;
                document.getElementById('note-title').value = note.title;
                document.getElementById('note-description').value = note.description || '';
                document.getElementById('note-editor').style.display = 'block';
                document.getElementById('note-placeholder').style.display = 'none';
                document.getElementById('note-actions').style.display = 'block';
                document.getElementById('note-editor-title').textContent = 'Edit Note';
                loadNotes();
            } catch (error) {
                console.error('Error opening note:', error);
                alert('Failed to open note: ' + error.message);
            }
        }

        document.getElementById('btn-new-note').addEventListener('click', () => {
            currentNoteId = null;
            document.getElementById('note-id').value = '';
            document.getElementById('note-title').value = '';
            document.getElementById('note-description').value = '';
            document.getElementById('note-editor').style.display = 'block';
            document.getElementById('note-placeholder').style.display = 'none';
            document.getElementById('note-actions').style.display = 'block';
            document.getElementById('note-editor-title').textContent = 'New Note';
            
            document.querySelectorAll('.note-item').forEach(item => item.classList.remove('active'));
        });

        document.getElementById('btn-save-note').addEventListener('click', async () => {
            const title = document.getElementById('note-title').value.trim();
            const description = document.getElementById('note-description').value.trim();
            console.log('saving...', title, description);
            
            if (!title) {
                alert('Please enter a title');
                return;
            }
            
            try {
                const noteId = document.getElementById('note-id').value;
                let note;
                
                if (noteId) {
                    note = await fetchAPI(`/notes/${noteId}`, {
                        method: 'PUT',
                        body: JSON.stringify({ title, description })
                    });
                } else {
                    note = await fetchAPI('/notes', {
                        method: 'POST',
                        body: JSON.stringify({ title, description })
                    });
                    currentNoteId = note.id;
                    document.getElementById('note-id').value = note.id;
                }
                
                document.getElementById('note-editor-title').textContent = 'Edit Note';
                loadNotes();
                alert('Note saved successfully!');
            } catch (error) {
                console.error('Error saving note:', error);
                alert('Failed to save note: ' + error.message);
            }
        });

        document.getElementById('btn-delete-note').addEventListener('click', async () => {
            const noteId = document.getElementById('note-id').value;
            if (!noteId) return;
            
            if (!confirm('Are you sure you want to delete this note?')) return;
            
            try {
                await fetchAPI(`/notes/${noteId}`, { method: 'DELETE' });
                currentNoteId = null;
                document.getElementById('note-editor').style.display = 'none';
                document.getElementById('note-placeholder').style.display = 'block';
                document.getElementById('note-actions').style.display = 'none';
                document.getElementById('note-editor-title').textContent = 'Select or Create a Note';
                loadNotes();
                alert('Note deleted successfully!');
            } catch (error) {
                console.error('Error deleting note:', error);
                alert('Failed to delete note: ' + error.message);
            }
        });

        // ==================== TODOS ====================
        async function loadTodos() {
            try {
                const todos = await fetchAPI('/todos');
                console.log('todos:', todos);
                const todosList = document.getElementById('todos-list');
                const todosEmpty = document.getElementById('todos-empty');
                
                if (todos.length === 0) {
                    todosList.innerHTML = '';
                    todosEmpty.style.display = 'block';
                    return;
                }
                
                todosEmpty.style.display = 'none';
                todosList.innerHTML = todos.map(todo => `
                    <div class="card mb-2 todo-item ${todo.is_completed ? 'completed' : ''}" data-id="${todo.id}">
                        <div class="card-body d-flex align-items-center py-2">
                            <div class="form-check me-3">
                                <input class="form-check-input todo-checkbox" type="checkbox" ${todo.is_completed ? 'checked' : ''} data-id="${todo.id}">
                            </div>
                            <div class="flex-grow-1">
                                <div class="todo-title fw-bold">${escapeHtml(todo.title)}</div>
                                ${todo.description ? `<small class="text-muted">${escapeHtml(todo.description)}</small>` : ''}
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1 btn-edit-todo" data-id="${todo.id}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-delete-todo" data-id="${todo.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
                
                // Add event listeners
                todosList.querySelectorAll('.todo-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', () => toggleTodo(parseInt(checkbox.dataset.id)));
                });
                
                todosList.querySelectorAll('.btn-edit-todo').forEach(btn => {
                    btn.addEventListener('click', () => editTodo(parseInt(btn.dataset.id)));
                });
                
                todosList.querySelectorAll('.btn-delete-todo').forEach(btn => {
                    btn.addEventListener('click', () => deleteTodo(parseInt(btn.dataset.id)));
                });
            } catch (error) {
                console.error('Error loading todos:', error);
                alert('Failed to load todos: ' + error.message);
            }
        }

        async function toggleTodo(id) {
            try {
                await fetchAPI(`/todos/${id}/toggle`, { method: 'PATCH' });
                loadTodos();
            } catch (error) {
                console.error('Error toggling todo:', error);
                alert('Failed to toggle todo: ' + error.message);
            }
        }

        async function editTodo(id) {
            try {
                const todo = await fetchAPI(`/todos/${id}`);
                document.getElementById('todo-id').value = todo.id;
                document.getElementById('todo-title').value = todo.title;
                document.getElementById('todo-description').value = todo.description || '';
                document.getElementById('todoModalLabel').textContent = 'Edit Task';
                new bootstrap.Modal(document.getElementById('todoModal')).show();
            } catch (error) {
                console.error('Error loading todo:', error);
                alert('Failed to load todo: ' + error.message);
            }
        }

        async function deleteTodo(id) {
            if (!confirm('Are you sure you want to delete this task?')) return;
            
            try {
                await fetchAPI(`/todos/${id}`, { method: 'DELETE' });
                loadTodos();
                alert('Task deleted successfully!');
            } catch (error) {
                console.error('Error deleting todo:', error);
                alert('Failed to delete todo: ' + error.message);
            }
        }

        document.getElementById('btn-new-todo').addEventListener('click', () => {
            document.getElementById('todo-id').value = '';
            document.getElementById('todo-title').value = '';
            document.getElementById('todo-description').value = '';
            document.getElementById('todoModalLabel').textContent = 'Add New Task';
        });

        document.getElementById('btn-save-todo').addEventListener('click', async () => {
            const title = document.getElementById('todo-title').value.trim();
            const description = document.getElementById('todo-description').value.trim();
            
            if (!title) {
                alert('Please enter a title');
                return;
            }
            
            try {
                const todoId = document.getElementById('todo-id').value;
                
                if (todoId) {
                    await fetchAPI(`/todos/${todoId}`, {
                        method: 'PUT',
                        body: JSON.stringify({ title, description })
                    });
                } else {
                    await fetchAPI('/todos', {
                        method: 'POST',
                        body: JSON.stringify({ title, description })
                    });
                }
                
                bootstrap.Modal.getInstance(document.getElementById('todoModal')).hide();
                loadTodos();
                alert('Task saved successfully!');
            } catch (error) {
                console.error('Error saving todo:', error);
                alert('Failed to save task: ' + error.message);
            }
        });

        // Helper function
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', () => {
            loadNotes();
        });
    </script>
</body>
</html>
