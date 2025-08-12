<?php

require_once __DIR__ . '/../../config/config.php';
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
    ORDER BY 
        p.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$blogs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">All Blog Posts</h2>
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
                                Category
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Published On
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($blogs)): ?>
                            <tr>
                                <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No blog posts found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($blogs as $blog): ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($blog['title']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($blog['author_name']); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($blog['category_name'] ?? 'N/A'); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo date("F j, Y", strtotime($blog['created_at'])); ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <button class="text-indigo-600 hover:text-indigo-900 mr-3 open-edit-blog-modal" data-blog-id="<?php echo $blog['id']; ?>">Edit</button>
                                        <button class="text-red-600 hover:text-red-900 font-semibold open-delete-blog-modal" data-blog-id="<?php echo $blog['id']; ?>">Delete</button>
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
