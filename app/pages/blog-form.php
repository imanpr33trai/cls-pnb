<?php

include_once('config/config.php');
include_once(__DIR__ . '/../../config/functions.php');
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = 'Blog-form.php';
    header('Location: login.php');
    exit();
}
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
$errors = [];
if (isset($_POST['btn_save_2'])) {
    $blogtitle       = trim($_POST['blogtitle'] ?? '');
    $blogusername    = $user_data['first_name'] . ' ' . $user_data['last_name'];
    $blogcategory    = $_POST['categoryblog'] ?? '';
    $blogdiscription = trim($_POST['Descriptionusrblog'] ?? '');
    $blogemail       = $user_data['email'];
    $blogmob         = trim($_POST['adsusermobblog'] ?? '');
    $platforms       = $_POST['platform'] ?? [];
    $links           = $_POST['link'] ?? [];
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
    if (strlen($blogdiscription) > 10000) {
        $errors[] = "Description is too long (max 5000 characters).";
    }

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

    if (empty($errors)) {
        $platform_data = [];
        foreach ($platforms as $index => $platform_name) {
            if (!empty($platform_name) && isset($links[$index]) && !empty($links[$index])) {
                $platform_data[] = ['platform' => $platform_name, 'link' => trim($links[$index])];
            }
        }
        $platform_json = json_encode($platform_data);

        $blog_slug = create_unique_slug($conn, $blogtitle, 'blog_posts', 'blog_slug', 4);
        $stmt = $conn->prepare("INSERT INTO blog_posts (title, blog_slug, author_name, category_id, description, email, phone, image, platform, platform_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $empty_str = '';
        $stmt->bind_param("sssissssss", $blogtitle, $blog_slug, $blogusername, $blogcategory, $blogdiscription, $blogemail, $blogmob, $encodedImages, $platform_json, $empty_str);

        if ($stmt->execute()) {
            $_SESSION['form_success'] = "Your blog post has been submitted successfully!";
            header("Location: blog-form.php");
            exit;
        } else {
            $errors[] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
include_once(__DIR__ . '/../../partials/header.php'); ?>
<style>
    #multi-preview-area span {
        font-size: 18px;
        line-height: 1;
    }


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



<section class="breadcrump">
    <div class="container">
        <div class="row py-1.5">
            <div class="d-flex gap-2">
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-1">Home >></a>
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-2">Post Ad</a>
            </div>
        </div>
    </div>
</section>


<?php

?>

<?php if (!isset($_SESSION['user_id'])): ?>
    <div class="text-center mt-5">
        <p>You must be logged in to post.</p>
        <a href="<?php echo $base_url; ?>login.php" class="theme-btn">Login to Continue</a>
    </div>
<?php else: ?>


    <section class="form-section pb-24">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="fos-40 playfair-medium mb-7">Create Your Free Articles</h1>

                    <?php if (!empty($_SESSION['form_success'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['form_success'];
                                                            unset($_SESSION['form_success']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger"><?php foreach ($errors as $err) echo "<p class='mb-0'>$err</p>"; ?></div>
                    <?php endif; ?>
                    <form action="" method="POST" enctype="multipart/form-data">

                        <div class="row xs:text-tiny sm:text-sm ">
                            <div class="col-lg-12 col-sm-12 d-flex flex-column mb-7">
                                <label for="blogtitles">Article Title*</label>
                                <input type="text" name="blogtitle" id="blogtitles" required minlength="10" maxlength="150" />
                                <div class="char-counter">0/150</div>
                                <div class="invalid-feedback">Title is required (10-150 characters).</div>
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7">
                                <label for="blogusrname">Your Name*</label>
                                <input type="text" name="blogusernamess" id="blogusrname" value="<?= htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?>" readonly />
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7">
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





                            <div class="col-lg-12 col-sm-12 d-flex flex-column mb-7">
                                <label for="descriptionsblogs">Article Content*</label>

                                <textarea name="Descriptionusrblog" id="descriptionsblogs" rows="20"></textarea>
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7">
                                <label for="ademailuserblog">Email*</label>
                                <input type="email" name="ademailuserssblog" id="ademailuserblog" value="<?= htmlspecialchars($user_data['email']); ?>" readonly />
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7">
                                <label for="teluserblog">Phone</label>
                                <input type="tel" name="adsusermobblog" id="teluserblog" value="<?= htmlspecialchars($user_data['phone'] ?? ''); ?>" />
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7 file-upload-main">
                                <label for="previewimagesblogs">Images (Optional, up to 5)</label>
                                <input type="file" name="pictures[]" id="previewimagesblogs" class="file-uploads h-full" multiple accept="image/*" />


                                <div class="upload-image-placeholder-area">
                                    <img src="<?php echo $base_url; ?>assets/images/upload-place.png" alt="" />
                                    <p class="poppins-medium">
                                        Drag and drop an image, or
                                        <span class="color-pink">Browse</span>
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7 file-upload-main">
                                <label>Preview Images</label>
                                <div id="multi-preview-area" class="d-flex flex-wrap gap-3"></div>
                            </div>
                            <div class="col-12 mb-2 playfair-medium fos-16 ">
                                <hr>
                                <h5 class="xs:text-lg playfair-medium sm:text-xl"> Social/Portfolio Links (Optional)</h5>
                            </div>


                            <div id="platform-container" class="col-12">

                                <div class="row platform-row align-items-end ">
                                    <div class="col-lg-4 col-sm-12 d-flex flex-column mb-3">
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
                                    <div class="col-lg-7 col-sm-12 gap-0.5 flex flex-column mb-3">
                                        <label>Link</label>
                                        <input type="url" name="link[]" class="form-control" placeholder="https://..." />
                                    </div>
                                    <div class="col-lg-1 col-sm-12 gap-0.5 flex">
                                        <button type="button" class="btn btn-danger remove-platform-btn" style="display:none;">X</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12 d-flex flex-column mb-12">
                                <a href="#" id="add-more-platforms" class="color-pink poppins-medium">Add More Links</a>
                            </div>

                            <div class="col-lg-12 col-sm-12 mb-7 text-center text-md-start">
                                <button type="submit" name="btn_save_2" class="theme-btn">Post This Blog</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('previewimagesblogs').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('multi-preview-area');
            previewContainer.innerHTML = '';
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



<?php endif; ?>




<?php
include_once(__DIR__ . '/../../partials/footer.php');
?>



<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function() {
        const blogForm = $('form');
        ClassicEditor
            .create(document.querySelector('#descriptionsblogs'))
            .catch(error => {
                console.error(error);
            });

        blogForm.find('input[required], select[required], textarea[required]').on('blur', function() {
            if (this.checkValidity()) {
                $(this).removeClass('is-invalid');
            } else {
                $(this).addClass('is-invalid');
            }
        });

        function setupCounter(inputId, counterClass, maxLength) {
            $(inputId).on('input', function() {
                $(this).parent().find(counterClass).text($(this).val().length + '/' + maxLength);
            });
        }
        setupCounter('#blogtitles', '.char-counter', 150);

        $('#add-more-platforms').on('click', function(e) {
            e.preventDefault();
            const platformRow = $('.platform-row').first().clone();
            platformRow.find('input, select').val('');
            platformRow.find('.remove-platform-btn').show();
            $('#platform-container').append(platformRow);
        });

        $('#platform-container').on('click', '.remove-platform-btn', function() {
            $(this).closest('.platform-row').remove();
        });

        blogForm.on('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                alert('Please fix the errors highlighted on the form.');
                blogForm.find('input, select, textarea').trigger('blur');
                $('.is-invalid').first().focus();
            }
        });
    });

    function restrictToNumbers(selector) {
        $(selector).on("input", function() {
            this.value = this.value.replace(/[^0-9]/g, "");
        });
    }

    restrictToNumbers("#teluserblog");
</script>