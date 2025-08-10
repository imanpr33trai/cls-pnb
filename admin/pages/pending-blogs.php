<?php
// /admin/pages/pending-blogs.php
require_once __DIR__ . '/../../config/config.php';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blog_id'], $_POST['status'])) {
    $blog_id = (int)$_POST['blog_id'];
    $status = $_POST['status'];

    if (in_array($status, ['live', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE blog_posts SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $blog_id);
        if ($stmt->execute()) {
            echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Blog status updated successfully!</div>";
        } else {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error updating status.</div>";
        }
        $stmt->close();
    }
}

// Fetch all blog posts with 'pending' status
$stmt = $conn->prepare("
    SELECT 
        p.id, 
        p.title, 
        p.author_name,
        c.name AS category_name,
        p.created_at
    FROM 
        blog_posts p
    LEFT JOIN 
        blog_categories c ON p.category_id = c.id
    WHERE
        p.status = 'pending'
    ORDER BY 
        p.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$pending_blogs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">Pending Blogs for Approval</h2>
        </div>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Title
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Author
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Submitted On
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pending_blogs)): ?>
                            <tr>
                                <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No pending blogs found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pending_blogs as $blog): ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($blog['title']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($blog['author_name']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo date("F j, Y", strtotime($blog['created_at'])); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <form action="" method="POST" class="inline-block">
                                            <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                            <button type="submit" name="status" value="live" class="text-green-600 hover:text-green-900 mr-3 font-semibold">Approve</button>
                                        </form>
                                        <form action="" method="POST" class="inline-block">
                                            <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                            <button type="submit" name="status" value="rejected" class="text-red-600 hover:text-red-900 font-semibold">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
