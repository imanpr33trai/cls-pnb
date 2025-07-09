    <!-- footer -->
    <!-- footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-md-start">
                    <img src="<?php echo $base_url; ?>assets/images/pnb-logo-full.svg" alt="" class="img-fluid">
                </div>
                <div class="col-12 d-flex flex-md-row flex-column pt-3">
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
                        include "partials/footer-cat.php";
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.16/js/intlTelInput.min.js"></script>
<!-- New, separate script JUST for the phone number country dropdown -->
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
        url: 'search-handler.php',
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



<script>
  document.getElementById('search-btn').addEventListener('click', function(e) {
    e.preventDefault();

    const keyword = document.getElementById('keyword').value.trim();
    const location = document.getElementById('location').value.trim();

    if (!keyword && !location) {
      alert('Please enter keyword or location');
      return;
    }

    const overlay = document.getElementById('overlay');
    const loader = document.getElementById('loader');
    const resultsContainer = document.getElementById('results');

    overlay.style.display = 'block';
    loader.style.display = 'block';
    resultsContainer.innerHTML = '';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'search-handler.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
      loader.style.display = 'none';

      let results;
      try {
        results = JSON.parse(this.responseText);
      } catch (e) {
        resultsContainer.innerHTML = "<p>Error parsing server response.</p>";
        return;
      }

      if (results.length > 0) {
        results.forEach(function (ad) {
          const html = `
            <div class="single-result">
              <a href="single-ad.php?id=${ad.id}" class="text-decoration-none">
                <h5>${ad.ad_title}</h5>
                <p>${ad.description.substring(0, 100)}...</p>
                <small>${ad.city_town_neighbourhood}, ${ad.postal_code}</small>
              </a>
            </div>
          `;
          resultsContainer.innerHTML += html;
        });
      } else {
        resultsContainer.innerHTML = `<p>No matching results found. Try different keywords or locations.</p>`;
      }
    };

    xhr.send(`keyword=${encodeURIComponent(keyword)}&location=${encodeURIComponent(location)}`);
  });
</script>

<script>
    $(document).ready(function () {
   

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
    $('#inputPassword5').on('input', function () {
        const password = $(this).val();
        const strength = zxcvbn(password);
        const colors = ['red', 'orange', '#ffcc00', '#2ecc71'];
        const labels = ['Weak', 'Medium', 'Good', 'Strong'];
        $('.password-strength').text(labels[strength.score]).css('color', colors[strength.score]);
    });

    // Show/Hide Password
    $('.hide a').on('click', function (e) {
        e.preventDefault();
        const input = $('#inputPassword5');
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        $(this).find('h6').text(type === 'password' ? 'Hide' : 'Show');
    });

      // Show/Hide Password
    $('.hide a').on('click', function (e) {
        e.preventDefault();
        const input = $('#inputPassword6');
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        $(this).find('h6').text(type === 'password' ? 'Hide' : 'Show');
    });

    $('#registerForm input').on('input blur', function () {
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

    $('#registerForm').on('submit', function (e) {
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