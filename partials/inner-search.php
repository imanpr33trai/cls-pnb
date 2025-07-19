<!-- This component should be included once in your site header -->
<div class="d-flex align-items-center main-search-con position-relative">
    <select name="cat" id="header-category-select" class="posts-search">
        <option value="all">All Categories</option>
        <?php
        // This part is fine, but ensure config.php is only included once per page load.
        // If your main header.php already includes it, you can remove this include.
        include_once(__DIR__ . '../../config/config.php');

        $result = $conn->query("SELECT id, name FROM ad_categories WHERE status = 'live' ORDER BY name ASC");
        while ($row = $result->fetch_assoc()):
        ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
    </select>

    <span class="line-head mx-2">|</span>

    <div class="search-posts-input-cont">
        <img src="<?php echo $base_url; ?>assets/images/search-icon.svg" alt="Search">
        <input type="text" id="header-search-input" placeholder="Search for ads...">
        <!-- We will ignore the voice search JS for now to keep it simple -->
        <img src="<?php echo $base_url; ?>assets/images/microphone.svg" alt="Voice Search" style="cursor:pointer;">
    </div>

    <!-- This is the box where results will appear. It's hidden by default. -->
    <div id="header-search-results" class="search-results-box d-none">
        <!-- AJAX results will be injected here -->
    </div>
</div>