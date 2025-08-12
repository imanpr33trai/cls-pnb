<footer class="sm:p-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center text-md-start">
                <img src="<?php echo $base_url; ?>assets/images/pnb-logo-full.svg" alt="" class="img-fluid">
            </div>
            <div class="col-12 d-flex flex-md-row flex-column pt-3 gap-5">
                <div class="col-lg-7 footer-links">

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




<script src="<?php echo $base_url; ?>assets/js/script.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>



<script>
    $(document).ready(function() {

        const searchInput = $('#header-search-input');
        const categorySelect = $('#header-category-select');
        const resultsBox = $('#header-search-results');
        let searchTimeout;

        function fetchSearchResults() {
            const query = searchInput.val().trim();
            const category = categorySelect.val();

            if (query.length < 2) {
                resultsBox.addClass('d-none').html('');
                return;
            }

            $.ajax({
                url: '/ajax/search.php',
                method: 'GET',
                data: {
                    q: query,
                    cat: category
                },
                success: function(response) {
                    resultsBox.html(response);
                    resultsBox.removeClass('d-none');
                },
                error: function() {
                    resultsBox.html("<div class='text-danger text-center p-2'>Search failed.</div>");
                    resultsBox.removeClass('d-none');
                }
            });
        }


        searchInput.on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(fetchSearchResults, 300);
        });

        categorySelect.on('change', function() {
            if (searchInput.val().trim().length >= 2) {
                fetchSearchResults();
            }
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#header-search-input, #header-search-results').length) {
                resultsBox.addClass('d-none');
            }
        });

    });
</script>

<script>
    $(document).ready(function() {
        const phoneInputField = document.querySelector("#phonenumbid");
        const countryHiddenField = $("#country_name_hidden");

        const phoneInput = window.intlTelInput(phoneInputField, {
            initialCountry: "auto",
            geoIpLookup: callback => {
                $.get("https://ipapi.co/json", function() {}).always(function(resp) {
                    const countryCode = (resp && resp.country_code) ? resp.country_code : "us";
                    callback(countryCode);
                });
            },
            separateDialCode: true,
        });

        function updateCountryName() {
            const countryData = phoneInput.getSelectedCountryData();
            countryHiddenField.val(countryData.name);
        }

        updateCountryName();

        phoneInputField.addEventListener('countrychange', updateCountryName);
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.querySelector(".search-posts-input-cont input");
        const categorySelect = document.getElementById("cats");
        const resultBox = document.getElementById("search-results");

        if (input && categorySelect && resultBox) {
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

            document.addEventListener("click", function(e) {
                if (!resultBox.contains(e.target) && !input.contains(e.target)) {
                    resultBox.classList.add("d-none");
                }
            });
        }
    });
</script>





<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchBtn = document.getElementById('search-btn');
        if (searchBtn) {
            const keywordInput = document.getElementById('keyword');
            const locationInput = document.getElementById('location');
            const overlay = document.getElementById('overlay');
            const loader = document.getElementById('loader');
            const resultsContainer = document.getElementById('search-results');

            searchBtn.addEventListener('click', function(e) {
                e.preventDefault();

                const keyword = keywordInput.value.trim();
                const location = locationInput.value.trim();

                if (!keyword && !location) {
                    alert('Please enter a keyword or location to search.');
                    return;
                }

                overlay.style.display = 'block';
                loader.style.display = 'block';
                resultsContainer.innerHTML = '';

                const formData = new FormData();
                formData.append('keyword', keyword);
                formData.append('location', location);

                const fetchUrl = `<?php echo $base_url; ?>ajax/search-handler.php`;

                fetch(fetchUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Network Error: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        loader.style.display = 'none';

                        if (data && Array.isArray(data.results) && data.results.length > 0) {
                            const resultsHtml = data.results.map(ad => {
                                const adUrl = `<?php echo $base_url; ?>ads/${ad.ad_slug}`;
                                const description = ad.description ? ad.description.substring(0, 100) + '...' : 'No description available.';
                                const adLocation = [ad.city_town_neighbourhood, ad.postal_code].filter(Boolean).join(', ');

                                return `
                            <div class="search-result-item" style="padding: 10px; border-bottom: 1px solid #eee;">
                                <a href="${adUrl}" class="text-decoration-none" style="color: #333;">
                                    <h5 style="margin-bottom: 5px;">${ad.ad_title}</h5>
                                    <p style="margin-bottom: 5px; font-size: 14px;">${description}</p>
                                    <small style="color: #777;">${adLocation}</small>
                                </a>
                            </div>`;
                            }).join('');
                            resultsContainer.innerHTML = resultsHtml;
                        } else {
                            resultsContainer.innerHTML = `<p style="padding: 20px; text-align: center;">No matching results found.</p>`;
                        }
                    })
                    .catch(error => {
                        console.error('Search Error:', error);
                        loader.style.display = 'none';
                        resultsContainer.innerHTML = `<p class="text-danger" style="padding: 20px; text-align: center;">An error occurred while fetching results.</p>`;
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

        $('#inputPassword5').on('input', function() {
            const password = $(this).val();
            const strength = zxcvbn(password);
            const colors = ['red', 'orange', '#ffcc00', '#2ecc71'];
            const labels = ['Weak', 'Medium', 'Good', 'Strong'];
            $('.password-strength').text(labels[strength.score]).css('color', colors[strength.score]);
        });

        $('.hide a').on('click', function(e) {
            e.preventDefault();
            const input = $('#inputPassword5');
            const type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
            $(this).find('h6').text(type === 'password' ? 'Hide' : 'Show');
        });

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

            clearError(this);
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

            $('#registerForm input').trigger('blur');
            if ($('.invalid').length > 0) {
                valid = false;
                $('.invalid:first').focus();
            }

            if (valid) {
                this.submit();
            }
        });
    });
