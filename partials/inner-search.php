<?php
    
$prefix = $prefix ?? 'header';
?>
    <div class="flex items-center main-search-con px-3  max-h-[40px] position-relative">
    <select name="cat" id="<?= $prefix ?>-category-select" class="posts-search">
        <option value="all">All Categories</option>
        <?php
            
            
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
                    <img src="<?php echo $base_url; ?>assets/images/microphone.svg" alt="Voice Search" style="cursor:pointer;">
    </div>

            <div id="<?= $prefix ?>-search-results" class="search-results-box d-none">
                </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
        
    const prefix = '<?= $prefix ?>';
    
    const searchIcon = document.getElementById(prefix + '-search-icon');
    const searchInput = document.getElementById(prefix + '-search-input');
    const categorySelect = document.getElementById(prefix + '-category-select');
    const searchResultsBox = document.getElementById(prefix + '-search-results');

    let debounceTimer;

        
    function performFullSearch() {
        const query = searchInput.value.trim();
        const category = categorySelect.value;
        
        if (query) {
            window.location.href = `<?php echo $base_url; ?>search-results?q=${encodeURIComponent(query)}&cat=${encodeURIComponent(category)}`;
        }
    }

        
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

        

        
    if (searchIcon) {
        searchIcon.addEventListener('click', performFullSearch);
    }
    if (searchInput) {
        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                performFullSearch();
            }
        });

            
        searchInput.addEventListener('keyup', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(performLiveSearch, 300);     
        });
    }

        
    document.addEventListener('click', function(event) {
        if (!searchResultsBox.contains(event.target) && !searchInput.contains(event.target)) {
            searchResultsBox.classList.add('d-none');
        }
    });
});
</script>
