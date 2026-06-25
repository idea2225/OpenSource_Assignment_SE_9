<?php
$dataFile = 'projects.json';

if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}

$projects = json_decode(file_get_contents($dataFile), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {
    $newProject = [
        'title' => htmlspecialchars($_POST['title']),
        'description' => htmlspecialchars($_POST['description']),
        'status' => htmlspecialchars($_POST['status']),
        'date' => date('Y-m-d')
    ];
    $projects[] = $newProject;
    file_put_contents($dataFile, json_encode($projects, JSON_PRETTY_PRINT));
    header("Location: index.php");
    exit;
}

$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Software Project Tracking System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🚀 Software Project Tracking System</h1>
        
        <div class="card">
            <h2>Add New Project</h2>
            <form action="index.php" method="POST">
                <div class="form-group">
                    <label for="title">Project Title:</label>
                    <input type="text" id="title" name="title" required placeholder="e.g., E-Commerce API">
                </div>
                <div class="form-group">
                    <label for="description">Project Description:</label>
                    <textarea id="description" name="description" required placeholder="Describe project requirements..."></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Project Status:</label>
                    <select id="status" name="status">
                        <option value="Not Started">❌ Not Started</option>
                        <option value="In Progress">⏳ In Progress</option>
                        <option value="Completed">✅ Completed</option>
                    </select>
                </div>
                <button type="submit" name="add_project">Record Project</button>
            </form>
        </div>

        <div class="card">
            <h2>Search Projects</h2>
            <form action="index.php" method="GET" class="search-form">
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search by project name...">
                <button type="submit">Search</button>
                <?php if ($searchQuery !== ''): ?>
                    <a href="index.php" class="btn-clear">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h2>Tracked Projects</h2>
            <table>
                <thead>
                    <tr>
                        <th>Project Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $hasResults = false;
                    foreach ($projects as $project): 
                        if ($searchQuery !== '' && stripos($project['title'], $searchQuery) === false) {
                            continue;
                        }
                        $hasResults = true;
                        $statusClass = 'status-' . strtolower(str_replace(' ', '-', $project['status']));
                    ?>
                        <tr>
                            <td><strong><?php echo $project['title']; ?></strong></td>
                            <td><?php echo $project['description']; ?></td>
                            <td><span class="badge <?php echo $statusClass; ?>"><?php echo $project['status']; ?></span></td>
                            <td><?php echo $project['date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (!$hasResults): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #888;">No projects tracked yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