</script>


<script>
    $(document).ready(function() {
        if ($('#loginForm').length) {

            const loginForm = $('#loginForm');
            const emailField = $('#loginEmail');
            const passwordField = $('#inputPassword6');
            const acceptCheckbox = $('#accept-login');
            const checkboxError = $('#checkbox-error');

            function showError(input, message) {
                const $input = $(input);
                $input.addClass("is-invalid").removeClass("is-valid");
                $input.next(".invalid-feedback").remove();
                $input.after(`<div class="invalid-feedback">${message}</div>`);
            }

            function clearError(input) {
                const $input = $(input);
                $input.removeClass("is-invalid");
                $input.next(".invalid-feedback").remove();
            }

            function showSuccess(input) {
                const $input = $(input);
                $input.removeClass("is-invalid").addClass("is-valid");
                $input.next(".invalid-feedback").remove();
            }

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

            loginForm.on('submit', function(e) {
                let isFormValid = true;

                if (emailField.val() === '' || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.val())) {
                    isFormValid = false;
                    showError(emailField, 'A valid email is required.');
                } else {
                    showSuccess(emailField);
                }

                if (passwordField.val() === '') {
                    isFormValid = false;
                    showError(passwordField, 'Password is required.');
                } else {
                    showSuccess(passwordField);
                }

                if (!acceptCheckbox.is(':checked')) {
                    isFormValid = false;
                    checkboxError.text('You must agree to the terms to log in.');
                } else {
                    checkboxError.text('');
                }

                if (!isFormValid) {
                    e.preventDefault();
                }
            });
        }
    });
</script>





<script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchBtn = document.getElementById('search-btn');
        if (searchBtn) {
            const keywordInput = document.getElementById('keyword');
            const locationInput = document.getElementById('location');
            const overlay = document.getElementById('overlay');
            const loader = document.getElementById('loader');
            const resultsContainer = document.getElementById('search-results');

            searchBtn.addEventListener('click', function(e) {
                e.preventDefault();

                const keyword = keywordInput.value.trim();
                const location = locationInput.value.trim();

                if (!keyword && !location) {
                    alert('Please enter a keyword or location to search.');
                    return;
                }

                overlay.style.display = 'block';
                loader.style.display = 'block';
                resultsContainer.innerHTML = '';

                const formData = new FormData();
                formData.append('keyword', keyword);
                formData.append('location', location);

                const fetchUrl = `<?php echo $base_url; ?>ajax/search-handler.php`;

                fetch(fetchUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Network Error: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        loader.style.display = 'none';

                        if (data && Array.isArray(data.results) && data.results.length > 0) {
                            const resultsHtml = data.results.map(ad => {
                                const adUrl = `<?php echo $base_url; ?>ads/${ad.ad_slug}`;
                                const description = ad.description ? ad.description.substring(0, 100) + '...' : '';
                                return `
                            <div class="search-result-item">
                                <a href="${adUrl}" class="text-decoration-none">
                                    <h5>${ad.ad_title}</h5>
                                    <p>${description}</p>
                                    <small>${ad.city_town_neighbourhood}</small>
                                </a>
                            </div>`;
                            }).join('');
                            resultsContainer.innerHTML = resultsHtml;
                        } else {
                            resultsContainer.innerHTML = `<p>No matching results found.</p>`;
                        }
                    })
                    .catch(error => {
                        console.error('Search Error:', error);
                        loader.style.display = 'none';
                        resultsContainer.innerHTML = `<p class="text-danger">An error occurred while fetching results.</p>`;
                    });
            });
        }
        const phoneInput = document.getElementById('phonenumbid');
        if (phoneInput && typeof window.intlTelInput === 'function') {
            const iti = window.intlTelInput(phoneInput, {
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.16/js/utils.js",
                initialCountry: "auto",
                geoIpLookup: function(callback) {
                    fetch("https://ipapi.co/json")
                        .then(res => res.json())
                        .then(data => callback(data.country_code))
                        .catch(() => callback("us"));
                }
            });

            const registerForm = document.getElementById('registerForm');
            if (registerForm) {
                registerForm.addEventListener('submit', function() {
                    const fullNumber = iti.getNumber();

                    const countryData = iti.getSelectedCountryData();
                    const countryNameInput = document.getElementById('country_name_hidden');
                    if (countryNameInput) {
                        countryNameInput.value = countryData.name;
                    }
                });
            }
        }
    });
</script>



</body>

</html>