<?php
// Default prefix to 'header' if not provided.
$prefix = $prefix ?? 'header';
?>
<!-- This component should be included once in your site header -->
<div class="flex items-center main-search-con px-3  max-h-[40px] position-relative">
    <select name="cat" id="<?= $prefix ?>-category-select" class="posts-search">
        <option value="all">All Categories</option>
        <?php
        // This part is fine, but ensure config.php is only included once per page load.
        // If your main header.php already includes it, you can remove this include.
        include_once(__DIR__ . '/../config/config.php');

        $result = $conn->query("SELECT id, name FROM ad_categories WHERE status = 'live' ORDER BY name ASC");
        while ($row = $result->fetch_assoc()):
        ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
    </select>

    <span class="line-head mx-2">|</span>

    <div class="search-posts-input-cont gap-2.5">
        <img id="<?= $prefix ?>-search-icon" src="<?php echo $base_url; ?>assets/images/search-icon.svg" alt="Search" style="cursor:pointer;">
        <input type="text" id="<?= $prefix ?>-search-input" class="2xl:w-full md:w-full" placeholder="Search for ads..." autocomplete="off">
        <!-- We will ignore the voice search JS for now to keep it simple -->
        <img src="<?php echo $base_url; ?>assets/images/microphone.svg" alt="Voice Search" style="cursor:pointer;">
    </div>

    <!-- This is the box where results will appear. It's hidden by default. -->
    <div id="<?= $prefix ?>-search-results" class="search-results-box d-none">
        <!-- AJAX results will be injected here -->
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Use the PHP prefix to create a unique variable in JS
    const prefix = '<?= $prefix ?>';
    
    const searchIcon = document.getElementById(prefix + '-search-icon');
    const searchInput = document.getElementById(prefix + '-search-input');
    const categorySelect = document.getElementById(prefix + '-category-select');
    const searchResultsBox = document.getElementById(prefix + '-search-results');

    let debounceTimer;

    // --- Function for full page search ---
    function performFullSearch() {
        const query = searchInput.value.trim();
        const category = categorySelect.value;
        
        if (query) {
            window.location.href = `<?php echo $base_url; ?>search-results?q=${encodeURIComponent(query)}&cat=${encodeURIComponent(category)}`;
        }
    }

    // --- Function for live AJAX search ---
    function performLiveSearch() {
        const query = searchInput.value.trim();
        const category = categorySelect.value;

        if (query.length < 2) {
            searchResultsBox.classList.add('d-none');
            searchResultsBox.innerHTML = '';
            return;
        }

        fetch(`<?php echo $base_url; ?>ajax/search.php?q=${encodeURIComponent(query)}&cat=${encodeURIComponent(category)}`)
            .then(response => response.text())
            .then(html => {
                searchResultsBox.innerHTML = html;
                searchResultsBox.classList.remove('d-none');
            })
            .catch(error => {
                console.error('Error fetching search results:', error);
                searchResultsBox.classList.add('d-none');
            });
    }

    // --- Event Listeners ---

    // Full search on icon click or Enter
    if (searchIcon) {
        searchIcon.addEventListener('click', performFullSearch);
    }
    if (searchInput) {
        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                performFullSearch();
            }
        });

        // Live search while typing (with debounce)
        searchInput.addEventListener('keyup', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(performLiveSearch, 300); // 300ms delay
        });
    }

    // Hide results when clicking outside
    document.addEventListener('click', function(event) {
        if (!searchResultsBox.contains(event.target) && !searchInput.contains(event.target)) {
            searchResultsBox.classList.add('d-none');
        }
    });
});
</script>
