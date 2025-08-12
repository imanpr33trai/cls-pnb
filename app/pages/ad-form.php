<?php
include_once(__DIR__ . '/../../config/config.php');
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = 'ad-form.php';
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$user_data = [];
$stmt_user = $conn->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user->num_rows === 1) {
    $user_data = $result_user->fetch_assoc();
}
$stmt_user->close();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $num1 = rand(1, 9);
    $num2 = rand(1, 9);
    $_SESSION['captcha_question'] = "What is $num1 + $num2?";
    $_SESSION['captcha_answer'] = $num1 + $num2;
}
$errors = [];
if (isset($_POST['btn_save'])) {
    $category       = $_POST['adcategory'] ?? '';
    $subcategory    = $_POST['adsubcategory'] ?? '';
    $other          = trim($_POST['adothercat'] ?? '');
    $adTitle        = trim($_POST['adTitlemytit'] ?? '');
    $askingPrice    = filter_var($_POST['askingPriceforad'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $description    = trim($_POST['descriptionforad'] ?? '');
    $name           = $user_data['first_name'] . ' ' . $user_data['last_name'];
    $organization   = trim($_POST['adorganizationuser'] ?? '');
    $email          = $user_data['email'];
    $phone          = trim($_POST['adphoneuser'] ?? '');
    $location       = trim($_POST['adlocationuser'] ?? '');
    $city           = trim($_POST['adcityuser'] ?? '');
    $postalCode     = trim($_POST['adpostalCodeuser'] ?? '');
    $expireIn       = $_POST['adexpireingin'] ?? '';
    $captcha_answer = trim($_POST['adtypecode'] ?? '');
    $platforms      = $_POST['platform'] ?? [];
    $links          = $_POST['link'] ?? [];

    $image = '';
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['picture']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['picture']['tmp_name'];
            $fileName = $_FILES['picture']['name'];
            $fileSize = $_FILES['picture']['size'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($fileExtension, $allowedExtensions)) {
                if ($fileSize < 5 * 1024 * 1024) {
                    $newFileName = uniqid('ad_', true) . '.' . $fileExtension;
                    $uploadDir = dirname(__DIR__, 2) . '/assets/uploads/ads_form/';
                    $uploadPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                        $image = $newFileName;
                    } else {
                        $errors[] = "Error: Could not move the uploaded file. Please check server directory permissions.";
                    }
                } else {
                    $errors[] = "Error: File is too large. Maximum size is 5MB.";
                }
            } else {
                $errors[] = "Error: Invalid file type. Please upload a JPG, PNG, WEBP, or GIF.";
            }
        } else {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE   => "The uploaded file exceeds the server's maximum file size limit.",
                UPLOAD_ERR_FORM_SIZE  => "The uploaded file exceeds the form's maximum file size limit.",
                UPLOAD_ERR_PARTIAL    => "The file was only partially uploaded. Please try again.",
                UPLOAD_ERR_NO_TMP_DIR => "Server configuration error: Missing a temporary folder.",
                UPLOAD_ERR_CANT_WRITE => "Server configuration error: Failed to write file to disk.",
                UPLOAD_ERR_EXTENSION  => "A server extension stopped the file upload.",
            ];
            $error_code = $_FILES['picture']['error'];
            $errors[] = $upload_errors[$error_code] ?? "An unknown file upload error occurred.";
        }
    }

    if (empty($category)) {
        $errors[] = "Category is required.";
    }
    if (intval($captcha_answer) !== $_SESSION['captcha_answer']) {
        $errors[] = "The answer to the math question is incorrect.";
    }


    if (empty($errors)) {
        $expires_at = date('Y-m-d H:i:s', strtotime($expireIn));

        $platform_names_array = [];
        $platform_links_array = [];
        foreach ($platforms as $index => $platform_name) {
            if (!empty($platform_name)) {
                $link_url = isset($links[$index]) ? trim($links[$index]) : '';
                if (!empty($link_url)) {
                    $platform_names_array[] = $platform_name;
                    $platform_links_array[] = $link_url;
                }
            }
        }
        $platform_names_str = implode(', ', $platform_names_array);
        $platform_links_str = implode(', ', $platform_links_array);

        $ad_slug = create_unique_slug($conn, $adTitle, 'ad_form', 'ad_slug');

        $stmt = $conn->prepare("INSERT INTO ad_form (
            category, subcategory, other_category, ad_title, asking_price, description, user_name,
            organisation, email, phone, location, city_town_neighbourhood, postal_code,
            expires_in, expires_at, image, platforms, platform_links, ad_slug
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "ssssdssssssssssssss",
            $category,
            $subcategory,
            $other,
            $adTitle,
            $askingPrice,
            $description,
            $name,
            $organization,
            $email,
            $phone,
            $location,
            $city,
            $postalCode,
            $expireIn,
            $expires_at,
            $image,
            $platform_names_str,
            $platform_links_str,
            $ad_slug
        );

        if ($stmt->execute()) {
            $_SESSION['form_success'] = "Your ad has been posted successfully!";
            unset($_SESSION['captcha_question'], $_SESSION['captcha_answer']);
            header("Location: ad-form.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
include "partials/header.php";
?>
<style>
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

    .form-control.is-invalid~.invalid-feedback,
    .form-select.is-invalid~.invalid-feedback {
        display: block;
    }

    .char-counter {
        font-size: 0.8em;
        color: #6c757d;
        text-align: right;
    }
</style>



<section class="breadcrump  py-2.5">
    <div class="container">
        <div class="row">
            <div class="d-flex gap-2">
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-1">Home >></a>
                <a href="#" class="text-decoration-none breadcrump-links breadcrump-link-2">Post Ad</a>
            </div>
        </div>
    </div>
</section>




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
                    <h1 class="fos-40 playfair-medium mb-7">Create Your Free ads</h1>

                    <?php if (!empty($_SESSION['form_success'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['form_success']; ?></div>
                        <?php unset($_SESSION['form_success']); ?>
                    <?php endif; ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $err) echo "<p class='mb-0'>$err</p>"; ?>
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST" enctype="multipart/form-data">


                        <div class="row">
                            <div class="col-lg-4 col-sm-12 d-flex flex-column mb-7">
                                <label for="categoryOfads">Category*</label>
                                <select name="adcategory" id="categoryOfads" class="form-select">
                                    <option value="">Select Category</option>
                                    <?php
                                    $cats = $conn->query("SELECT * FROM ad_categories WHERE status = 'live'");
                                    while ($row = $cats->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-lg-4 col-sm-12 d-flex flex-column mb-7">
                                <label for="subcategoryOfads">Sub Category*</label>
                                <select name="adsubcategory" id="subcategoryOfads" class="form-select">
                                    <option value="">Select Subcategory</option>
                                </select>
                            </div>

                            <div class="col-lg-4 col-sm-12 d-flex flex-column mb-7">
                                <label for="otherCategory">Others (if not in list)</label>
                                <input type="text" name="adothercat" id="otherCategory" pattern="[a-zA-Z\s\-]+" />
                                <div class="invalid-feedback">Only letters, spaces, and hyphens are allowed.</div>
                            </div>


                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7">
                                <label for="adTitle">Ad Title*</label>
                                <input type="text" name="adTitlemytit" id="adTitle" required maxlength="200" />
                                <div class="char-counter">0/200</div>
                                <div class="invalid-feedback">Title is required (max 200 characters).</div>
                            </div>


                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7">
                                <label for="adPrice">Asking price*</label>
                                <input type="number" name="askingPriceforad" id="adPrice" placeholder="Enter 0 if free" step="0.01" min="0" required />
                                <div class="invalid-feedback">Please enter a valid price (e.g., 150.50).</div>
                            </div>


                            <div class="col-lg-12 col-sm-12 d-flex flex-column mb-7">
                                <label for="descriptions">Description*</label>
                                <textarea name="descriptionforad" id="descriptions" rows="8" required></textarea>
                                <div class="invalid-feedback">Description is required.</div>
                            </div>


                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7">
                                <label for="aduserName">Your Name*</label>
                                <input type="text" name="adusername" id="aduserName" value="<?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?>" readonly />
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7">
                                <label for="aduserOrganisation">Organisation</label>
                                <input type="text" name="adorganizationuser" id="aduserOrganisation" />
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7">
                                <label for="ademailuser">Email*</label>
                                <input type="email" name="ademailuser" id="ademailuser" value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly />
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7">
                                <label for="teluserads">Phone</label>
                                <input type="tel" name="adphoneuser" id="teluserads" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>" />
                            </div>

                            <div class="col-lg-12 col-sm-12 d-flex flex-column mb-7">
                                <label for="adlocuser">Location</label>
                                <input type="text" name="adlocationuser" id="adlocuser" placeholder="" />
                            </div>

                            <div class="col-lg-12 col-sm-12 d-flex flex-column mb-7">
                                <label for="adusercityTown">City, town, or neighborhood*</label>
                                <input type="text" name="adcityuser" id="adusercityTown" placeholder="" />
                            </div>


                            <div class="col-lg-4 col-sm-12 d-flex flex-column mb-7">
                                <label for="adpostalcode">Postal code*</label>
                                <input type="text" name="adpostalCodeuser" id="adpostalcode" required pattern="\d{6}" maxlength="6" />
                                <div class="invalid-feedback">Must be a 6-digit postal code.</div>
                            </div>


                            <div class="col-lg-4 col-sm-12 d-flex flex-column mb-7">
                                <label for="expiresindate">Expires In*</label>
                                <select name="adexpireingin" id="expiresindate" class="form-select" required>
                                    <option value="">Select Duration</option>
                                    <option value="+1 week">1 Week</option>
                                    <option value="+2 weeks">2 Weeks</option>
                                    <option value="+1 month">1 Month</option>
                                    <option value="+3 months">3 Months</option>
                                    <option value="+6 months">6 Months</option>
                                </select>
                                <div class="invalid-feedback">Please select a duration.</div>
                            </div>


                            <div class="col-lg-4 col-sm-12 d-flex flex-column mb-7">
                                <label for="pleaseTypeThisCode"><?php echo $_SESSION['captcha_question'] ?? 'Please solve the math problem.'; ?>*</label>
                                <input type="number" name="adtypecode" id="pleaseTypeThisCode" required />
                                <div class="invalid-feedback">Please answer the question.</div>
                            </div>

                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7 file-upload-main">
                                <label for="imageaduser">Image</label>
                                <div class="upload-image-placeholder-area text-center">
                                    <input type="file" name="picture" id="imageaduser" class="file-uploads w-full h-full" accept="image/*" />
                                    <img src="<?php echo $base_url; ?>assets/images/upload-place.png" alt="" class="img-fluid" />
                                    <p class="poppins-medium">
                                        Drag and drop an image, or
                                        <span class="color-pink">Browse</span>
                                    </p>
                                </div>
                            </div>


                            <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7 file-upload-main">
                                <label>Preview Images</label>
                                <div id="preview-images" class="d-flex flex-wrap gap-3"></div>
                            </div>


                            <div id="platform-container">

                                <div class="row platform-row align-items-end mb-3">
                                    <div class="col-lg-4 col-sm-12 d-flex flex-column">
                                        <label>Platform</label>
                                        <select name="platform[]" class="form-select">
                                            <option value="">Select Platform</option>
                                            <option value="Facebook">Facebook</option>
                                            <option value="Instagram">Instagram</option>
                                            <option value="Twitter">Twitter</option>
                                            <option value="LinkedIn">LinkedIn</option>
                                            <option value="Website">Website</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-7 col-sm-12 d-flex flex-column">
                                        <label>Link</label>
                                        <input type="url" name="link[]" class="form-control" placeholder="https://..." />
                                    </div>
                                    <div class="col-lg-1 col-sm-12 d-flex">
                                        <button type="button" class="theme-btn remove-platform-btn" style="display:none;">X</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12 d-flex flex-column mb-12">
                                <a href="#" id="add-more-platforms" class="color-pink poppins-medium">Add More Links</a>
                            </div>

                            <div class="col-lg-12 col-sm-12 mb-7 text-center text-md-start">

                                <button type="submit" name="btn_save" id="btn_save" class="theme-btn">Post This Ad</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>



<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function() {
        ClassicEditor
            .create(document.querySelector('#descriptions'))
            .catch(error => {
                console.error(error);
            });

        const adForm = $('form');

        function validateField(input) {
            if (input.get(0).checkValidity()) {
                input.removeClass('is-invalid');
                return true;
            } else {
                input.addClass('is-invalid');
                return false;
            }
        }
        adForm.find('input[required], select[required], textarea[required]').on('blur', function() {
            validateField($(this));
        });

        function setupCounter(inputId, counterClass, maxLength) {
            const input = $(inputId);
            const counter = input.parent().find(counterClass);
            input.on('input', function() {
                counter.text($(this).val().length + '/' + maxLength);
            });
        }
        setupCounter('#adTitle', '.char-counter', 200);

        $('#imageaduser').on('change', function(e) {
            const previewContainer = $('#preview-images');
            previewContainer.html('');
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                const previewDiv = $(`
                <div class="position-relative" style="width: 100px; height: 100px;">
                    <img src="${event.target.result}" class="img-fluid rounded" style="object-fit: cover; width: 100%; height: 100%;">
                    <span class="position-absolute top-0 end-0 bg-danger text-white p-1 rounded-circle remove-preview-btn" style="cursor: pointer;">×</span>
                </div>
            `);
                previewContainer.append(previewDiv);
            };
            reader.readAsDataURL(file);
        });
        $('#preview-images').on('click', '.remove-preview-btn', function() {
            $(this).parent().remove();
            $('#imageaduser').val('');
        });

        $('#categoryOfads').on('change', function() {
            var categoryId = $(this).val();
            if (categoryId) {
                $.ajax({
                    url: '<?php echo $base_url; ?>ajax/get_subcategories.php',
                    type: 'POST',
                    data: {
                        category_id: categoryId
                    },
                    success: function(response) {
                        $('#subcategoryOfads').html(response);
                    }
                });
            } else {
                $('#subcategoryOfads').html('<option value="">Select Subcategory</option>');
            }
        });

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

        adForm.on('submit', function(e) {
            let isFormValid = true;
            adForm.find('input[required], select[required], textarea[required]').each(function() {
                if (!validateField($(this))) {
                    isFormValid = false;
                }
            });

            adForm.find('input[pattern]').each(function() {
                if ($(this).val() !== '' && !validateField($(this))) {
                    isFormValid = false;
                }
            });

            if (!isFormValid) {
                e.preventDefault();
                alert('Please fix the errors highlighted on the form.');
                $('.is-invalid').first().focus();
            }
        });

        const successPopup = document.getElementById('successPopup');
        if (successPopup && successPopup.style.display !== 'none') {
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        }


    });

    function restrictToNumbers(selector) {
        $(selector).on("input", function() {
            this.value = this.value.replace(/[^0-9]/g, "");
        });
    }
    restrictToNumbers("#teluserads");
    restrictToNumbers("#adpostalcode");
    restrictToNumbers("#adPrice");
</script>
<?php include_once('partials/footer.php'); ?>