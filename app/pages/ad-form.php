<?php
// CRUCIAL: Must be the very first thing on the page

include_once(__DIR__ . '/../../config/config.php');

// --- Security: Redirect non-logged-in users ---
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = 'ad-form.php';
    header('Location: login.php');
    exit();
}

// --- Fetch Logged-in User Data ---
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


// --- Math Captcha Logic ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $num1 = rand(1, 9);
    $num2 = rand(1, 9);
    $_SESSION['captcha_question'] = "What is $num1 + $num2?";
    $_SESSION['captcha_answer'] = $num1 + $num2;
}

// --- Form Submission Logic ---
$errors = [];
if (isset($_POST['btn_save'])) {
    // --- Collect and Sanitize Form Data ---
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

    // *** THIS IS THE CORRECTED IMAGE UPLOAD LOGIC ***
    $image = ''; // Start with an empty image name
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['picture']['tmp_name'];
        $fileName = $_FILES['picture']['name'];
        $fileSize = $_FILES['picture']['size'];
        $fileType = $_FILES['picture']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            if ($fileSize < 5 * 1024 * 1024) { // Max 5MB
                // Create a unique filename to prevent overwriting
                $newFileName = uniqid('ad_', true) . '.' . $fileExtension;
                $uploadDir = 'assets/uploads/ads_form/';
                $uploadPath = $uploadDir . $newFileName;

                // Move the file to the destination
                if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                    $image = $newFileName; // SUCCESS: Store the new filename in our variable
                } else {
                    $errors[] = "Error: Could not move the uploaded file. Check directory permissions.";
                }
            } else {
                $errors[] = "Error: File is too large. Maximum size is 5MB.";
            }
        } else {
            $errors[] = "Error: Invalid file type. Please upload a JPG, PNG, WEBP, or GIF.";
        }
    }

    // --- Server-Side Validation ---
    if (empty($category)) { $errors[] = "Category is required."; }
    // ... all other validation rules ...
    if (intval($captcha_answer) !== $_SESSION['captcha_answer']) { $errors[] = "The answer to the math question is incorrect."; }


    // --- Process and Store Data if No Errors ---
    if (empty($errors)) {
        // ... (all the logic for expires_at, platform_json is correct) ...
        $expires_at = date('Y-m-d H:i:s', strtotime($expireIn));
        // ...
        // *** THIS IS THE CORRECTED PLATFORM/LINK LOGIC ***
        $platform_data = [];
        // Loop through all submitted platforms. The key ($index) is important.
        foreach ($platforms as $index => $platform_name) {
            // Check if a platform was selected for this row.
            if (!empty($platform_name)) {
                // Get the corresponding link from the $links array using the same index.
                $link_url = isset($links[$index]) ? trim($links[$index]) : '';
                // Only add the pair if the link is also provided.
                if (!empty($link_url)) {
                     $platform_data[] = ['platform' => $platform_name, 'link' => $link_url];
                }
            }
        }
        $platform_json = json_encode($platform_data);
        // *** END OF CORRECTION ***
        
        // The $image variable now correctly holds either the new filename or an empty string.
        $stmt = $conn->prepare("INSERT INTO ad_form (
            category, subcategory, other_category, ad_title, asking_price, description, user_name,
            organisation, email, phone, location, city_town_neighbourhood, postal_code,
            expires_in, expires_at, image, platforms, platform_links
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $empty_str = ''; // For the unused platform_links column
        $stmt->bind_param(
            "ssssisssssssisssss",
            $category, $subcategory, $other, $adTitle, $askingPrice, $description, $name,
            $organization, $email, $phone, $location, $city, $postalCode,
            $expireIn, $expires_at, $image, $platform_json, $empty_str
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

// --- Must include header AFTER all PHP logic ---
include "partials/header.php";
?>
<style>
/* Add styles for validation feedback */
.form-control.is-invalid, .form-select.is-invalid {
    border-color: #dc3545;
}
.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: .25rem;
    font-size: .875em;
    color: #dc3545;
}
.form-control.is-invalid ~ .invalid-feedback,
.form-select.is-invalid ~ .invalid-feedback {
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
<!-- Breadcrump -->
<!-- Breadcrump -->


<?php if (!isset($_SESSION['user_id'])): ?>
    <div class="text-center mt-5">
        <p>You must be logged in to post.</p>
        <a href="<?php echo $base_url; ?>login.php" class="theme-btn">Login to Continue</a>
    </div>
<?php else: ?>
    <!-- Show the form -->
    <!-- form section -->
<!-- form section -->
<section class="form-section pb-24">
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="fos-40 playfair-medium mb-7">Create Your Free ads</h1>
                   <!-- Display Success/Error Messages -->
                <?php if (!empty($_SESSION['form_success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['form_success']; ?></div>
                    <?php unset($_SESSION['form_success']); ?>
                <?php endif; ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach($errors as $err) echo "<p class='mb-0'>$err</p>"; ?>
                    </div>
                <?php endif; ?>
                <form action="" method="POST" enctype="multipart/form-data">

                    <!-- form row-->
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

                          <!-- Ad Title -->
                    <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7">
                        <label for="adTitle">Ad Title*</label>
                        <input type="text" name="adTitlemytit" id="adTitle" required maxlength="200" />
                        <div class="char-counter">0/200</div>
                        <div class="invalid-feedback">Title is required (max 200 characters).</div>
                    </div>

                          <!-- Asking Price -->
                    <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7">
                        <label for="adPrice">Asking price*</label>
                        <input type="number" name="askingPriceforad" id="adPrice" placeholder="Enter 0 if free" step="0.01" min="0" required/>
                        <div class="invalid-feedback">Please enter a valid price (e.g., 150.50).</div>
                    </div>

                        <!-- Description -->
                    <div class="col-lg-12 col-sm-12 d-flex flex-column mb-7">
                        <label for="descriptions">Description*</label>
                        <textarea name="descriptionforad" id="descriptions" rows="8" required maxlength="2000"></textarea>
                        <div class="char-counter">0/2000</div>
                        <div class="invalid-feedback">Description is required (max 2000 characters).</div>
                    </div>

                          <!-- User Details (Pre-filled) -->
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

                        <!-- Postal Code -->
                    <div class="col-lg-4 col-sm-12 d-flex flex-column mb-7">
                        <label for="adpostalcode">Postal code*</label>
                        <input type="text" name="adpostalCodeuser" id="adpostalcode" required pattern="\d{6}" maxlength="6" />
                        <div class="invalid-feedback">Must be a 6-digit postal code.</div>
                    </div>

                          <!-- Expires In -->
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

                   <!-- Captcha -->
<div class="col-lg-4 col-sm-12 d-flex flex-column mb-7">
    <label for="pleaseTypeThisCode"><?php echo $_SESSION['captcha_question'] ?? 'Please solve the math problem.'; ?>*</label>
    <input type="number" name="adtypecode" id="pleaseTypeThisCode" required />
    <div class="invalid-feedback">Please answer the question.</div>
</div>

                        <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7 file-upload-main">
        <label for="imageaduser">Image</label>
        <input type="file" name="picture" id="imageaduser" class="file-uploads" accept="image/*" />
        <div class="upload-image-placeholder-area text-center">
            <img src="<?php echo $base_url; ?>assets/images/upload-place.png" alt="" class="img-fluid" />
            <p class="poppins-medium">
                Drag and drop an image, or
                <span class="color-pink">Browse</span>
            </p>
        </div>
    </div>

                        <!-- Preview Area -->
    <div class="col-lg-6 col-sm-12 d-flex flex-column mb-7 file-upload-main">
        <label>Preview Images</label>
        <div id="preview-images" class="d-flex flex-wrap gap-3"></div>
    </div>

                       <!-- Platform/Link Section (Now Repeatable) -->
                    <div id="platform-container">
                        <!-- Initial Row -->
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
                                <button type="button" class="btn btn-danger remove-platform-btn" style="display:none;">X</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-sm-12 d-flex flex-column mb-12">
                        <a href="#" id="add-more-platforms" class="color-pink poppins-medium">Add More Links</a>
                    </div>

                        <div class="col-lg-12 col-sm-12 mb-7 text-center text-md-start">
                            <!-- <a href="#" name="btn_save" class="theme-btn w-100 text-decoration-none">Post This Ad</a> -->
                            <button type="submit" name="btn_save" id="btn_save" class="theme-btn">Post This Ad</button>
                        </div>
                    </div>
                    <!-- form row -->
                </form>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- Include jQuery ONCE at the start -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- UNIFIED SCRIPT BLOCK FOR ALL PAGE FUNCTIONALITY -->
<script>
$(document).ready(function() {
    // --- Setup ---
    const adForm = $('form'); // Target the main form

    // --- Live Validation & Feedback ---
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

    // --- Character Counters ---
    function setupCounter(inputId, counterClass, maxLength) {
        const input = $(inputId);
        const counter = input.parent().find(counterClass);
        input.on('input', function() {
            counter.text($(this).val().length + '/' + maxLength);
        });
    }
    setupCounter('#adTitle', '.char-counter', 200);
    setupCounter('#descriptions', '.char-counter', 2000);

    // --- Image Preview ---
    $('#imageaduser').on('change', function (e) {
        const previewContainer = $('#preview-images');
        previewContainer.html(''); // Clear previous previews
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
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
    // Remove preview image button
    $('#preview-images').on('click', '.remove-preview-btn', function() {
        $(this).parent().remove();
        $('#imageaduser').val('');
    });

    // --- Dynamic Subcategories ---
    $('#categoryOfads').on('change', function() {
        var categoryId = $(this).val();
        if (categoryId) {
            $.ajax({
                url: 'get_subcategories.php',
                type: 'POST',
                data: { category_id: categoryId },
                success: function(response) {
                    $('#subcategoryOfads').html(response);
                }
            });
        } else {
            $('#subcategoryOfads').html('<option value="">Select Subcategory</option>');
        }
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

    // --- Final Form Submission Validation ---
    adForm.on('submit', function(e) {
        let isFormValid = true;
        // Check all required fields
        adForm.find('input[required], select[required], textarea[required]').each(function() {
            if (!validateField($(this))) {
                isFormValid = false;
            }
        });
        
        // Also validate non-required fields that have a pattern
        adForm.find('input[pattern]').each(function() {
             if ($(this).val() !== '' && !validateField($(this))) {
                isFormValid = false;
            }
        });

        if (!isFormValid) {
            e.preventDefault(); // Stop submission
            alert('Please fix the errors highlighted on the form.');
            $('.is-invalid').first().focus();
        }
    });

    // --- Success Popup Logic (from your code) ---
    const successPopup = document.getElementById('successPopup');
    if (successPopup && successPopup.style.display !== 'none') {
        setTimeout(function () {
            window.location.reload();
        }, 2000);
    }
});
</script>

<?php include_once('partials/footer.php'); ?>

<!-- form section -->
<!-- form section -->

<!-- footer -->
<!-- footer -->
<?php
 include_once('partials/footer.php');
 ?>
<!-- footer -->
<!-- footer -->

