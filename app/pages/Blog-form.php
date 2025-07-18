<?php
// =========================================================================
// PART 1: ALL PHP LOGIC - THIS IS THE ONLY PHP LOGIC BLOCK YOU NEED
// =========================================================================


include_once('config/config.php');
include_once('config/functions.php');
require __DIR__ . '/../../config/whoops.php';
// --- 1. Security: Redirect non-logged-in users ---
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = 'Blog-form.php';
    header('Location: login.php');
    exit();
}

// --- 2. Fetch Logged-in User Data for Pre-filling the Form ---
$user_id = $_SESSION['user_id'];
$user_data = [];
$stmt_user = $conn->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user && $result_user->num_rows === 1) {
    $user_data = $result_user->fetch_assoc();
}
$stmt_user->close();

// --- 3. Form Submission Logic ---
$errors = [];
if (isset($_POST['btn_save_2'])) {
    // --- Collect and Sanitize Form Data ---
    $blogtitle       = trim($_POST['blogtitle'] ?? '');
    $blogusername    = $user_data['first_name'] . ' ' . $user_data['last_name'];
    $blogcategory    = $_POST['categoryblog'] ?? '';
    $blogdiscription = trim($_POST['Descriptionusrblog'] ?? '');
    $blogemail       = $user_data['email'];
    $blogmob         = trim($_POST['adsusermobblog'] ?? '');
    $platforms       = $_POST['platform'] ?? []; // Comes as an array
    $links           = $_POST['link'] ?? [];     // Comes as an array

    // --- Server-Side Validation ---
    if (empty($blogtitle)) {
        $errors[] = "Article Title is required.";
    }
    if (strlen($blogtitle) > 150) {
        $errors[] = "Article Title cannot exceed 150 characters.";
    }
    if (empty($blogcategory)) {
        $errors[] = "Category is required.";
    }
    if (empty($blogdiscription)) {
        $errors[] = "Description is required.";
    }
    if (strlen($blogdiscription) > 5000) {
        $errors[] = "Description is too long (max 5000 characters).";
    }

    // --- Enhanced Image Upload Logic ---
    $imageNames = [];
    if (isset($_FILES['pictures']) && !empty($_FILES['pictures']['name'][0])) {
        $uploadDir = 'assets/uploads/blog_form/';
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        foreach ($_FILES['pictures']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['pictures']['error'][$key] === UPLOAD_ERR_OK) {
                $fileExt = strtolower(pathinfo($_FILES['pictures']['name'][$key], PATHINFO_EXTENSION));
                if (in_array($fileExt, $allowedExtensions)) {
                    $newFileName = uniqid('blog_', true) . '.' . $fileExt;
                    if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
                        $imageNames[] = $newFileName;
                    }
                } else {
                    $errors[] = "Invalid file type uploaded: " . htmlspecialchars($_FILES['pictures']['name'][$key]);
                }
            }
        }
    }
    $encodedImages = json_encode($imageNames);

    // --- Process and Store Data if No Errors ---
    if (empty($errors)) {
        // Prepare platforms/links for database (JSON encode)
        $platform_data = [];
        foreach ($platforms as $index => $platform_name) {
            if (!empty($platform_name) && isset($links[$index]) && !empty($links[$index])) {
                $platform_data[] = ['platform' => $platform_name, 'link' => trim($links[$index])];
            }
        }
        $platform_json = json_encode($platform_data);

        // Insert into DB
        $blog_slug = generate_slug($blogtitle);
        $stmt = $conn->prepare("INSERT INTO blog_posts (title, blog_slug, author_name, category_id, description, email, phone, image, platform, platform_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $empty_str = ''; // platform_link is no longer used individually
        $stmt->bind_param("sssissssss", $blogtitle, $blog_slug, $blogusername, $blogcategory, $blogdiscription, $blogemail, $blogmob, $encodedImages, $platform_json, $empty_str);

        if ($stmt->execute()) {
            $_SESSION['form_success'] = "Your blog post has been submitted successfully!";
            header("Location: Blog-form.php");
            exit;
        } else {
            $errors[] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// --- 4. Include Header (Now that all PHP logic is done) ---
include_once(__DIR__ . '/../../partials/header.php');

// =========================================================================
// PART 2: THE HTML STRUCTURE (USING THE DATA WE FETCHED ABOVE)
// =========================================================================
?>
<style>
    #multi-preview-area span {
        font-size: 18px;
        line-height: 1;
    }

    /* ... Your existing styles for preview images are good ... */
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: .25rem;
        font-size: .875em;
        color: #dc3545;
    }

    .form-control.is-invalid~.invalid-feedback {
        display: block;
    }

    .char-counter {
        font-size: 0.8em;
        color: #6c757d;
        text-align: right;
    }
</style>

<!-- Breadcrump -->
<!-- Breadcrump -->
<section class="breadcrump">
    <div class="container">
        <div class="row">
            <div class="d-flex gap-2">
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-1">Home >></a>
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-2">Post Ad</a>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrump -->
<!-- Breadcrump -->
<?php

?>

<?php if (!isset($_SESSION['user_id'])): ?>
    <div class="text-center mt-5">
        <p>You must be logged in to post.</p>
        <a href="<?php echo $base_url; ?>login.php" class="theme-btn">Login to Continue</a>
    </div>
<?php else: ?>
    <!-- form section -->
    <!-- form section -->
    <section class="form-section pb-100">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="fos-40 playfair-medium mb-30">Create Your Free Articles</h1>
                    <!-- Display Success/Error Messages -->
                    <?php if (!empty($_SESSION['form_success'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['form_success'];
                                                            unset($_SESSION['form_success']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger"><?php foreach ($errors as $err) echo "<p class='mb-0'>$err</p>"; ?></div>
                    <?php endif; ?>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <!-- form row-->
                        <div class="row">
                            <div class="col-lg-12 col-sm-12 d-flex flex-column mb-30">
                                <label for="blogtitles">Article Title*</label>
                                <input type="text" name="blogtitle" id="blogtitles" required minlength="10" maxlength="150" />
                                <div class="char-counter">0/150</div>
                                <div class="invalid-feedback">Title is required (10-150 characters).</div>
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-30">
                                <label for="blogusrname">Your Name*</label>
                                <input type="text" name="blogusernamess" id="blogusrname" value="<?= htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?>" readonly />
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-30">
                                <label for="categoryOfblog">Category*</label>
                                <select name="categoryblog" id="categoryOfblog" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    $result = $conn->query("SELECT id, name FROM blog_categories");
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">Please select a category.</div>
                            </div>

                            <!-- Description -->
                            <!-- <div class="col-lg-12 col-sm-12 d-flex flex-column mb-30">
                            <label for="descriptionsblogs">Description*</label>
                            <textarea name="Descriptionusrblog" id="descriptionsblogs" rows="8" required minlength="50" maxlength="5000"></textarea>
                            <div class="char-counter">0/5000</div>
                            <div class="invalid-feedback">Description is required (50-5000 characters).</div>
                        </div> -->

                            <!-- Inside your Blog-form.php form -->
                            <div class="col-lg-12 col-sm-12 d-flex flex-column mb-30">
                                <label for="descriptionsblogs">Article Content*</label>
                                <!-- The ID "descriptionsblogs" is important -->
                                <textarea name="Descriptionusrblog" id="descriptionsblogs" rows="20"></textarea>
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-30">
                                <label for="ademailuserblog">Email*</label>
                                <input type="email" name="ademailuserssblog" id="ademailuserblog" value="<?= htmlspecialchars($user_data['email']); ?>" readonly />
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-30">
                                <label for="teluserblog">Phone</label>
                                <input type="tel" name="adsusermobblog" id="teluserblog" value="<?= htmlspecialchars($user_data['phone'] ?? ''); ?>" />
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-30 file-upload-main">
                                <label for="previewimagesblogs">Images (Optional, up to 5)</label>
                                <input type="file" name="pictures[]" id="previewimagesblogs" class="file-uploads" multiple accept="image/*" />


                                <div class="upload-image-placeholder-area">
                                    <img src="<?php echo $base_url; ?>assets/images/upload-place.png" alt="" />
                                    <p class="poppins-medium">
                                        Drag and drop an image, or
                                        <span class="color-pink">Browse</span>
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-30 file-upload-main">
                                <label>Preview Images</label>
                                <div id="multi-preview-area" class="d-flex flex-wrap gap-3"></div>
                            </div>
                            <div class="col-12 mb-2 fos-16">
                                <hr>
                                <h5>Social/Portfolio Links (Optional)</h5>
                            </div>
                            <!-- Platform & Link Repeatable Section -->

                            <div id="platform-container" class="col-12">
                                <!-- Initial Row -->
                                <div class="row platform-row align-items-end mb-3">
                                    <div class="col-lg-4 col-sm-12 d-flex flex-column">
                                        <label>Platform</label>
                                        <select name="platform[]" class="form-select">
                                            <option value="">Select Platform</option>
                                            <option value="Facebook">Facebook</option>
                                            <option value="Twitter">Twitter</option>
                                            <option value="Instagram">Instagram</option>
                                            <option value="LinkedIn">LinkedIn</option>
                                            <option value="GitHub">GitHub</option>
                                            <option value="Portfolio">Portfolio/Website</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-7 col-sm-12 d-flex flex-column">
                                        <label>Link</label>
                                        <input type="url" name="link[]" class="form-control" placeholder="https://..." />
                                    </div>
                                    <div class="col-lg-1 col-sm-12 d-flex">
                                        <button type="button" class="btn btn-danger remove-platform-btn" style="display:none;">X</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12 d-flex flex-column mb-50">
                                <a href="#" id="add-more-platforms" class="color-pink poppins-medium">Add More Links</a>
                            </div>

                            <div class="col-lg-12 col-sm-12 mb-30 text-center text-md-start">
                                <button type="submit" name="btn_save_2" class="theme-btn">Post This Blog</button>
                            </div>
                        </div>
                        <!-- form row -->
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('previewimagesblogs').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('multi-preview-area');
            previewContainer.innerHTML = ''; // Clear previous previews

            const files = e.target.files;
            if (!files.length) return;

            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const previewDiv = document.createElement('div');
                    previewDiv.classList.add('position-relative');
                    previewDiv.style.width = '100px';
                    previewDiv.style.height = '100px';

                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.classList.add('img-fluid', 'rounded');
                    img.style.objectFit = 'cover';
                    img.style.width = '100%';
                    img.style.height = '100%';

                    const removeBtn = document.createElement('span');
                    removeBtn.innerHTML = '&times;';
                    removeBtn.classList.add('position-absolute', 'top-0', 'end-0', 'bg-danger', 'text-white', 'p-1', 'rounded-circle');
                    removeBtn.style.cursor = 'pointer';

                    removeBtn.onclick = () => {
                        previewDiv.remove();
                        // Optional: Clear file from input (reset input if all removed)
                        if (previewContainer.children.length === 1) {
                            e.target.value = '';
                        }
                    };

                    previewDiv.appendChild(img);
                    previewDiv.appendChild(removeBtn);
                    previewContainer.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>

    <!-- form section -->
    <!-- form section -->
<?php endif; ?>


<!-- footer -->
<!-- footer -->
<?php
include_once(__DIR__ . '/../../partials/footer.php');
?>
<!-- footer -->
<!-- footer -->
<!-- Include jQuery if it's not in your footer -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Unified script for the blog form -->
<script>
    $(document).ready(function() {
        const blogForm = $('#blogForm');

        // --- Initialize TinyMCE ---
        tinymce.init({
            selector: '#descriptionsblogs', // Targets your textarea
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            height: 500, // Make the editor taller
            menubar: false,
        });

        // --- Live validation feedback ---
        blogForm.find('input[required], select[required], textarea[required]').on('blur', function() {
            if (this.checkValidity()) {
                $(this).removeClass('is-invalid');
            } else {
                $(this).addClass('is-invalid');
            }
        });

        // --- Character Counters ---
        function setupCounter(inputId, counterClass, maxLength) {
            $(inputId).on('input', function() {
                $(this).parent().find(counterClass).text($(this).val().length + '/' + maxLength);
            });
        }
        setupCounter('#blogtitles', '.char-counter', 150);
        setupCounter('#descriptionsblogs', '.char-counter', 5000);

        // --- Multiple Image Preview (Your existing vanilla JS is good) ---
        document.getElementById('previewimagesblogs').addEventListener('change', function(e) {
            // ... your existing multi-image preview JS is perfect ...
        });

        // --- "Add More" Platforms Logic ---
        $('#add-more-platforms').on('click', function(e) {
            e.preventDefault();
            const platformRow = $('.platform-row').first().clone();
            platformRow.find('input, select').val('');
            platformRow.find('.remove-platform-btn').show();
            $('#platform-container').append(platformRow);
        });

        // --- "Remove" Platform Logic ---
        $('#platform-container').on('click', '.remove-platform-btn', function() {
            $(this).closest('.platform-row').remove();
        });

        // --- Final validation on submit ---
        blogForm.on('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                alert('Please fix the errors highlighted on the form.');
                // Trigger blur on all fields to show all errors
                blogForm.find('input, select, textarea').trigger('blur');
                $('.is-invalid').first().focus();
            }
        });
    });
</script>