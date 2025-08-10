<?php
// /admin/pages/view-subscribers.php
require_once __DIR__ . '/../../config/config.php';

// Handle subscriber deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_subscriber'])) {
    $subscriber_id = (int)$_POST['subscriber_id'];
    $stmt = $conn->prepare("DELETE FROM subscribers WHERE id = ?");
    $stmt->bind_param("i", $subscriber_id);
    if ($stmt->execute()) {
        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>✅ Subscriber deleted successfully!</div>";
    } else {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>❌ Error deleting subscriber.</div>";
    }
    $stmt->close();
}

// Fetch all subscribers
$stmt = $conn->prepare("SELECT id, email, created_at FROM subscribers ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$subscribers = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold leading-tight">Newsletter Subscribers</h2>
            <a href="#" data-page="send-newsletter" class="tab-link bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Send Newsletter
            </a>
        </div>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Email Address
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Subscribed On
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($subscribers)): ?>
                            <tr>
                                <td colspan="3" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No subscribers found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($subscribers as $subscriber): ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($subscriber['email']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo date("F j, Y", strtotime($subscriber['created_at'])); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <form action="" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this subscriber?');">
                                            <input type="hidden" name="subscriber_id" value="<?php echo $subscriber['id']; ?>">
                                            <button type="submit" name="delete_subscriber" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
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
