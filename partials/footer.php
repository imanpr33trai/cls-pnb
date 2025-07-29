    <!-- footer -->
    <!-- footer -->
    <footer class="sm:p-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-md-start">
                    <img src="<?php echo $base_url; ?>assets/images/pnb-logo-full.svg" alt="" class="img-fluid">
                </div>
                <div class="col-12 d-flex flex-md-row flex-column pt-3 gap-5">
                    <div class="col-lg-7 footer-links">
                        <!-- <a href="#">Vehicle</a>
                        <a href="#">Townhouses</a>
                        <a href="#">Video Game</a>
                        <a href="#">Boats</a>
                        <a href="#">Campers</a>
                        <a href="#">Car</a>
                        <a href="#">Clothes</a>
                        <a href="#">All</a> -->
                        <?php
                        include_once(__DIR__ . '/footer-cat.php');
                        ?>
                    </div>
                    <div class="col-lg-5 text-white">
                        <h6 class="poppins-bold fos-14">Send me updates & offers.</h6>
                        <div class="form-newsletter text-center text-md-end">
                            <img src="<?php echo $base_url; ?>assets/images/send.svg" alt="">
                            <input type="email" name="newsletter" id="newsletter" class="newsletter">
                        </div>
                        <h6 class="poppins-regular fos-12 mt-2">Unsubscribe any time. Privacy Policy</h6>
                    </div>
                </div>
                <hr class="bg-light mt-4 mb-4">
                <div class="col-12 text-white d-flex flex-md-row">
                    <div class="col-md-6">
                        <h6 class="fos-14">© 2024 WhatNWhere. All Rights Reserved Worldwide.</h6>
                    </div>
                    <div class="col-md-6 text-end">
                        <select name="lang" id="lang">
                            <option value="1">English</option>
                            <option value="1">Franch</option>
                            <option value="1">Hindi</option>
                            <option value="1">Chinese</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer -->
    <!-- footer -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

    <script src="<?php echo $base_url; ?>assets/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.16/js/intlTelInput.min.js"></script> -->
    <!-- New, separate script JUST for the phone number country dropdown -->

    <script>
        $(document).ready(function() {

            // Get references to our HTML elements
            const searchInput = $('#header-search-input');
            const categorySelect = $('#header-category-select');
            const resultsBox = $('#header-search-results');
            let searchTimeout; // This is for debouncing, to avoid too many requests

            // --- Main function to fetch and display search results ---
            function fetchSearchResults() {
                const query = searchInput.val().trim();
                const category = categorySelect.val();

                // 1. If the search box is empty or too short, hide the results and stop
                if (query.length < 2) {
                    resultsBox.addClass('d-none').html(''); // Hide and clear
                    return;
                }

                // 2. Perform the AJAX request to our backend script
                $.ajax({
                    url: 'ajax/search.php', // This should be the path to your PHP script
                    method: 'GET',
                    data: {
                        q: query, // The search term
                        cat: category // The selected category
                    },
                    success: function(response) {
                        // 3. When we get a successful response...
                        resultsBox.html(response); // Put the HTML from PHP into our results box
                        resultsBox.removeClass('d-none'); // Make the results box visible
                    },
                    error: function() {
                        // Optional: Handle errors
                        resultsBox.html("<div class='text-danger text-center p-2'>Search failed.</div>");
                        resultsBox.removeClass('d-none');
                    }
                });
            }

            // --- Event Listeners ---

            // When a user types in the search input
            searchInput.on('keyup', function() {
                clearTimeout(searchTimeout); // Reset the timer on each keypress
                // Wait 300ms after the user stops typing, then perform the search.
                // This is called "debouncing" and is critical for performance.
                searchTimeout = setTimeout(fetchSearchResults, 300);
            });

            // When the user changes the category dropdown
            categorySelect.on('change', function() {
                // If the user has already typed something, re-run the search immediately
                if (searchInput.val().trim().length >= 2) {
                    fetchSearchResults();
                }
            });

            // Hide the results box if the user clicks anywhere else on the page
            $(document).on('click', function(e) {
                // If the click is not on the search input or the results box...
                if (!$(e.target).closest('#header-search-input, #header-search-results').length) {
                    resultsBox.addClass('d-none'); // ...hide the results.
                }
            });

        });
    </script>

    <script>
        $(document).ready(function() {
            const phoneInputField = document.querySelector("#phonenumbid");
            const countryHiddenField = $("#country_name_hidden");

            // Initialize the intl-tel-input plugin
            const phoneInput = window.intlTelInput(phoneInputField, {
                initialCountry: "auto", // Auto-detect user's country
                geoIpLookup: callback => {
                    $.get("https://ipapi.co/json", function() {}).always(function(resp) {
                        const countryCode = (resp && resp.country_code) ? resp.country_code : "us";
                        callback(countryCode);
                    });
                },
                separateDialCode: true, // Shows the country code next to the flag
                // We are NOT using utilsScript because we are not validating the number itself
            });

            // --- Function to update the hidden input with the selected country name ---
            function updateCountryName() {
                const countryData = phoneInput.getSelectedCountryData();
                countryHiddenField.val(countryData.name); // e.g., "India", "United States"
            }

            // 1. Set the initial country name on page load
            updateCountryName();

            // 2. Update the country name whenever the user selects a new country
            phoneInputField.addEventListener('countrychange', updateCountryName);
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.querySelector(".search-posts-input-cont input");
            const categorySelect = document.getElementById("cats");
            const resultBox = document.getElementById("search-results");

            input.addEventListener("input", function() {
                const query = input.value.trim();
                const categoryId = categorySelect.value;

                if (query.length < 2) {
                    resultBox.innerHTML = "";
                    resultBox.classList.add("d-none");
                    return;
                }

                fetch(`search.php?q=${encodeURIComponent(query)}&cat=${categoryId}`)
                    .then(res => res.text())
                    .then(data => {
                        resultBox.innerHTML = data;
                        resultBox.classList.remove("d-none");
                    });
            });

            // Optional: hide on click outside
            document.addEventListener("click", function(e) {
                if (!resultBox.contains(e.target) && !input.contains(e.target)) {
                    resultBox.classList.add("d-none");
                }
            });
        });
    </script>





    <script>
        $('#search-btn').on('click', function(e) {
            e.preventDefault();
            var keyword = $('#keyword').val().trim();
            var location = $('#location').val().trim();

            $('#loader').show();
            $('#search-results').empty();

            $.ajax({
                url: 'partials/search-handler.php',
                type: 'POST',
                data: {
                    keyword: keyword,
                    location: location
                },
                success: function(response) {
                    $('#loader').hide();
                    $('#search-results').html(response);
                },
                error: function() {
                    $('#loader').hide();
                    $('#search-results').html('<p>Error fetching results.</p>');
                }
            });
        });
    </script>



    // /partials/footer.php (JavaScript section)

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchBtn = document.getElementById('search-btn');
            if (searchBtn) {
                searchBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const keyword = document.getElementById('keyword').value.trim();
                    const location = document.getElementById('location').value.trim();

                    const loader = document.getElementById('search-loader');
                    const resultsContainer = document.getElementById('search-results');

                    if (!keyword && !location) {
                        resultsContainer.innerHTML = '<p class="text-warning">Please enter a keyword or location to search.</p>';
                        return;
                    }

                    // Show loader and clear previous results
                    loader.style.display = 'block';
                    resultsContainer.innerHTML = '';

                    // Create a FormData object for the request
                    const formData = new FormData();
                    formData.append('keyword', keyword);
                    formData.append('location', location);

                    // AJAX call to the correct handler path
                    fetch('/partials/search-handler.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok.');
                            }
                            return response.json(); // Expect a JSON response
                        })
                        .then(results => {
                            loader.style.display = 'none';

                            if (results.length > 0) {
                                // Use map and join for better performance than innerHTML +=
                                const resultsHtml = results.map(ad => {
                                    // Build the new clean URL
                                    const adUrl = `<?php echo $base_url; ?>${ad.category_slug}/${ad.ad_slug}`;
                                    const description = ad.description ? ad.description.substring(0, 100) + '...' : 'No description available.';

                                    return `
                            <div class="search-result-item border-bottom py-2">
                                <a href="${adUrl}" class="text-decoration-none text-dark">
                                    <h5>${ad.ad_title}</h5>
                                    <p class="mb-1">${description}</p>
                                    <small class="text-muted">${ad.city_town_neighbourhood}, ${ad.postal_code}</small>
                                </a>
                            </div>
                        `;
                                }).join('');
                                resultsContainer.innerHTML = resultsHtml;
                            } else {
                                resultsContainer.innerHTML = '<p>No matching results found. Try different keywords or locations.</p>';
                            }
                        })
                        .catch(error => {
                            console.error('Search Error:', error);
                            loader.style.display = 'none';
                            resultsContainer.innerHTML = '<p class="text-danger">An error occurred while fetching results.</p>';
                        });
                });
            }
        });
    </script>

    <script>
        $(document).ready(function() {


            function showError(input, message) {
                $(input).addClass("invalid");
                if (!$(input).next().hasClass("error")) {
                    $(input).after(`<div class="error">${message}</div>`);
                }
            }

            function clearError(input) {
                $(input).removeClass("invalid").next(".error").remove();
            }

            // Password Strength
            $('#inputPassword5').on('input', function() {
                const password = $(this).val();
                const strength = zxcvbn(password);
                const colors = ['red', 'orange', '#ffcc00', '#2ecc71'];
                const labels = ['Weak', 'Medium', 'Good', 'Strong'];
                $('.password-strength').text(labels[strength.score]).css('color', colors[strength.score]);
            });

            // Show/Hide Password
            $('.hide a').on('click', function(e) {
                e.preventDefault();
                const input = $('#inputPassword5');
                const type = input.attr('type') === 'password' ? 'text' : 'password';
                input.attr('type', type);
                $(this).find('h6').text(type === 'password' ? 'Hide' : 'Show');
            });

            // Show/Hide Password
            $('.hide a').on('click', function(e) {
                e.preventDefault();
                const input = $('#inputPassword6');
                const type = input.attr('type') === 'password' ? 'text' : 'password';
                input.attr('type', type);
                $(this).find('h6').text(type === 'password' ? 'Hide' : 'Show');
            });

            $('#registerForm input').on('input blur', function() {
                const id = $(this).attr('id');
                const val = $(this).val();

                clearError(this); // clear previous error

                if (id === 'firstnameid' && !/^[a-zA-Z]{2,}$/.test(val)) {
                    showError(this, 'First name should contain only letters.');
                }
                if (id === 'lastnameid' && !/^[a-zA-Z]{2,}$/.test(val)) {
                    showError(this, 'Last name should contain only letters.');
                }
                if (id === 'emailid' && val !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                    showError(this, 'Please enter a valid email.');
                }
                if (id === 'inputPassword5') {
                    const passCheck = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
                    if (!passCheck.test(val)) {
                        showError(this, 'Password must be 8+ chars with uppercase, lowercase, number & symbol.');
                    }
                }
            });

            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                let valid = true;

                $('#registerForm input').trigger('blur'); // trigger validation on all fields

                if ($('.invalid').length > 0) {
                    valid = false;
                    $('.invalid:first').focus();
                }

                if (valid) {
                    this.submit(); // or send via AJAX
                }
            });
        });
    </script>

    <!-- Login Page Validation Script -->
    <script>
        $(document).ready(function() {
            // Only run this script on the login page
            if ($('#loginForm').length) {

                const loginForm = $('#loginForm');
                const emailField = $('#loginEmail');
                const passwordField = $('#inputPassword6');
                const acceptCheckbox = $('#accept-login');
                const checkboxError = $('#checkbox-error');

                // Re-usable error function (similar to your register page)
                function showError(input, message) {
                    const $input = $(input);
                    $input.addClass("is-invalid").removeClass("is-valid");
                    // Add error message below the input
                    $input.next(".invalid-feedback").remove();
                    $input.after(`<div class="invalid-feedback">${message}</div>`);
                }

                // Re-usable clear error function
                function clearError(input) {
                    const $input = $(input);
                    $input.removeClass("is-invalid");
                    $input.next(".invalid-feedback").remove();
                }

                // Re-usable success function
                function showSuccess(input) {
                    const $input = $(input);
                    $input.removeClass("is-invalid").addClass("is-valid");
                    $input.next(".invalid-feedback").remove();
                }

                // --- Real-time validation as user types ---
                emailField.on('input blur', function() {
                    if (this.value === '') {
                        showError(this, 'Email address is required.');
                    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value)) {
                        showError(this, 'Please enter a valid email format.');
                    } else {
                        showSuccess(this);
                    }
                });

                passwordField.on('input blur', function() {
                    if (this.value === '') {
                        showError(this, 'Password is required.');
                    } else {
                        showSuccess(this);
                    }
                });

                // --- Validation on Form Submit ---
                loginForm.on('submit', function(e) {
                    let isFormValid = true;

                    // 1. Validate email
                    if (emailField.val() === '' || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.val())) {
                        isFormValid = false;
                        showError(emailField, 'A valid email is required.');
                    } else {
                        showSuccess(emailField);
                    }

                    // 2. Validate password
                    if (passwordField.val() === '') {
                        isFormValid = false;
                        showError(passwordField, 'Password is required.');
                    } else {
                        showSuccess(passwordField);
                    }

                    // 3. **Validate the checkbox**
                    if (!acceptCheckbox.is(':checked')) {
                        isFormValid = false;
                        checkboxError.text('You must agree to the terms to log in.');
                    } else {
                        checkboxError.text(''); // Clear error if checked
                    }

                    // If any validation fails, stop the form from submitting
                    if (!isFormValid) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>





    </body>

    </html>