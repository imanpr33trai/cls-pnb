<div class="d-flex align-items-center main-search-con">
    <select name="cats" id="cats" class="posts-search">
        <option value="all">All Categories</option>
        <?php
        include('config/config.php');

        $result = $conn->query("SELECT id, name FROM ad_categories WHERE status = 'live' ORDER BY name ASC");
        while ($row = $result->fetch_assoc()):
            ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
    </select>

    <span class="line-head mx-2">|</span>
    <div class="search-posts-input-cont">
        <img src="<?php echo $base_url; ?>assets/images/search-icon.svg" alt="">
        <input type="text" id="voiceSearchInput" placeholder="Search">
        <img src="<?php echo $base_url; ?>assets/images/microphone.svg" alt="Mic" id="startVoiceSearchs"
            style="cursor:pointer;">
    </div>


    <div id="search-results" class="search-results-box d-none">

    </div>
</div>